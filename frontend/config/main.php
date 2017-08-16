<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'view' => 'frontend\components\MyView',
        'user' => [
            'identityClass' => 'common\modules\wechat\models\WxUser',
            'enableAutoLogin' => true,
            'loginUrl' => '/wechat/oauth/login',
            'identityCookie' => ['name' => 'W_U_L_I', 'httpOnly' => false],
            // 修复Yii 2.0.8对"Accept: */*"的请求不能重定向到登录页的问题
            'acceptableRedirectTypes' => ['text/html', 'application/xhtml+xml', '*/*'],
        ],
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => YII_DEBUG ? true : false,
            'bundles' => require(__DIR__ . '/' . (YII_ENV_PROD ? 'assets-prod.php' : 'assets-dev.php')),
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'maxLogFiles' => 30,
                    'rotateByCopy' => false,
                ],
                [
                    'class' => 'yii\log\EmailTarget',
                    'mailer' =>'mailer',
                    'levels' => ['error'],
                    'message' => [
                        'from' => [''],
                        'to' => [''],
                        'subject' => '[HDD][FRONTEND][ERROR]',
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                        'javascript',
                    ],
                ],
                [
                    'class' => 'yii\log\EmailTarget',
                    'mailer' =>'mailer',
                    'levels' => ['error'],
                    'message' => [
                        'from' => [''],
                        'to' => [''],
                        'subject' => '[HDD][FRONTEND][ERROR]',
                    ],
                    'categories' => [
                        'javascript',
                    ],
                ]
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ],
            ],
        ],
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'guest/index',
                'debug' => 'guest/debug',
                'demo' => 'guest/demo',
                'guest/recognize' => 'guest/recognize',
                'post/api:6' => 'guest/hotel-list',
                'post/api:7' => 'guest/hotel-id',
                'post/api:97' => 'guest/pay-status',
                'post/api:18' => 'guest/join',
                'guest/<id:\d+>' => 'guest/detail',
                'timeline/<id:\w+>' => 'user/timeline'
            ],
        ],
    ],
    'modules' => [
        'wechat' => [
            'class' => 'common\modules\wechat\Module',
        ],
    ],
    'params' => $params,
];
