<?php
/**
 * Configuration file for the "yii asset" console command.
 */

// In the console environment, some path aliases may not exist. Please define these:
Yii::setAlias('@webroot', __DIR__ . '/../web');
Yii::setAlias('@web', '/');

return [
    // Adjust command/callback for JavaScript files compressing:
    'jsCompressor' => 'java -jar ' . __DIR__ . '/../../vendor/bin/compiler.jar --language_out=ES5 --js {from} --js_output_file {to}',
    // Adjust command/callback for CSS files compressing:

    'cssCompressor' => 'java -jar ' . __DIR__ . '/../../vendor/bin/yuicompressor.jar --type css {from} -o {to}',
    // The list of asset bundles to compress:
    'bundles' => [
    ],
    // Asset bundle for compression output:
    'targets' => [
        'all' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'all-{hash}.js',
            'css' => 'all-{hash}.css',
        ],
        'jquery' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'jquery-{hash}.js',
            'depends' => ['yii\web\JqueryAsset'],
        ],
        'avalon' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/js/avalon', // 放在/js/avalon下AMD可以加载
            'baseUrl' => '@web/js/avalon',
            'js' => 'avalon-{hash}.js',
            'depends' => ['frontend\assets\AvalonAsset'],
        ],
        'vue' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'vue-{hash}.js',
            'depends' => ['frontend\assets\VueAsset'],
        ],
        'floala' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'floala-{hash}.js',
            'css' => 'floala-{hash}.css',
            'depends' => ['frontend\assets\FroalaAsset'],
        ],
        'common' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'common-{hash}.js',
            'css' => 'common-{hash}.css',
            'depends' => [
                'frontend\assets\AppAsset',
            ],
        ],
        'joins' => [
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/assets',
            'baseUrl' => '@web/assets',
            'js' => 'joins-{hash}.js',
            'css' => 'joins-{hash}.css',
            'depends' => ['frontend\assets\my\JoinsAsset'],
        ],
    ],
    // Asset manager configuration:
    'assetManager' => [
        'basePath' => '@webroot/assets',
        'baseUrl' => '@web/assets',
    ],
];