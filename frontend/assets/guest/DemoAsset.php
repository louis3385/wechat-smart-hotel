<?php

namespace frontend\assets\guest;

use yii\web\AssetBundle;

class DemoAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/demo/demo.css',
    ];

    public $js = [
        'js/zepto/zepto.js',
        'js/zepto/endecode.js',
        'js/zepto/event.js',
        'js/zepto/fx.js',
        'js/zepto/fx_methods.js',
        'js/zepto/touch.js',
        'js/zepto/selector.js',
    ];

    public $depends = [
        'frontend\assets\AppAsset',
        'frontend\assets\WechatAsset',
        'common\modules\wechat\assets\WXPayAsset',
        'frontend\assets\VueAsset',
    ];
}
