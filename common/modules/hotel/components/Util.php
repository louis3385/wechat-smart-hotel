<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/16/16
 * Time: 12:38 PM
 */

namespace common\modules\hotel\components;

use yii\httpclient\Client;
use yii\base\Exception;

class Util
{
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

}

