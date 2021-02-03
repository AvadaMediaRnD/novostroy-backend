<?php

return [
    'class' => 'yii\web\UrlManager',
    'baseUrl' => '/api',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '<controller:[\w-]+>/<id:[\d\-]+>' => 'v1/<controller>/view',
        '<controller:[\w-]+>/<action:[\w-]+>/<id:[\d\-]+>' => 'v1/<controller>/<action>',
        '<controller:[\w-]+>/<action:[\w-]+>' => 'v1/<controller>/<action>',
    ],
];
