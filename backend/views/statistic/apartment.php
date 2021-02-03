<?php

use yii\grid\GridView;
use backend\widgets\InformerWidget;
use common\models\House,
    common\models\ViewTotalFlat,
    common\models\Agency;

/* @var $this yii\web\View */
/* @var $searchModel \backend\models\ViewTotalFlatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <h2 class="page-header">Сводка оплат по помещениям:</h2>
    </div>
</div>
<div class="row">

    <?=
    InformerWidget::widget([
        'items' => [
            InformerWidget::W_FLATS,
            InformerWidget::W_MONEY,
            InformerWidget::W_DEBT,
        ],
    ])
    ?>

</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Таблица платежей</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/statistic/apartment']) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Очистить фильтры</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <?=
            GridView::widget([
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/flats/update', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    /* [
                      'class' => 'yii\grid\SerialColumn',
                      'headerOptions' => ['style' => 'width: 40px; min-width: 40px'],
                      ], */

                    [
                        'attribute' => 'number',
                        'headerOptions' => ['style' => 'width: 100px; min-width: 100px'],
                    ],
                    [
                        'attribute' => 'square',
                        'headerOptions' => ['style' => 'min-width: 100px; width: 100px'],
                        'filter' => \kartik\field\FieldRange::widget([
                            'model' => $searchModel,
                            'attribute1' => 'searchSquareFrom',
                            'attribute2' => 'searchSquareTo',
                            'type' => \kartik\field\FieldRange::INPUT_TEXT,
                            'separator' => '',
                            'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                            'options1' => ['placeholder' => 'от',],
                            'options2' => ['placeholder' => 'до',],
                        ]),
                    ],
                    [
                        'attribute' => 'house_id',
                        'filter' => House::getOptions(),
                        'value' => function ($model) {
                            return $model->getNameSection();
                        }
                    ],
                    /*
                      [
                      'attribute' => 'price',
                      'headerOptions' => ['style' => 'min-width: 100px'],
                      'filter' => \kartik\field\FieldRange::widget([
                      'model' => $searchModel,
                      'attribute1' => 'searchPriceFrom',
                      'attribute2' => 'searchPriceTo',
                      'type' => \kartik\field\FieldRange::INPUT_TEXT,
                      'separator' => '',
                      'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                      'options1' => ['placeholder' => 'от',],
                      'options2' => ['placeholder' => 'до',],
                      ]),
                      'value' => 'priceFormatted',
                      ],
                      [
                      'attribute' => 'price_m',
                      'label' => 'Цена за м<sup>2</sup>',
                      'headerOptions' => ['style' => 'min-width: 100px'],
                      'filter' => \kartik\field\FieldRange::widget([
                      'model' => $searchModel,
                      'attribute1' => 'searchPriceMFrom',
                      'attribute2' => 'searchPriceMTo',
                      'type' => \kartik\field\FieldRange::INPUT_TEXT,
                      'separator' => '',
                      'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                      'options1' => ['placeholder' => 'от',],
                      'options2' => ['placeholder' => 'до',],
                      ]),
                      'value' => 'priceMFormatted',
                      'encodeLabel' => false,
                      ],
                     * 
                     */
                    [
                        'attribute' => 'price_plan',
                        'label' => 'Цена продажи',
                        'headerOptions' => ['style' => 'min-width: 100px'],
                        'filter' => \kartik\field\FieldRange::widget([
                            'model' => $searchModel,
                            'attribute1' => 'searchPricePlanFrom',
                            'attribute2' => 'searchPricePlanTo',
                            'type' => \kartik\field\FieldRange::INPUT_TEXT,
                            'separator' => '',
                            'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                            'options1' => ['placeholder' => 'от',],
                            'options2' => ['placeholder' => 'до',],
                        ]),
                        'value' => 'pricePlanFormatted',
                    ],
                    /*
                      [
                      'attribute' => 'price_sell_m',
                      'label' => 'Продажи за м<sup>2</sup>',
                      'headerOptions' => ['style' => 'min-width: 100px'],
                      'filter' => \kartik\field\FieldRange::widget([
                      'model' => $searchModel,
                      'attribute1' => 'searchPriceSellMFrom',
                      'attribute2' => 'searchPriceSellMTo',
                      'type' => \kartik\field\FieldRange::INPUT_TEXT,
                      'separator' => '',
                      'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                      'options1' => ['placeholder' => 'от',],
                      'options2' => ['placeholder' => 'до',],
                      ]),
                      'value' => 'priceSellMFormatted',
                      'encodeLabel' => false,
                      ],
                     * 
                     */
                    [
                        'attribute' => 'price_fact',
                        'headerOptions' => ['style' => 'min-width: 100px'],
                        'filter' => \kartik\field\FieldRange::widget([
                            'model' => $searchModel,
                            'attribute1' => 'searchPriceFactFrom',
                            'attribute2' => 'searchPriceFactTo',
                            'type' => \kartik\field\FieldRange::INPUT_TEXT,
                            'separator' => '',
                            'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                            'options1' => ['placeholder' => 'от',],
                            'options2' => ['placeholder' => 'до',],
                        ]),
                        'value' => 'priceFactFormatted',
                    ],
                    [
                        'attribute' => 'price_left',
                        'headerOptions' => ['style' => 'min-width: 100px'],
                        'filter' => \kartik\field\FieldRange::widget([
                            'model' => $searchModel,
                            'attribute1' => 'searchPriceLeftFrom',
                            'attribute2' => 'searchPriceLeftTo',
                            'type' => \kartik\field\FieldRange::INPUT_TEXT,
                            'separator' => '',
                            'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                            'options1' => ['placeholder' => 'от',],
                            'options2' => ['placeholder' => 'до',],
                        ]),
                        'value' => 'priceLeftFormatted',
                    ],
                    [
                        'attribute' => 'price_debt',
                        'label' => 'Задолженность',
                        'headerOptions' => ['style' => 'min-width: 100px'],
                        'filter' => \kartik\field\FieldRange::widget([
                            'model' => $searchModel,
                            'attribute1' => 'searchPriceDebtFrom',
                            'attribute2' => 'searchPriceDebtTo',
                            'type' => \kartik\field\FieldRange::INPUT_TEXT,
                            'separator' => '',
                            'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                            'options1' => ['placeholder' => 'от',],
                            'options2' => ['placeholder' => 'до',],
                        ]),
                        'value' => 'priceDebtFormatted',
                    ],
                    [
                        'attribute' => 'sell_status',
                        'headerOptions' => ['style' => 'min-width: 120px'],
                        'filter' => ViewTotalFlat::getSellStatusOptions(),
                        'value' => function ($model) {
                            return $model->getSellStatusLabel();
                        }
                    ],
                    [
                        'attribute' => 'searchClientFullname',
                        'label' => 'Покупатель',
                        'value' => function ($model) {
                            $value = $model->getClientFullname();
                            if ($value === null) {
                                return '';
                            }
                            return $value;
                        }
                    ],
                    [
                        'attribute' => 'phone',
                        'value' => function ($model) {
                            if ($model->phone === null) {
                                return '';
                            }
                            return $model->phone;
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'value' => function ($model) {
                            if ($model->email === null) {
                                return '';
                            }
                            return $model->email;
                        }
                    ],
                    [
                        'attribute' => 'searchUserFullname',
                        'label' => 'Менеджер',
                        'value' => function ($model) {
                            $value = $model->getUserFullname();
                            if ($value === null) {
                                return '';
                            }
                            return $value;
                        }
                    ],
                    [
                        'attribute' => 'agency_id',
                        'filter' => Agency::getOptions(),
                        'value' => function ($model) {
                            if ($model->agency_id === null) {
                                return '';
                            }
                            return $model->agency_name;
                        }
                    ],
                    [
                        'attribute' => 'price_discount',
                        'headerOptions' => ['style' => 'min-width: 100px'],
                        'filter' => \kartik\field\FieldRange::widget([
                            'model' => $searchModel,
                            'attribute1' => 'searchPriceDiscountFrom',
                            'attribute2' => 'searchPriceDiscountTo',
                            'type' => \kartik\field\FieldRange::INPUT_TEXT,
                            'separator' => '',
                            'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                            'options1' => ['placeholder' => 'от',],
                            'options2' => ['placeholder' => 'до',],
                        ]),
                        'value' => 'priceDiscountFormatted',
                    ],
                ],
            ]);
            ?>

        </div>
    </div>
</div>
