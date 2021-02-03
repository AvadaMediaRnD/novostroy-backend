<?php

use yii\helpers\Html,
    yii\grid\GridView;
use backend\widgets\InformerWidget;
use common\helpers\PriceHelper,
    common\models\Article,
    common\models\Cashbox,
    common\models\Invoice,
    common\models\House,
    common\models\Flat,
    common\models\User;
use kartik\daterange\DateRangePicker,
    kartik\export\ExportMenu;
use PhpOffice\PhpSpreadsheet\Style\Border,
    PhpOffice\PhpSpreadsheet\Style\Color,
    PhpOffice\PhpSpreadsheet\Style\Fill;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totalBalance float */
/* @var $totalBalanceUah float */
/* @var $totalBalanceUsd float */
/* @var $totalBalanceEur float */
/* @var $totalIn float */
/* @var $totalInUah float */
/* @var $totalInUsd float */
/* @var $totalInEur float */
/* @var $totalOut float */
/* @var $totalOutUah float */
/* @var $totalOutUsd float */
/* @var $totalOutEur float */
/* @var $informerFilter array */

$this->title = 'Касса';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12 col-lg-7">
        <h2 class="page-header">Приходы и расходы</h2>
    </div>
    <div class="col-xs-12 col-lg-5">
        <div class="menu-align text-right">
            <div class="btn-group margin-bottom text-left display-inline">

                <a type="button" class="btn btn-success" href="#!" onclick="$('#export-xlsx a').click()">Выгрузить в Excel</a>

                <?php
                $gridColumns = [
                    'id',
                    'uid',
                    [
                        'attribute' => 'uid_date',
                        'value' => function ($model) {
                            return $model->getUidDate();
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return $model->getTypeLabel();
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return $model->getStatusLabel();
                        }
                    ],
                    [
                        'attribute' => 'searchHouse',
                        'label' => 'Объект',
                        'value' => function ($model) {
                            return $model->flat['house'] ? $model->flat['house']->getNameSection() : '';
                        }
                    ],
                    [
                        'attribute' => 'searchFlat',
                        'label' => 'Номер',
                        'value' => function ($model) {
                            if ($model->flat) {
                                return $model->flat->getNumberWithIndex();
                            }
                            return '';
                        }
                    ],
                    [
                        'attribute' => 'searchUnitType',
                        'label' => 'Тип помещения',
                        'value' => function ($model) {
                            if ($model->flat) {
                                return $model->flat->getUnitTypeLabel();
                            }
                            return null;
                        }
                    ],
                    [
                        'attribute' => 'article_id',
                        'value' => function ($model) {
                            return $model->article->name ?? '';
                        }
                    ],
                    [
                        'attribute' => 'price',
                        'value' => function ($model) {
                            $price = PriceHelper::format($model->price, false) ?? '0.00';
                            return $price;
                        }
                    ],
                    [
                        'attribute' => 'cashbox_id',
                        'value' => function ($model) {
                            return $model->cashbox->name;
                        }
                    ],
                    [
                        'attribute' => 'description',
                        'value' => function ($model) {
                            return strip_tags($model->description);
                        }
                    ],
                    [
                        'attribute' => 'searchCounterparty',
                        'label' => 'Контрагент',
                        'value' => function ($model) {
                            return $model->getCounterpartyLabel();
                        }
                    ],
                ];
                echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'showConfirmAlert' => false,
                    'showColumnSelector' => false,
                    'container' => ['class' => 'btn-group hide', 'role' => 'group'],
                    'filename' => 'export-payments-' . date('Ymd'),
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_HTML => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_CSV => false,
                        ExportMenu::FORMAT_EXCEL => false,
                        ExportMenu::FORMAT_EXCEL_X => [
                            'label' => Yii::t('kvexport', 'Excel 2007+'),
                            'icon' => 'floppy-remove',
                            'iconOptions' => ['class' => 'text-success'],
                            'linkOptions' => [],
                            'options' => ['id' => 'export-xlsx', 'title' => Yii::t('kvexport', 'Microsoft Excel 2007+ (xlsx)')],
                            'alertMsg' => Yii::t('kvexport', 'The EXCEL 95+ (xls) export file will be generated for download.'),
                            'mime' => 'application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'extension' => 'xlsx',
                            'writer' => ExportMenu::FORMAT_EXCEL_X,
                        ],
                    ],
                    'dropdownOptions' => [
                        'label' => 'Выгрузить в XLS',
                        'class' => 'btn btn-secondary'
                    ],
                    'boxStyleOptions' => [
                        ExportMenu::FORMAT_EXCEL_X => [
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ],
                                'inside' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ]
                            ],
                        ],
                    ],
                    'headerStyleOptions' => [
                        ExportMenu::FORMAT_EXCEL_X => [
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => [
                                    'argb' => 'FFE5E5E5',
                                ],
                            ],
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ],
                                'inside' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ]
                            ],
                        ],
                    ],
                    'onRenderDataCell' => function($cell, $content, $model, $key, $index, $widget) {
                        if ($cell->getColumn() != 'G') {
                            /* @var $cell PhpOffice\PhpSpreadsheet\Cell\Cell */
                            $cell->setValueExplicit($content, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        }
                    },
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<div class="row">

    <?=
    InformerWidget::widget([
        'notRoles' => [User::ROLE_VIEWER_FLAT],
        'items' => [
            InformerWidget::W_CASH_STATE,
            InformerWidget::W_CASH_IN,
            InformerWidget::W_CASH_OUT,
        ],
        'filter' => $informerFilter,
    ])
    ?>

</div>
<div class="row">
    <div class="col-xs-12">
        <a href="<?= Yii::$app->urlManager->createUrl(['/payments/create', 'type' => Invoice::TYPE_INCOME]) ?>" class="btn btn-success margin-right-15 margin-bottom-15">+ Приход</a>
        <a href="<?= Yii::$app->urlManager->createUrl(['/payments/create', 'type' => Invoice::TYPE_OUTCOME]) ?>" class="btn btn-danger margin-bottom-15">- Расход </a>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Список платежей</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/payments/index']) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Очистить фильтры</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap linkedRow'],
                'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return [
                        'data-href' => Yii::$app->urlManager->createUrl(['/payments/update', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    /* [
                      'attribute' => 'uid',
                      'label' => '#',
                      'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                      ], */
                    [
                        'attribute' => 'searchUidDateRange',
                        'label' => 'Дата',
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'searchUidDateRange',
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'timePicker' => false,
                                'locale' => ['format' => 'd.m.Y']
                            ]
                        ]),
                        'headerOptions' => ['style' => 'min-width: 125px; max-width: 180px; width: 180px'],
                        'value' => function ($model) {
                            return $model->getUidDate();
                        }
                    ],
                    [
                        'attribute' => 'type',
                        'format' => 'html',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Invoice::getTypeOptions(),
                            'model' => $searchModel,
                            'attribute' => 'type',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                        'value' => function ($model) {
                            return $model->getTypeLabelHtml();
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'html',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Invoice::getStatusOptions(),
                            'model' => $searchModel,
                            'attribute' => 'status',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        }
                    ],
                    [
                        'attribute' => 'searchHouse',
                        'label' => 'Объект',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => House::getOptions(),
                            'model' => $searchModel,
                            'attribute' => 'searchHouse',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 200px; min-width: 200px'],
                        'value' => function ($model) {
                            return $model->flat['house'] ? $model->flat['house']->getNameSection() : null;
                        }
                    ],
                    [
                        'attribute' => 'searchFlat',
                        'label' => 'Номер',
                        'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                        'value' => function ($model) {
                            if ($model->flat) {
                                return $model->flat->getNumberWithIndex();
                            }
                            return null;
                        }
                    ],
                    [
                        'attribute' => 'searchUnitType',
                        'label' => 'Тип помещения',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Flat::getUnitTypeOptions(),
                            'model' => $searchModel,
                            'attribute' => 'searchUnitType',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'value' => function ($model) {
                            if ($model->flat) {
                                return $model->flat->getUnitTypeLabel();
                            }
                            return null;
                        }
                    ],
                    [
                        'attribute' => 'article_id',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Article::getOptions(),
                            'model' => $searchModel,
                            'attribute' => 'article_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 200px; min-width: 200px'],
                        'value' => function ($model) {
                            return $model->article->name ?? '';
                        }
                    ],
                    [
                        'attribute' => 'price',
                        'filter' => false,
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                        'value' => function ($model) {
                            $price = PriceHelper::format($model->price) ?? '0.00';
                            return $price;
                        }
                    ],
                    [
                        'attribute' => 'cashbox_id',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Cashbox::getOptions(),
                            'model' => $searchModel,
                            'attribute' => 'cashbox_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                        'value' => function ($model) {
                            return $model->cashbox->name;
                        }
                    ],
                    [
                        'attribute' => 'searchCounterparty',
                        'enableSorting' => false,
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Invoice::getCounterpartyOptions(),
                            'model' => $searchModel,
                            'attribute' => 'searchCounterparty',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['dropdownAutoWidth' => true],
                        ]),
                        'label' => 'Контрагент',
                        'value' => function ($model) {
                            return $model->getCounterpartyLabel();
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                                [
                                                    '/payments/update',
                                                    'id' => $model->id,
                                                ],
                                                ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                                [
                                                    '/payments/delete',
                                                    'id' => $model->id,
                                                ],
                                                ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                                );
                            },
                        ]
                    ],
                ],
            ]);
            ?>

        </div>
    </div>
</div>
