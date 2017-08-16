<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 10/5/15
 * Time: 1:23 AM
 */

namespace frontend\components;


use yii\base\Exception;
use yii\httpclient\Client;

class SMSGateway
{
    /**
     * 发送短信
     * @param string $mobile
     * @param string $content
     * @return bool
     */
    public static function send($mobile, $content)
    {
        $url = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $client = new Client();
        try {
            $response = $client->post($url, [
                'account' => 'cf_shuyin',
                // 密码可以使用明文密码或使用32位MD5加密
                'password' => 'cH33Gf',
                'mobile' => $mobile,
                'content' => $content,
            ])->send();
        } catch (Exception $ex) {
            \Yii::error($ex->getMessage());
            return false;
        }
        if ($response->isOk) {
            //$gets =  self::xml_to_array($response->getContent());
            $gets = $response->data;
            if($gets['code'] == 2) {
                return true;
            } else {
                \Yii::error($gets['msg']);
                return false;
            }
        }
        return false;
    }
}
