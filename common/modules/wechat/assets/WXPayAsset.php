<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 4/15/16
 * Time: 2:10 PM
 */

namespace common\modules\wechat\assets;


use yii\web\AssetBundle;

class WXPayAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $js = [
        'js/wxpay.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}