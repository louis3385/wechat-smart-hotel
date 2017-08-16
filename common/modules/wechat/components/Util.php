<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/15/14
 * Time: 12:38 PM
 */

namespace common\modules\wechat\components;

use yii\httpclient\Client;
use yii\base\Exception;

class Util
{
    /**
     * 判断浏览器是否支持微信支付
     * @param string $userAgent
     * @return bool
     */
    public static function canWXPay($userAgent)
    {
        if (!$userAgent) {
            return false;
        }
        if (preg_match('#MicroMessenger/([\d\.]+)#', $userAgent, $matches)) {
            $version = floatval($matches[1]);
            return $version >= 5.0;
        }
        return true; // 没有版本信息可用，当作支持（有的浏览器不传）。
    }

    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     */
    public static function createNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 按照月和日格式化时间
     * @param int $datetime
     * @return string
     */
    public static function formatterAsDate($datetime)
    {
        return \Yii::$app->formatter->asDate($datetime,'MM月dd日');
    }

    /**
     * 按照年月日格式化时间
     * @param int $datetime
     * @return string
     */
    public static function formatterAsYearMonthDate($datetime)
    {
        return \Yii::$app->formatter->asDate($datetime,'yyyy年MM月dd日');
    }
    
    /**
     * 把时间转换成yyyy-MM-dd HH:mm格式
     * @param int $datetime
     * @return string
     */
    public static function formatterAsDateTime($datetime)
    {
        return \Yii::$app->formatter->asDatetime($datetime,'yyyy-MM-dd HH:mm');
    }

    /**
     * 报名时间的人性化返回
     * @param int $datetime
     * @return string
     */
    public static function partyJoinDateFormatter($datetime)
    {
        $time_span = time() - $datetime;
        if ( $time_span < 1*60 ) {
            $temp_date = "刚刚";
        } else if ( $time_span > 1*60 && $time_span < 1*3600 ) {
            $time_span_1 = $time_span/60;
            $time_span_2 = intval($time_span_1);
            $temp_date = (string)$time_span_2 ."分钟以前";
        } else if ( $time_span > 1*3600 && $time_span < 24*3600 ) {
            $time_span_1 = $time_span/(3600);
            $time_span_2 = intval($time_span_1);
            $temp_date = (string)$time_span_2 ."个小时前";
        } else {
            $temp_date = Util::formatterAsDate($datetime);
        }
        
        return $temp_date;

    }

    /**
     * 把文本地址通过腾讯地图API转换成为经纬度
     * @param string $address
     * @return string|null
     */
    public static function createLngLatFromAddress($address)
    {
        $address = str_replace(" ", "", $address);
        $key = \Yii::$app->params['qqmap']['keyId'];
        $url = \Yii::$app->params['qqmap']['url'];

        $client = new Client();
        try {
            $response = $client->get($url, [
                'address' => $address,
                'key' => $key,
            ])->send();
        } catch (Exception $ex) {
            \Yii::error($ex->getMessage());
            return null;
        }
        if ($response->isOk) {
            $gets = $response->data;
            if($gets['status'] == 0) { //腾讯地图服务端返回的状态码:0为正常,310请求参数信息有误,311key格式错误,306请求有护持信息请检查字符串,110请求来源未被授权
                if ($gets['result']['location']['lng'] && $gets['result']['location']['lat']) {
                    if ($gets['result']['deviation'] > -1 && $gets['result']['reliability'] >= 7) {
                        return $gets['result']['location']['lng'].'|'.$gets['result']['location']['lat'];
                    } else {
                        \Yii::warning("输入地址过于模糊，误差距离:".$gets['result']['deviation']."，可信度参考:".$gets['result']['reliability']."，经纬度偏差超出我们的标准，系统将经纬度地址设置为0");
                        return '0'.'|'.'0';
                    }
                }
            } else {
                \Yii::error("腾讯地图服务端返回的状态说明:".$gets['message']."，状态码:".$gets['status']);
                return null;
            }
        }
        return null;
    }
}

