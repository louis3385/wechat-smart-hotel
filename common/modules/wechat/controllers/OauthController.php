<?php

namespace common\modules\wechat\controllers;

use common\modules\wechat\models\WxPlatform;
use common\modules\wechat\models\WxUser;
use common\modules\wechat\components\WeixinOauthClient;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class OauthController extends Controller
{

    /**
     * 微信OAuth登录，尽量避免弹出授权页面，过程如下：
     * 1. 先尝试通过snsapi_base授权作用域拿用户openid，再用公众平台接口拿用户信息，如果拿不到说明还没有关注；
     * 2. 再改用snsapi_userinfo授权作用域拿用户accessToken（会弹出授权页面），再通过OAuth接口拿用户信息。
     *
     * @param string $scope
     * @param string $returnUrl 登录成功后返回的url
     * @param bool $silent 是否不弹授权页面静默登录（如果用户不存在, 静默登录并不会使用户登录）
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionLogin($scope = 'snsapi_base', $returnUrl = null, $silent = false)
    {
        if ($returnUrl) {
            \Yii::$app->user->returnUrl = $returnUrl;
        }
        if (\Yii::$app->request->userIP == \Yii::$app->params['autoLoginIP1']) {
            $user = WxUser::findOne(['openId' => \Yii::$app->params['autoLoginOpenId']]);
            if ($user) {
                \Yii::$app->user->login($user, 3600 * 24 * 30);
                return $this->redirect(\Yii::$app->user->returnUrl);
            }
        }

        $oauthClient = $this->getOauthClient();
        if ($silent) {
            if (isset(\Yii::$app->params['staging'])) {
                $callbackUrl = 'http://www.touzquan.com' . Url::toRoute('oauth/callback2-staging');
            } else {
                $callbackUrl = Url::toRoute('oauth/callback2', true);
            }
            $authUrl = $oauthClient->buildAuthUrl($callbackUrl, 'snsapi_base');
        } else {
            if (isset(\Yii::$app->params['staging'])) {
                $callbackUrl = 'http://www.touzquan.com' . Url::toRoute('oauth/callback-staging');
            } else {
                $callbackUrl = Url::toRoute('oauth/callback', true);
            }
            $authUrl = $oauthClient->buildAuthUrl($callbackUrl, $scope, $scope);
        }
        return $this->redirect($authUrl);
    }

    /**
     * 微信OAuth登录回调
     * @param string $code
     * @param string $state
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function actionCallback($code = null, $state = null)
    {
        if (!$code) {
            throw new UnauthorizedHttpException('未被授权无法继续，请同意授权后重试。');
        }
        $oauthClient = $this->getOauthClient();
        $user = $oauthClient->fetchAccessToken($code, $state);
        if (!$user) {
            return;
        }
        if (!$user->nickname) { // 还没有关注，改用snsapi_userinfo的OAuth接口等用户授权后再拿用户信息
            $this->redirect(Url::toRoute(['oauth/login', 'scope' => 'snsapi_userinfo']));
            return;
        }
        \Yii::$app->user->login($user, 3600 * 24 * 30);
        $this->redirect(\Yii::$app->user->returnUrl);
    }

    /**
     * 微信OAuth静默登录回调
     * @param string $code
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function actionCallback2($code = null)
    {
        if (!$code) {
            throw new UnauthorizedHttpException('未被授权无法继续，请同意授权后重试。');
        }
        $oauthClient = $this->getOauthClient();
        $user = $oauthClient->fetchAccessToken($code, null);
        if ($user) {
            \Yii::$app->user->login($user, 3600 * 24 * 30);
        }
        $this->redirect(\Yii::$app->user->returnUrl);
    }

    /**
     * 微信OAuth登录回调, 并将参数传递给staging server
     * @param string $code
     * @param string $state
     */
    public function actionCallbackStaging($code = null, $state = null)
    {
        $callbackUrl = 'http://www.touzquan.com' . Url::to(['oauth/callback', 'code' => $code, 'state' => $state]);
        $this->redirect($callbackUrl);
    }

    /**
     * 微信OAuth静默登录回调, 并将参数传递给staging server
     * @param string $code
     * @param string $state
     */
    public function actionCallback2Staging($code = null, $state = null)
    {
        $callbackUrl = 'http://www.touzquan.com' . Url::to(['oauth/callback2', 'code' => $code, 'state' => $state]);
        $this->redirect($callbackUrl);
    }

    /**
     * 获取当前WeixinOauthClient
     * @return WeixinOauthClient
     * @throws NotFoundHttpException
     */
    private function getOauthClient()
    {
        $platform = WxPlatform::getFromHost();
        if (!$platform) {
            throw new NotFoundHttpException('Cannot find the specified platform.');
        }
        return new WeixinOauthClient($platform);
    }

} 
