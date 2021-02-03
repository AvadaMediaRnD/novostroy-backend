<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Europe/Kiev',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'dateFormat' => 'php:d.m.Y',
            'timeFormat' => 'php:h:i:s',
            'datetimeFormat' => 'php:d.m.Y h:i',
            'decimalSeparator' => '.',
        ],
        'glide' => [
            'class' => 'trntv\glide\components\Glide',
            'sourcePath' => '@frontend/web',
            'cachePath' => '@frontend/web/upload/cache',
            'signKey' => false, // 'surprisemotherfucker',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    //'sourceLanguage' => 'ru-RU',
                    'fileMap' => [
                        'app' => 'app.php',
                        'error' => 'error.php',
                        'model' => 'model.php',
                    ],
                ],
            ],
        ],
    ],
    'language' => 'ru-RU',
];
