<?php

use backend\widgets\adminlte\Menu;
?>
<!-- /.search form -->
<!-- sidebar menu: : style can be found in sidebar.less -->
<?=
Menu::widget(
        [
            'options' => [
                'class' => 'sidebar-menu tree',
                'data-widget' => 'tree'
            ],
            //'itemOptions' => ['class' => 'treeview'],
            'items' => [
                [
                    'label' => 'Статистика',
                    'icon' => 'fa fa-line-chart',
                    'url' => '#',
                    'visible' => Yii::$app->user->identity->hasAccessToController('statistic'),
                    //'active' => ($route == 'statistic/index' || 'statistics-apartment/index' || 'statistics-month/index'),
                    'options' => [
                        'class' => 'treeview'
                    ],
                    'items' => [
                        [
                            'label' => 'ПЛАН-ФАКТ',
                            'icon' => 'fa fa-circle-o',
                            'url' => \yii\helpers\Url::to(['statistic/index']),
                            'active' => $route == 'statistic/index'
                        ],
                        [
                            'label' => 'Оплаты по помещениям',
                            'icon' => 'fa fa-circle-o',
                            'url' => \yii\helpers\Url::to(['statistic/apartment']),
                            'active' => $route == 'statistic/apartment'
                        ],
                        [
                            'label' => 'Ежемесячные платежи',
                            'icon' => 'fa fa-circle-o',
                            'url' => \yii\helpers\Url::to(['statistic/month']),
                            'active' => $route == 'statistic/month'
                        ]
                    ]
                ],
                [
                    'label' => 'Помещения',
                    'icon' => 'fa fa-key',
                    'url' => \yii\helpers\Url::to(['flats/index']),
                    'visible' => Yii::$app->user->identity->hasAccessToController('flats'),
                    'active' => $route == 'flats/index'
                ],
                [
                    'label' => 'Касса',
                    'icon' => 'fa fa-dollar',
                    'url' => \yii\helpers\Url::to(['payments/index']),
                    'visible' => Yii::$app->user->identity->hasAccessToController('payments'),
                    'active' => $route == 'payments/index'
                ],
                [
                    'label' => 'Покупатели',
                    'icon' => 'fa fa-users',
                    'url' => \yii\helpers\Url::to(['clients/index']),
                    'visible' => Yii::$app->user->identity->hasAccessToController('clients'),
                    'active' => $route == 'clients/index',
                ],
                [
                    'label' => 'Агентства',
                    'icon' => 'fa fa-briefcase',
                    'url' => \yii\helpers\Url::to(['agency/index']),
                    'visible' => Yii::$app->user->identity->hasAccessToController('agency'),
                    'active' => $route == 'agency/index'
                ],
                [
                    'label' => 'Объекты',
                    'icon' => 'fa fa-building',
                    'url' => \yii\helpers\Url::to(['objects/index']),
                    'visible' => Yii::$app->user->identity->hasAccessToController('objects'),
                    'active' => $route == 'objects/index'
                ],
                /*
                  [
                  'label' => 'Договора',
                  'icon' => 'fa fa-file-text-o',
                  'url' => \yii\helpers\Url::to(['contracts/index']),
                  'visible' => Yii::$app->user->identity->hasAccessToController('contacts'),
                  'active' => $route == 'contracts/index'
                  ],
                 * 
                 */
                [
                    'label' => 'Настройки',
                    'icon' => 'fa fa-wrench',
                    'url' => '#',
                    'visible' => Yii::$app->user->identity->hasAccessToController('settings'),
                    //'active' => $route == 'settings-cash-currency/index',
                    'options' => [
                        'class' => 'treeview'
                    ],
                    'items' => [
                        [
                            'label' => 'Кассы и валюта',
                            'icon' => 'fa fa-circle-o',
                            'url' => \yii\helpers\Url::to(['settings/cash-currency']),
                            'active' => $route == 'settings/cash-currency'
                        ],
                        [
                            'label' => 'Роли',
                            'icon' => 'fa fa-circle-o',
                            'url' => \yii\helpers\Url::to(['settings/roles']),
                            'active' => $route == 'settings/roles'
                        ],
                        [
                            'label' => 'Пользователи',
                            'icon' => 'fa fa-circle-o',
                            'url' => \yii\helpers\Url::to(['settings/users']),
                            'active' => $route == 'settings/users'
                        ],
                        [
                            'label' => 'Переменные',
                            'icon' => 'fa fa-circle-o',
                            'url' => \yii\helpers\Url::to(['settings/variables']),
                            'active' => $route == 'settings/variables'
                        ],
//                    [
//                        'label' => 'Установки',
//                        'icon' => 'fa fa-circle-o',
//                        'url' => \yii\helpers\Url::to(['site/index']),
//                        //'active' =>  $route == 'site/index'
//                    ]
                    ]
                ],
            ]
        ]
)
?>
        
