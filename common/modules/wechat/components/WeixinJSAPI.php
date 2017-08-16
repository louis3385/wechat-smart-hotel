<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 1/12/15
 * Time: 2:26 PM
 */

namespace common\modules\wechat\components;

use common\modules\wechat\models\WxPlatform;
use yii\helpers\Json;

class WeixinJSAPI
{
    /**
     * @var WxPlatform
     */
    private $platform;

    /**
     * @param WxPlatform $platform
     */
    public function __construct($platform)
    {
        $this->platform = $platform;
    }

    /**
     * 格式化参数为字符串
     * @param array $params
     * @return string
     */
    public function formatParams($params)
    {
        $array = [];
        foreach ($params as $key => $val) {
            $key = strtolower($key);
            $array[] = "{$key}={$val}";
        }
        return implode('&', $array);
    }

    /**
     * 生成签名
     * @param array $params
     * @return string
     */
    public function getSign($params)
    {
        //签名步骤一：按字典序排序参数
        ksort($params);
        $string1 = $this->formatParams($params);
        //签名步骤二：SHA1加密
        $signValue = sha1($string1);
        return $signValue;
    }

    /**
     * 获取配置参数
     * @param string[] $jsApiList
     * @return array
     */
    public function getConfigParams($jsApiList)
    {
        $params = [
            'nonceStr' => Util::createNonceStr(16),
            'jsapi_ticket' => $this->platform->getValidJSAPITicket(),
            'timestamp' => time(),
            'url' => \Yii::$app->request->absoluteUrl,
        ];
        $params['signature'] = $this->getSign($params);
        $params['debug'] = false;
        $params['beta'] = true;
        $params['appId'] = $this->platform->appId;
        $params['jsApiList'] = $jsApiList;
        return $params;
    }

    /**
     * 获取配置js
     * @param string[] $jsApiList
     * @return string
     */
    public function getConfigJS($jsApiList)
    {
        $params = $this->getConfigParams($jsApiList);
        return 'wx.config(' . Json::encode($params) . '); var OPEN_ID = "' . \Yii::$app->user->id . '";';
    }

    /**
     * 获取当前平台的配置js
     * @param string[] $jsApiList
     * @return string
     */
    public static function getPlatformConfigJS($jsApiList)
    {
        $weixinJSAPI = new WeixinJSAPI(WxPlatform::getFromHost());
        return $weixinJSAPI->getConfigJS($jsApiList);
    }
} 