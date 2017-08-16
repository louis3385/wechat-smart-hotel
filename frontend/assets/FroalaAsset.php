<?php
/**
 * Created by PhpStorm.
 * User: fallingdust
 * Date: 4/12/16
 * Time: 12:17 PM
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class FroalaAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';


    public $js = [
        'froala_editor/js/froala_editor.min.js',
        'froala_editor/js/plugins/insertReview.js',
        'froala_editor/js/plugins/insertVideo.js',
        'froala_editor/js/plugins/align.min.js',
        'froala_editor/js/plugins/colors.min.js',
        'froala_editor/js/plugins/draggable.min.js',
        'froala_editor/js/plugins/font_size.min.js',
        'froala_editor/js/plugins/image2.min.js',
        'froala_editor/js/plugins/link.min.js',
        'froala_editor/js/plugins/insertImage.js',
        'froala_editor/js/picker.js',
        'froala_editor/js/picker.date.js',
        'froala_editor/js/picker.time.js',
        'froala_editor/js/legacy.js'
    ];

    public $css = [
       'froala_editor/css/wangEditor-mobile.min.css',
        'froala_editor/css/font-awesome.min.css',
        'froala_editor/css/test.css',
        'froala_editor/css/froala_editor.css',
        'froala_editor/css/froala_style.css',
        'froala_editor/css/plugins/insertReview.css',
        'froala_editor/css/plugins/code_view.css',
        'froala_editor/css/plugins/colors.css',
        'froala_editor/css/plugins/image.css',
        'froala_editor/css/plugins/quick_insert.css',
        'froala_editor/css/default.css',
        'froala_editor/css/default.date.css',
        'froala_editor/css/default.time.css',
        
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
