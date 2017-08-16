<?php

namespace common\modules\wechat\components;

use common\modules\wechat\models\WxPlatform;
use yii\httpclient\Client;
use yii\base\Exception;
use yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class WeixinService
{
    const MAX_RETRY_COUNT = 2;

    /**
     * 请求url并返回JSON
     * @param string $url
     * @param array  $data
     * @param int $retryCount 已重试次数
     * @return array
     */
    private function getJSONOfUrl($url, $data = null, $retryCount = 0)
    {
        Yii::info('Request: ' . $url);
        $client = new Client();
        $request = $client->createRequest()->setUrl($url);
        if ($data) {
            $request->setMethod('POST');
            $request->setFormat(Client::FORMAT_XML);
            $request->setHeaders(['Content-Type' => 'application/json']);
            Yii::info('Post data: ' . VarDumper::dumpAsString($data));
            if (is_string($data)) {
                $request->setContent($data);
            } else {
                $request->setData($data);
            }
        }
        try {
            $response = $request->send();
        } catch (Exception $ex) {
            if ($retryCount < self::MAX_RETRY_COUNT) {
                Yii::warning('Request failed, retry count ' . $retryCount . ': ' . VarDumper::dumpAsString($ex));
                return self::getJSONOfUrl($url, $data, $retryCount + 1);
            } else {
                Yii::error('Request failed, exceed max retry count: ' . VarDumper::dumpAsString($ex));
                return null;
            }
        }

        if ($response->isOk) {
            $data = $response->data;
            Yii::info('Response: ' . VarDumper::dumpAsString($data));
            return $data;
        }
        return null;
    }

    /**
     * 获取公众号的access_token
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public function getAccessToken($appId, $appSecret)
    {
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $appId,
            'secret' => $appSecret,
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/token?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * 获取jsapi_ticket
     * @param string $accessToken
     * @return array
     */
    public function getJSAPITicket($accessToken)
    {
        $params = [
            'access_token' => $accessToken,
            'type' => 'jsapi',
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * 获取用户基本信息
     * @param string $accessToken
     * @param string $openId
     * @return array
     */
    public function getUserInfo($accessToken, $openId)
    {
        $params = [
            'access_token' => $accessToken,
            'openid' => $openId,
            'lang' => 'zh_CN',
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * 根据长链接获取转化的短链接
     * @param string $accessToken
     * @param string $longUrl
     * @return array
     */
    public function getShortUrl($accessToken, $longUrl){
        $params = [
            'long_url' => $longUrl,
            'action' => 'long2short',
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $accessToken;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * 发送模板消息
     * @param string $accessToken
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
    public function sendDeviceMsg($accessToken, $openId, $platformId, $msgId, $msgType, $createdTime, $deviceId, $sessionId, $content)
    {
        $params = [
            'open_id' => $openId,
            'device_id ' => $deviceId,
            'device_type ' => $platformId,
            'msg_id' => $msgId,
            'msg_type' => $msgType,
            'create_time' => $createdTime,
            'session_id' => $sessionId,
            'content' => $content
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * 发送模板消息
     * @param string $accessToken
     * @param string $openId
     * @param string $templateId
     * @param string $targetUrl
     * @param string $topColor
     * @param array $data
     * @return array
     */
    public function sendTmplMsg($accessToken, $openId, $templateId, $targetUrl, $topColor, $data)
    {
        $params = [
            'touser' => $openId,
            'template_id' => $templateId,
            'url' => $targetUrl,
            'topcolor' => $topColor,
            'data' => $data,
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }


    /**
     * 清除接口调用频率限制
     * @param $accessToken
     * @param $appId
     * @return array
     */
    public function clearQuota($accessToken, $appId)
    {
        $params = [
            'appid' => $appId,
        ];
        $url = 'https://api.weixin.qq.com/cgi-bin/clear_quota?access_token=' . $accessToken;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * get Auto Reply message 
     * @param $accessToken
     * @return array
     */
    public function getAutoReply($accessToken)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=' . $accessToken;
        return $this->getJSONOfUrl($url);
    }

    /**
     * authorize a device, which will return a Device ID and a QRCode ticket
     * @param string $access_token
     * @param string $productId
     * @return array
     */
    public function authorizeDevice($access_token, $productId)
    {
        $params = [
            'access_token' => $access_token,
            'product_id' => $productId,
        ];
        $url = 'https://api.weixin.qq.com/device/getqrcode?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * create QRCode ticket for Device ID by 3rd account
     * @param string $access_token
     * @param string $deviceNum
     * @param array $deviceList
     * @param string $opType
     * @return array
     */
    public function authorizeDevice3rd($access_token, $deviceNum, $deviceList, $opType)
    {
        $params = [
            'device_num' => $deviceNum,
            'device_list' => $deviceList,
            'op_type' => $opType
        ];

        $url = "https://api.weixin.qq.com/device/authorize_device?access_token=" . $access_token;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * bind a device to an openId
     * @param string $access_token
     * @param string $qrcodeTicket
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function bindDeviceToOpenid($access_token, $qrcodeTicket, $deviceId, $openId)
    {
        $params = [
            'ticket' => $qrcodeTicket,
            'device_id' => $deviceId,
            'openid' => $openId
        ];

        $url = "https://api.weixin.qq.com/device/bind?access_token=" . $access_token;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * unbind a device to an openId
     * @param string $access_token
     * @param string $qrcodeTicket
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function unbindDeviceToOpenid($access_token, $qrcodeTicket, $deviceId, $openId)
    {
        $params = [
            'ticket' => $qrcodeTicket,
            'device_id' => $deviceId,
            'openid' => $openId
        ];

        $url = "https://api.weixin.qq.com/device/unbind?access_token=" . $access_token;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * bind a device to an openId by force
     * @param string $access_token
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function bindDeviceToOpenidByForce($access_token, $deviceId, $openId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openId
        ];

        $url = "https://api.weixin.qq.com/device/compel_bind?access_token=" . $access_token;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * unbind a device to an openId by force
     * @param string $access_token
     * @param string $deviceId
     * @param string $openId
     * @return array
     */
    public function unbindDeviceToOpenidByForce($access_token, $deviceId, $openId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openId
        ];

        $url = "https://api.weixin.qq.com/device/compel_unbind?access_token=" . $access_token;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }


    /**
     * get Device status
     * @param string $access_token
     * @param string $deviceId
     * @return array
     */
    public function getDeviceStatus($access_token, $deviceId)
    {
        $params = [
            'access_token' => $access_token,
            'device_id' => $deviceId,
        ];
        $url = 'https://api.weixin.qq.com/device/getqrcode?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * verify QRcode ticket
     * @param string $qrcodeTicket
     * @return array
     */
    public function verifyQrcodeTicket($access_token, $qrcodeTicket)
    {
        $params = [
            'ticket' => $qrcodeTicket,
        ];
        $url = "https://api.weixin.qq.com/device/verify_qrcode?access_token=" . $access_token;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * get OpenId of a Device
     * @param string $access_token
     * @param string $deviceType
     * @param string $deviceId
     * @return array
     */
    public function getOpenIdByDevice($access_token, $deviceType, $deviceId)
    {
        $params = [
            'access_token' => $access_token,
            'device_type' => $deviceType,
            'device_id' => $deviceId,
        ];
        $url = 'https://api.weixin.qq.com/device/get_openid?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * get Device IDs by OpenId
     * @param string $access_token
     * @param string $openId
     * @return array
     */
    public function getDeviceIdsByOpenId($access_token, $openId)
    {
        $params = [
            'access_token' => $access_token,
            'openid' => $openId,
        ];
        $url = "https://api.weixin.qq.com/device/get_bind_device?access_token=" . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }
}
