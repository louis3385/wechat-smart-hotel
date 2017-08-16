<?php

namespace common\modules\wechat\controllers;

use common\modules\wechat\components\AutoReplyProcessor;
use common\modules\wechat\models\WxNotification;
use common\modules\wechat\models\WxPlatform;
use common\modules\wechat\models\Device;
use yii\web\Controller;

class NotificationController extends Controller
{

    /**
     * @inheritDoc
     */
    public $enableCsrfValidation = false;

    /**
     * 绑定验证
     */
    public function actionVerify()
    {
        if (!isset($_GET['timestamp']) ||
            !isset($_GET['nonce']) ||
            !isset($_GET['signature']) ||
            !isset($_GET['echostr'])) {
            exit;
        }
        if (isset($_GET['userId'])) {
            $platform = WxPlatform::findOne(['uniqueId' => $_GET['userId']]);
        }

        if (!isset($platform)) {
            exit;
        }

        $callbackToken = $platform->callbackToken;
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $signatureTMP = array($callbackToken, $timestamp, $nonce);
        sort($signatureTMP, SORT_STRING);
        $signature = $signatureTMP[0] . $signatureTMP[1] . $signatureTMP[2];
        if ($_GET['signature'] == sha1($signature)) {
            echo $_GET['echostr'];
        }
    }

    /**
     * 消息回调接口
     */
    public function actionCallback()
    {
        if (isset($_GET['userId'])) {
            $platform = WxPlatform::findOne(['uniqueId' => $_GET['userId']]);
        }

        if (!isset($platform)) {
            exit;
        }

        $postString = $GLOBALS["HTTP_RAW_POST_DATA"];
        \Yii::info("Post data from weixin to platform $platform->name: $postString");

        if (empty($postString)) {
            exit;
        }

        $xml = (array)simplexml_load_string($postString, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml['MsgType'] == 'device_text') {
            $notification = new WxNotification();
            $notification->toUserName = (string)$xml['ToUserName'];
            $notification->fromUserName = (string)$xml['FromUserName'];
            $notification->createTime = intval($xml['CreateTime']);
            $notification->msgType = (string)$xml['MsgType'];
            $notification->deviceType = (string)$xml['DeviceType'];
            $notification->deviceId = (string)$xml['DeviceID'];
            $notification->msgId = (string)$xml['MsgID'];
            $notification->openId = (string)$xml['OpenID'];
            if (isset($xml['SessionID'])) {
                $notification->sessionId = (string)$xml['SessionID'];
            }
            $notification->save();
            $content = base64_decode($xml['Content']);

            if ($content == 'welcome WeChat Smart Hotel') {
                if ($xml['deviceType'] == $platform->uniqueId) {
                    Device::receiveDeviceHelloMsg($xml['DeviceId'], $xml['FromUserName']);
                }
            }
        }

        $autoReplyProcessor = new AutoReplyProcessor($platform);
        $reply = $autoReplyProcessor->autoReply($xml);
        if ($reply) {
            $reply['from'] = (string)$xml['ToUserName'];
            $reply['to'] = (string)$xml['FromUserName'];
            $result = $this->renderPartial('callback', ['reply' => $reply]);
            echo $result;
            \Yii::info("Output to weixin: $result");
        }
    }

    public function actionTest()
    {
        $platform = WxPlatform::findOne(['uniqueId' => 'gh_c90f968a53c8']);

        if (!isset($platform)) {
            exit;
        }

        $postString = <<<CODE_END
<xml>
<ToUserName><![CDATA[gh_c90f968a53c8]]></ToUserName>
<FromUserName><![CDATA[omqRQxD3rL7XzoP8Tzc8XRAzY9uM]]></FromUserName>
<CreateTime>1486954289</CreateTime>
<MsgType><![CDATA[device_text]]></MsgType>
<DeviceType><![CDATA[gh_c90f968a53c8]]></DeviceType>
<DeviceID><![CDATA[gh_c90f968a53c8_9f22a0549c20cc5c]]></DeviceID>
<Content><![CDATA[Hello WeChat Smart Hotel]]></Content>
<SessionID>1234567890</SessionID>
<MsgID>1234567890</MsgID>
<OpenID><![CDATA[omqRQxD3rL7XzoP8Tzc8XRAzY9uM]]></OpenID>
</xml>
CODE_END;

        $xml = (array)simplexml_load_string($postString, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml['MsgType'] == 'device_text') {
            $notification = new WxNotification();
            $notification->toUserName = (string)$xml['ToUserName'];
            $notification->fromUserName = (string)$xml['FromUserName'];
            $notification->createTime = intval($xml['CreateTime']);
            $notification->msgType = (string)$xml['MsgType'];
            $notification->deviceType = (string)$xml['DeviceType'];
            $notification->deviceId = (string)$xml['DeviceID'];
            $notification->msgId = (string)$xml['MsgID'];
            $notification->openId = (string)$xml['OpenID'];
            if (isset($xml['SessionID'])) {
                $notification->sessionId = (string)$xml['SessionID'];
            }
            $notification->save();
            $content = base64_decode($xml['Content']);

            if ($content == 'welcome WeChat Smart Hotel') {
                if ($xml['deviceType'] == $platform->uniqueId) {
                    Device::receiveDeviceHelloMsg($xml['DeviceId'], $xml['FromUserName']);
                }
            }
        }
        $autoReplyProcessor = new AutoReplyProcessor($platform);
        $reply = $autoReplyProcessor->autoReply($xml);
        if ($reply) {
            $reply['from'] = (string)$xml['ToUserName'];
            $reply['to'] = (string)$xml['FromUserName'];
            $result = $this->renderPartial('callback', ['reply' => $reply]);
            echo $result;
            \Yii::info("Output to weixin: $result");
        }
    }
} 