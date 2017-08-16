<?php

namespace common\modules\wechat\models;

use common\modules\wechat\components\WeixinService;
use yii\helpers\VarDumper;
use yii\helpers\Json;
use yii\db\ActiveRecord;

/**
 * This is the model class for collection "wx_platform".
 *
 * @property integer $_id
 * @property string $name 公众号名称
 * @property string $uniqueId 原始ID
 * @property string $number 微信号
 * @property string $callbackToken
 * @property string $appId 微信分配的公众账号ID
 * @property string $appSecret
 * @property string $mchId 微信支付分配的商户号
 * @property string $key 商户支付密钥Key
 * @property string $accessToken 获取到的凭证
 * @property int $expiresAt 凭证有效时间
 * @property string $JSAPITicket 调用微信JS接口的临时票据
 * @property int $ticketExpiresAt 临时票据有效时间
 * @property string $hmId 百度统计ID
 * @property AutoReply $autoReply 自动回复内容
 */
class WxPlatform extends ActiveRecord
{
    /**
     * @var bool 是否已强制刷新过access token
     */
    private $accessTokenRefreshed = false;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'wx_platform';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'uniqueId',
            'number',
            'callbackToken',
            'appId',
            'appSecret',
            'mchId',
            'key',
            'accessToken',
            'expiresAt',
            'JSAPITicket',
            'ticketExpiresAt',
            'hmId',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'uniqueId', 'number', 'appId', 'appSecret', 'mchId', 'key'], 'required'],
            ['hmId', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'name' => '公众号名称',
            'uniqueId' => '原始ID',
            'number' => '微信号',
            'callbackToken' => 'Token',
            'mchId' => '微信支付商户号',
            'key' => '商户支付密钥',
            'hmId' => '百度统计ID',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->callbackToken = md5(microtime());
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return WxPlatform
     */
    public static function getFromHost()
    {
        /*$hostInfo = Yii::$app->request->hostInfo;
        if (preg_match('/^http:\/\/([\w\-]+)\.\w+\.\w+$/', $hostInfo, $matches)) {
            return self::findOne(['number' => $matches[1]]);
        }
        return null;*/
        return self::find()->one();
    }

    /**
     * 获取有效的access token
     * @return string
     */
    public function getValidAccessToken()
    {
        if (!$this->accessToken || $this->expiresAt <= time()) {
            $this->refreshAccessToken();
        }
        return $this->accessToken;
    }

    /**
     * 强制刷新access token.
     */
    private function refreshAccessToken()
    {
        $result = (new WeixinService())->getAccessToken($this->appId, $this->appSecret);
        if ($result && isset($result['access_token'])) {
            $this->accessToken = $result['access_token'];
            $this->expiresAt = time() + $result['expires_in'];
            $this->save();
        } else {
            \Yii::error('Get access token failed: ' . VarDumper::dumpAsString($result));
        }
    }

    /**
     * 获取有效的jsapi ticket
     * @return string
     */
    public function getValidJSAPITicket()
    {
        if (!$this->JSAPITicket || $this->ticketExpiresAt <= time()) {
            $this->refreshJSAPITicket();
        }
        return $this->JSAPITicket;
    }

    /**
     * 强制刷新jsapi ticket.
     */
    private function refreshJSAPITicket()
    {
        $result = (new WeixinService())->getJSAPITicket($this->getValidAccessToken());
        if (isset($result['errcode']) && $result['errcode'] == 40001) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->refreshJSAPITicket();
        }
        if ($result && isset($result['ticket'])) {
            $this->JSAPITicket = $result['ticket'];
            $this->ticketExpiresAt = time() + $result['expires_in'];
            $this->save();
        } else {
            \Yii::error('Get jsapi ticket failed: ' . VarDumper::dumpAsString($result));
        }
    }

    /**
     * 获取用户基本信息
     * @param string $openId
     * @return array
     */
    public function getUserInfo($openId)
    {
        $result = (new WeixinService())->getUserInfo($this->getValidAccessToken(), $openId);
        if (isset($result['errcode']) && $result['errcode'] == 40001) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->getUserInfo($openId);
        }
        return $result;
    }


    /**
     * 获取短链接
     * @param string $longUrl
     * @return string|null
     */
    public function getShortUrl($longUrl)
    {
        $result = (new WeixinService())->getShortUrl($this->getValidAccessToken(), $longUrl);
        if ($result && isset($result['errmsg']) && ($result['errmsg'] === 'ok')) {
            return $result['short_url'];
        } else {
            return null;
        }
    }

    /**
     * 发送模板消息
     * @param string $openId
     * @param string $templateId
     * @param string $targetUrl
     * @param string $topColor
     * @param array $data
     * @return array
     */
    public function sendTmplMsg($openId, $templateId, $targetUrl, $topColor, $data)
    {
        $result = (new WeixinService())->sendTmplMsg($this->getValidAccessToken(),
            $openId, $templateId, $targetUrl, $topColor, $data);
        if (isset($result['errcode']) && $result['errcode'] == 40001) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->sendTmplMsg($openId, $templateId, $targetUrl, $topColor, $data);
        }
        if (isset($result['errcode']) && $result['errcode'] == 43004) {
            // 忽略require subscribe报错
            \Yii::warning($result['errmsg']);
            return ['errcode' => 0];
        }
        return $result;
    }

    /**
     * send device message
     * @param string $openId
     * @param string $msgId
     * @param string $msgType
     * @param string $createdTime
     * @param string $deviceId
     * @param string $sessionId
     * @param string $content
     * @return array
     */
    public function sendDeviceMsg($openId, $msgId, $msgType, $createdTime, $deviceId, $sessionId, $content)
    {
        $result = (new WeixinService())->sendDeviceMsg($this->getValidAccessToken(), $openId, $this->uniqueId, $msgId, $msgType, $createdTime, $deviceId, $sessionId, $content);
        if (isset($result['errcode']) && $result['errcode'] == 40001) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->sendDeviceMsg($openId, $msgId, $msgType, $createdTime, $deviceId, $sessionId, $content);
        }
        if (isset($result['errcode']) && $result['errcode'] == 43004) {
            // 忽略require subscribe报错
            \Yii::warning($result['errmsg']);
            return ['errcode' => 0];
        }
        return $result;
    }

    /**
     * 清除接口调用频率限制
     * @return bool
     */
    public function clearQuota()
    {
        $result = (new WeixinService())->clearQuota($this->getValidAccessToken(), $this->appId);
        if (isset($result['errcode']) && $result['errcode'] == 40001) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->clearQuota();
        }
        if ($result['errcode'] != 0) {
            \Yii::error('Clear quota error: ' . $result['errmsg']);
            return false;
        }
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAutoReply()
    {
        return $this->hasOne(AutoReply::className(), ['platformId' => '_id']);
    }

    /**
     * 同步自动回复
     * @return bool
     */
    public function syncAutoReply()
    {
        $result = (new WeixinService())->getAutoReply($this->getValidAccessToken());
        if (isset($result['errcode']) && $result['errcode'] == 40001 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->syncAutoReply();
        }
        $autoReply = $this->autoReply;
        if (!$autoReply) {
            $autoReply = new AutoReply();
            $autoReply->platformId = $this->_id;
        }
        $autoReply->content = Json::encode($result);
        return $autoReply->save();
    }

    /**
     * authorize a device (new WeChat interface)
     * @return array
     */
    public function authorizeDevice()
    {
        $productId = \Yii::$app->params['productId'];
        $result = (new WeixinService())->authorizeDevice($this->getValidAccessToken(),$productId);
        if (isset($result['ret_code']) && $result['ret_code'] == -1 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['ret_code']);
            $this->refreshAccessToken();
            return $this->authorizeDevice();
        }
        return $result;
    }

    /**
     * authorize a device by 3rd account
     * @param string $deviceNum
     * @param array $deviceList
     * @param string $opType
     * @return array
     */
    public function authorizeDevice3rd($deviceNum, $deviceList, $opType)
    {
        $result = (new WeixinService())->authorizeDevice3rd($this->getValidAccessToken(),$deviceNum, $deviceList, $opType);
        if (isset($result['errcode']) && $result['errcode'] == 42001 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->authorizeDevice3rd($deviceNum, $deviceList, $opType);
        }
        return $result;
    }

    /**
     * bind a device to an openId
     * @param string $qrcodeTicket
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function bindDeviceToOpenid($qrcodeTicket, $deviceId, $openId)
    {
        $result = (new WeixinService())->bindDeviceToOpenid($this->getValidAccessToken(),$qrcodeTicket, $deviceId, $openId);
        if (isset($result['errcode']) && $result['errcode'] == -1 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->bindDeviceToOpenid($qrcodeTicket, $deviceId, $openId);
        }
        return $result;
    }

    /**
     * unbind a device to an openId
     * @param string $qrcodeTicket
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function unbindDeviceToOpenid($qrcodeTicket, $deviceId, $openId)
    {
        $result = (new WeixinService())->unbindDeviceToOpenid($this->getValidAccessToken(),$qrcodeTicket, $deviceId, $openId);
        if (isset($result['errcode']) && $result['errcode'] == -1 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->unbindDeviceToOpenid($qrcodeTicket, $deviceId, $openId);
        }
        return $result;
    }

    /**
     * bind a device to an openId by force
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function bindDeviceToOpenidByForce($deviceId, $openId)
    {
        $result = (new WeixinService())->bindDeviceToOpenidByForce($this->getValidAccessToken(), $deviceId, $openId);
        if (isset($result['errcode']) && $result['errcode'] == -1 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->bindDeviceToOpenidByForce($deviceId, $openId);
        }
        return $result;
    }

    /**
     * unbind a device to an openId by force
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function unbindDeviceToOpenidByForce($deviceId, $openId)
    {
        $result = (new WeixinService())->unbindDeviceToOpenidByForce($this->getValidAccessToken(),$deviceId, $openId);
        if (isset($result['errcode']) && $result['errcode'] == -1 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->unbindDeviceToOpenidByForce($deviceId, $openId);
        }
        return $result;
    }

    /**
     * get device status
     * @param string $deviceId
     * @return array
     */
    public function getDeviceStatus($deviceId)
    {
        $result = (new WeixinService())->getDeviceStatus($this->getValidAccessToken(),$deviceId);
        if (isset($result['errcode']) && $result['errcode'] == 42001 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->getDeviceStatus($deviceId);
        }
        return $result;
    }

    /**
     * verify QRcode ticket
     * @param string $qrcodeTicket
     * @return array
     */
    public function verifyQrcodeTicket($qrcodeTicket)
    {
        $result = (new WeixinService())->verifyQrcodeTicket($this->getValidAccessToken(),$qrcodeTicket);
        if (isset($result['errcode']) && $result['errcode'] == 42001 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->verifyQrcodeTicket($qrcodeTicket);
        }
        return $result;
    }

    /**
     * get OpenId of a Device
     * @param string $deviceType
     * @param string $deviceId
     * @return array
     */
    public function getOpenIdByDevice($deviceType, $deviceId)
    {
        $result = (new WeixinService())->getOpenIdByDevice($this->getValidAccessToken(),$deviceType, $deviceId);
        if (isset($result['errcode']) && $result['errcode'] == -1 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['errcode']);
            $this->refreshAccessToken();
            return $this->getOpenIdByDevice($deviceType, $deviceId);
        }
        return $result;
    }

    /**
     * get Device IDs by OpenId
     * @param string $openId
     * @return array
     */
    public function getDeviceIdsByOpenId($openId)
    {
        $result = (new WeixinService())->getDeviceIdsByOpenId($this->getValidAccessToken(),$openId);
        if (isset($result['ret_code']) && $result['ret_code'] == -1 && !$this->accessTokenRefreshed) {
            \Yii::warning($result['ret_code']);
            $this->refreshAccessToken();
            return $this->getDeviceIdsByOpenId($openId);
        }
        return $result;
    }

    /**
     * send message from Device to WeChat User
     * @param string $openId
     * @param string $platformId
     * @param string $msgId
     * @param string $msgType
     * @param string $createdTime
     * @param string $deviceId
     * @param string $sessionId
     * @param string $content
     * @return array
     */
    public function sendMsgFromDeviceToUser($openId, $platformId, $msgId, $msgType, $createdTime, $deviceId, $sessionId, $content)
    {
        $result = (new WeixinService())->sendDeviceMsg($this->getValidAccessToken(),
            $openId, $platformId, $msgId, $msgType, $createdTime, $deviceId, $sessionId, $content);
        if (isset($result['errcode']) && $result['errcode'] == 40001) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->sendMsgFromDeviceToUser($openId, $platformId, $msgId, $msgType, $createdTime, $deviceId, $sessionId, $content);
        }
        if (isset($result['errcode']) && $result['errcode'] == 43004) {
            \Yii::warning($result['errmsg']);
            return ['errcode' => 0];
        }
        return $result;
    }

    /**
     * 抓取微信用户信息(已抓取则直接返回)
     * @param string $openId
     * @return WXUser|null
     */
    public function fetchWxUser($openId)
    {
        $wxUser = WxUser::findOne(['openId' => $openId]);
        if ($wxUser) {
            return $wxUser;
        }

        $wxUser = new WxUser();
        $wxUser->openId = $openId;
        $wxUser->platformId = $this->_id;
        if ($wxUser->updateUserInfo() && $wxUser->save()) {
            return $wxUser;
        }
        return null;
    }
}
