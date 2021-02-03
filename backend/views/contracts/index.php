<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;
use kartik\daterange\DateRangePicker;
use common\models\Agreement;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AgreementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Договора';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Владельцы квартир</h2>-->
    <!--</div>-->
    <div class="col-xs-12">
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/contracts/create']) ?>">Оформить договор</a></li>
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/contracts/template-index']) ?>">Шаблоны договоров</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Список договоров</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/index']) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Очистить фильтры</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions'=>['class'=>'table table-bordered table-hover table-striped table-nowrap'],
                'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return [
                        'data-href' => Yii::$app->urlManager->createUrl(['/contracts/update', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'uid',
                        'label' => '#',
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'attribute' => 'uid_date',
                        'label' => 'Дата подписания',
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
                        'attribute' => 'searchNumber',
                        'label' => '№ квартиры',
                        'format' => 'html',
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                        'value' => function ($model) {
                            return $model->flat->number;
                        }
                    ],
                    [
                        'attribute' => 'searchHouse',
                        'label' => 'Объект',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->flat->house->name;
                        }
                    ],
                    [
                        'attribute' => 'searchClient',
                        'label' => 'Покупатель',
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->client->fullname;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'enableSorting' => false,
                        'format' => 'html',
                        'headerOptions' => ['style' => 'width: 160px; min-width: 160px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Agreement::getStatusOptions(),
                            'model' => $searchModel,
                            'attribute' => 'status',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        }
                    ],
                    [
                        'attribute' => 'description',
                        'label' => 'Примечание',
                        'format' => 'html',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->getDescriptionShort();
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
                                        '/contracts/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/contracts/delete',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                                );
                            },
                        ]
                    ],
                ],
            ]); ?>
            
        </div>
    </div>
</div>
