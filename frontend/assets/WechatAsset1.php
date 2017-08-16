<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class WechatAsset1 extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/jweixin-1.0.0.js',
        'js/share.js',
        'js/wxGuestRecognition.js',
    ];
}