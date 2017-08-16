<?php
/**
 * Created by PhpStorm.
 * User: fallingdust
 * Date: 4/12/16
 * Time: 12:17 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class PluginsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'plugins/js/picker.js',
        'plugins/js/picker.date.js',
        'plugins/js/picker.time.js',
        'plugins/js/legacy.js'
    ];

    public $css = [
        'plugins/css/default.css',
        'plugins/css/default.date.css',
        'plugins/css/default.time.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
