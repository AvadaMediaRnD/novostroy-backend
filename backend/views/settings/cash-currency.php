<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\product\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <h2 class="page-header">Кассы и валюта</h2>
    </div>
</div>
<?= Html::beginForm(['settings/update-currency-rates']);?>
<?php Pjax::begin(['id' => 'pjax-grid-view']); ?>
<div class="row">
    <div class="col-xs-12 col-lg-6">
        <div class="box">
            <?= GridView::widget([
                'dataProvider' => $provider,
                'filterModel' => null,
                'options' => [
                    'style' => 'overflow:auto;'
                ],
                'tableOptions' => [
                    'class' => 'table table-bordered table-hover table-striped',
                ],
                'layout' => "<div class='box-header with-border'>
                    <h3 class='box-title'>Список касс и валют</h3>
                  "
                    //.SelectPajaxPageSizeWidget::widget(['pageSize' => 1, 'pajaxId' => 'pjax-grid-view'])
                    ."</div>\n<div class='box-body'>{items}</div>\n
                ",
                //<div style ='overflow:auto;'>{pager}",
                //.SelectPajaxPageSizeWidget::widget(['pageSize' => 1, 'pajaxId' => 'pjax-grid-view'])."</div>",
                'columns' => [
                    [
                        'header' => 'Основная',
                        'headerOptions' => ['style' => 'width: 40px; min-width: 40px;'],
                        'class' => 'yii\grid\CheckboxColumn',
                        //'attribute' => 'is_default',
                        /*'options' => [
                            'uncheck' => 'empty'
                        ],*/
                        'checkboxOptions' => function($model) {
                            return [
                                'value' =>  $model->id,
                                'disabled' => true,
                                'checked' => $model->is_default,
                            ];
                        },
                        // 'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'header' => 'Название',
                        //'class' => 'yii\grid\CheckboxColumn',
                        'attribute' => 'name',
                    ],
                    [
                        'header' => 'Валюта',
                        //'class' => 'yii\grid\CheckboxColumn',
                        'attribute' => 'currency',
                    ],
                    [
                        'header' => 'Курс',
                        //'class' => 'yii\grid\CheckboxColumn',
                        'attribute' => 'rate',
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'width: 40px; min-width: 40px;'],
                        'template' => '<div class="btn-group pull-right">{edit}</div>',
                        'buttons' => [
                            'edit' => function ($url, $model) {
                                return Html::a('<i class="fa fa-pencil"></i>', "#", [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal' . $model->id,
                                    'title' => Yii::t('app', 'Update'),
                                    'class' => 'btn btn-primary btn-sm'
                                ]);
                            }
                        ],
                    ],
                ]
            ]); ?>
            <div class="box-body">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="currencySync" <?=$currencySync == 1 ? 'checked' : '' ?> onchange="this.value = this.checked ? 1 : 0; form.submit()"> Использовать автоматически верхний курс ПриватБанка
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
<?= Html::endForm(); ?>

<?php if(isset($models)) : ?>
    <?php foreach($models as $model) : ?>
    <?= $this->render('_cash_currency_modal', [
        'model' => $model,
        'state' => $currencySync
    ]); ?>
    <?php endforeach; ?>
<?php endif; ?>

