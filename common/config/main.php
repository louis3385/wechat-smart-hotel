<?php
return [
    'name' => 'WeChat Smart Hotel',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mutex' => [
            'class' => 'yii\mutex\MysqlMutex',
        ],
    ],
];
