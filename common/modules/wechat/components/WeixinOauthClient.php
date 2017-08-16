<?php

namespace common\modules\wechat\components;

use common\modules\wechat\models\WxPlatform;
use common\modules\wechat\models\WxUser;
use yii\httpclient\Client;
use yii\base\Exception;
use yii\helpers\Url;
use yii;
use yii\helpers\VarDumper;

class WeixinOauthClient {

    /**
     * @var WxPlatform
     */
    private $platform;

    /**
     * @param WxPlatform $platform
     */
    public function __construct($platform) {
        $this->platform = $platform;
    }

    /**
     * 请求url并返回JSON
     * @param $url
     * @return array
     */
    private function getJSONOfUrl($url) {
        Yii::info('URL: ' . $url);
        $client = new Client();
        try {
            $response = $client->get($url)->send();
        } catch (Exception $ex) {
            Yii::error('Request failed, exception: '. VarDumper::dumpAsString($ex));
            return null;
        }
        if ($response->isOk) {
            $body = $response->data;
            Yii::info('Response: ' . VarDumper::dumpAsString($body));
            return $body;
        }
        return null;
    }

    /**
     * Composes user authorization URL.
     * @param string $callbackUrl 授权回调的URL
     * @param string $scope snsapi_base或snsapi_userinfo
     * @param string $state
     * @return string
     */
    public function buildAuthUrl($callbackUrl, $scope, $state = null) {
        $params = [
            'appid' => $this->platform->appId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ];
        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * Fetches access token from authorization code.
     * @param string $authCode authorization code
     * @param string $scope 应用授权作用域(null时只查找不新建用户)
     * @return WxUser
     */
    public function fetchAccessToken($authCode, $scope) {
        $params = [
            'appid' => $this->platform->appId,
            'secret' => $this->platform->appSecret,
            'code' => $authCode,
            'grant_type' => 'authorization_code',
        ];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($params);
        $json = $this->getJSONOfUrl($url);
        if ($json && isset($json['openid'])) {
            $openId = $json['openid'];
            // 部分终端会调用两次callback，导致生成两个WxUser
            Yii::$app->mutex->acquire(WxUser::className() . $openId, Yii::$app->params['mutexTimeout']);
            $user = WxUser::findOne(['openId' => $openId]);
            if ($scope == null) {
                return $user;
            }
            if (!$user) {
                $user = new WxUser();
                $user->platformId = $this->platform->_id;
                $user->openId = $openId;
            }
            $user->accessToken = $json['access_token'];
            $user->expiresAt = time() + $json['expires_in'];
            $user->refreshToken = $json['refresh_token'];
            $user->save();
            if ($scope == 'snsapi_userinfo') {
                $this->updateUserInfo($user);
            }
            return $user;
        }
        return null;
    }

    /**
     * Gets new auth token to replace expired one.
     * @param string $refreshToken refresh token
     */
    public function refreshAccessToken($refreshToken) {
        $params = [
            'appid' => $this->platform->appId,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];
        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?' . http_build_query($params);
        $json = $this->getJSONOfUrl($url);
        if ($json && isset($json['openid'])) {
            $user = WxUser::findOne(['openId' => $json['openid']]);
            $user->accessToken = $json['access_token'];
            $user->expiresAt = time() + $json['expires_in'];
            $user->refreshToken = $json['refresh_token'];
            $user->save();
        }
    }

    /**
     * 更新用户基本信息
     * @param WxUser $user
     */
    public function updateUserInfo($user) {
        $params = [
            'access_token' => $user->accessToken,
            'openid' => $user->openId,
        ];
        $url = 'https://api.weixin.qq.com/sns/userinfo?' . http_build_query($params);
        $json = $this->getJSONOfUrl($url);
        if ($json && isset($json['openid'])) {
            $user->nickname = $json['nickname'];
            $user->sex = $json['sex'];
            $user->city = $json['city'];
            $user->country = $json['country'];
            $user->province = $json['province'];
            $user->language = $json['language'];
            $user->headImgUrl = $json['headimgurl'];
            $user->save();
        }
    }
} 
