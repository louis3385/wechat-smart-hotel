<?php
 

namespace common\modules\wechat\components;


use yii\httpclient\Client;
use yii\base\Exception;
use yii\helpers\VarDumper;

use common\modules\wechat\models\WxPlatform;

class WXPayService
{

    const MAX_RETRY_COUNT = 2;

    /**
     * @var WxPlatform
     */
    public $platform;

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
            if ($val != null && $val != '') { // 值为空的参数不参与签名
                $array[] = "{$key}={$val}";
            }
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
        //签名步骤二：在string后加入KEY
        $stringSignTemp = $string1 . "&key=" . $this->platform->key;
        //签名步骤三：MD5加密
        $signValue = strtoupper(md5($stringSignTemp));
        return $signValue;
    }

    /**
     * 验证签名
     * @param array $result
     * @return bool
     */
    public function checkSign($result)
    {
        if (!isset($result['sign'])) {
            return false;
        }
        $temp = $result;
        unset($temp['sign']);
        $sign = $this->getSign($temp);
        return $result['sign'] == $sign;
    }

    /**
     * array转xml
     * @param array $arr
     * @return string xml
     */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @return array
     */
    public function xmlToArray($xml)
    {
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     * 获取请求参数（包括默认的）
     * @param array $params
     * @return array
     */
    public function getPostParams($params)
    {
        $params['appid'] = $this->platform->appId;
        $params['mch_id'] = $this->platform->mchId;
        $params['nonce_str'] = Util::createNonceStr();
        $params['sign'] = $this->getSign($params);
        return $params;
    }

    /**
     * 获取请求参数（包括默认的）
     * @param array $params
     * @return array
     */
    public function getPostParams2($params)
    {
        $params['mch_appid'] = $this->platform->appId;
        $params['mchid'] = $this->platform->mchId;
        $params['nonce_str'] = Util::createNonceStr();
        $params['sign'] = $this->getSign($params);
        return $params;
    }

    /**
     * 发送POST请求
     * @param string $url
     * @param array $params
     * @param bool $secure 是否使用商户证书
     * @param int $retryCount 已重试次数
     * @return array|null
     */
    public function postToUrl($url, $params, $secure, $retryCount = 0)
    {
        $xml = $this->arrayToXml($params);
        \Yii::info('Post to: ' . $url . ' with body: ' . $xml);
        $client = new Client();
        $client->setTransport('yii\httpclient\CurlTransport');
        $request = $client->createRequest()
                   ->setFormat(Client::FORMAT_XML)
                   ->setHeaders(['Content-Type' => 'text/xml'])
                   ->setMethod('POST')
                   ->setUrl($url)
                   ->setContent($xml);
        if ($secure) {
            $request->setOptions(['SSLCERT' => \Yii::getAlias('@common/cert/all.pem')]);
        }
        try {
            $response = $request->send();
        } catch (Exception $ex) {
            if ($retryCount < self::MAX_RETRY_COUNT) {
                \Yii::warning('WXPay request failed, retry count ' . $retryCount .': '. VarDumper::dumpAsString($ex));
                sleep($retryCount + 1);
                return $this->postToUrl($url, $params, $secure, $retryCount + 1);
            } else {
                \Yii::error('WXPay request failed, exceed max retry count: '. VarDumper::dumpAsString($ex));
                return null;
            }
        }

        if ($response->isOk) {
            $body = $response->content;
            \Yii::info('Response: ' . VarDumper::dumpAsString($body));
            $array = $this->xmlToArray($body);
            if ($array['return_code'] == 'FAIL') {
                if ($retryCount < self::MAX_RETRY_COUNT) {
                    \Yii::warning('WXPay communication failed, retry count ' . $retryCount .': ' .
                        empty($array['return_msg']) ? '' : $array['return_msg']);
                    $multiplier = $array['return_msg'] == '你的操作过于频繁，请稍后再试' ? 5 : 1;
                    sleep(($retryCount + 1) * $multiplier);
                    return $this->postToUrl($url, $params, $secure, $retryCount + 1);
                } else {
                    \Yii::error('WXPay communication failed, exceed max retry count: ' . $array['return_msg']);
                    return null;
                }
            }
            if ($array['result_code'] == 'FAIL') {
                if ($retryCount < self::MAX_RETRY_COUNT) {
                    \Yii::warning('WXPay business failed, retry count ' . $retryCount .
                        ', error code: ' . (isset($array['err_code']) ? $array['err_code'] : '') .
                        ', error message: ' . (isset($array['err_code_des']) ? $array['err_code_des'] : ''));
                    sleep($retryCount + 1);
                    return $this->postToUrl($url, $params, $secure, $retryCount + 1);
                } else {
                    \Yii::error('WXPay business failed, exceed max retry count' .
                        ', error code: ' . (isset($array['err_code']) ? $array['err_code'] : '') .
                        ', error message: ' . (isset($array['err_code_des']) ? $array['err_code_des'] : ''));
                    return null;
                }
            }
            return $array;
        }
        return null;
    }

    /**
     * 下预支付订单
     * @param string $openId 用户ID
     * @param float $amount 总金额（单位：元）
     * @param string $productDescription 产品描述
     * @param string $outTradeNO 商户订单号
     * @param string $notifyUrl 通知接口URL
     * @return string|null 预支付订单号
     */
    public function unifiedOrder($openId, $amount, $productDescription, $outTradeNO, $notifyUrl)
    {
        $params = $this->getPostParams([
            'openid' => $openId,
            'body' => $productDescription,
            'out_trade_no' => $outTradeNO,
            'total_fee' => (int)($amount * 100), // 单位：分
            'notify_url' => $notifyUrl,
            'trade_type' => 'JSAPI',
            'spbill_create_ip' => \Yii::$app->request->userIP, //终端ip
        ]);
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->postToUrl($url, $params, false);
        if ($result && isset($result['prepay_id'])) {
            return $result['prepay_id'];
        }
        return null;
    }

    /**
     * 订单查询接口
     * @param string $outTradeNO
     * @return array|null
     */
    public function queryOrder($outTradeNO)
    {
        $params = $this->getPostParams(['out_trade_no' => $outTradeNO]);
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        $result = $this->postToUrl($url, $params, false);
        return $result;
    }

    /**
     * 企业付款
     * @param string $partnerTradeNO 商户订单号，需保持唯一性
     * @param string $openId 商户appid下，某用户的openid
     * @param int $amount 企业付款金额，单位为分
     * @param string $desc 企业付款操作说明信息
     * @return array|null
     */
    public function transfer($partnerTradeNO, $openId, $amount, $desc)
    {
        $params = $this->getPostParams2([
            'partner_trade_no' => $partnerTradeNO,
            'openid' => $openId,
            'check_name' => 'NO_CHECK',
            'amount' => $amount,
            'desc' => $desc,
            //'spbill_create_ip' => \Yii::$app->request->userIP, //终端ip
            'spbill_create_ip' => '139.196.50.32',
        ]);
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $result = $this->postToUrl($url, $params, true);
        if ($result && $result['payment_no'] && $result['payment_time']) {
            return [$result['payment_no'], $result['payment_time']];
        }
        return null;
    }

    /**
     * 退款
     * @param string $outTradeNO 商户订单号
     * @param string $outRefundNO 商户退款单号(商户系统内部的退款单号，商户系统内部唯一，同一退款单号多次请求只退一笔)
     * @param int $totalFee 订单总金额，单位为分
     * @param int $refundFee 退款总金额，订单总金额
     * @param string $opUserId 操作员帐号, 默认为商户号
     * @return array|null
     */
    public function refund($outTradeNO, $outRefundNO, $totalFee, $refundFee, $opUserId)
    {
        if (YII_ENV == 'test') {
            return [
                'transaction_id' => uniqid(),
                'refund_id' => uniqid(),
                'total_fee' => $totalFee,
                'refund_fee' => $refundFee,
                'cash_fee' => $totalFee,
            ];
        }
        $params = $this->getPostParams([
            'out_trade_no' => $outTradeNO,
            'out_refund_no' => $outRefundNO,
            'total_fee' => $totalFee,
            'refund_fee' => $refundFee,
            'op_user_id' => $opUserId,
        ]);
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $result = $this->postToUrl($url, $params, true);
        if ($result && isset($result['refund_id'])) {
            return $result;
        }
        return null;
    }
} 
