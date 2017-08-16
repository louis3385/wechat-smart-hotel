<?php

namespace frontend\assets\guest;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/manage/manager_index.js',
        'js/zepto/zepto.js',
        'js/zepto/endecode.js',
        'js/zepto/event.js',
        'js/zepto/fx.js',
        'js/zepto/fx_methods.js',
        'js/zepto/touch.js',
        'js/zepto/selector.js',
    ];

    public $css = [
        'css/home.css',
        'css/common.css',
        'css/other.css'
    ];

    public $depends = [
        'frontend\assets\AppAsset',
        'frontend\assets\WechatAsset1',
        'frontend\assets\VueAsset'
    ];

}
