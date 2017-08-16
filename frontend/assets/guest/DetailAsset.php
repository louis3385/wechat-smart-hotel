<?php

namespace frontend\assets\guest;

use yii\web\AssetBundle;

class DetailAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/jquery.datetimepicker.js',
        'js/detail/bookProcess.js',
        'js/detail/functionMenu.js',
        'js/detail/roomTypeList.js',
        'js/detail/main.js',
    ];

    public $css = [
        'css/jquery.datetimepicker.css',
        'css/detail/bookProcess.css',
        'css/detail/functionMenu.css',
        'css/detail/roomTypeList.css',
        'css/detail/main.css',
    ];

    public $depends = [
        'frontend\assets\AppAsset',
        //'frontend\assets\PluginsAsset',
        'frontend\assets\WechatAsset',
        'common\modules\wechat\assets\WXPayAsset',
        'frontend\assets\VueAsset',
    ];
}
