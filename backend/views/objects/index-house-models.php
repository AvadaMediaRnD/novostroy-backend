<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;
use common\models\House;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\HouseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Объект ' . Yii::$app->request->get('HouseSearch')['name'];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Объекты'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::$app->request->get('HouseSearch')['name'];
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="#!" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal">
                <span class="hidden-xs">Добавить объект</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'tableOptions'=>['class'=>'table table-bordered table-hover table-striped table-nowrap'],
        'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
        'pager' => [
            'class' => 'yii\widgets\LinkPager',
            'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
        ],
        'rowOptions' => function ($model, $index, $widget, $grid) {
            return [
                'data-href' => Yii::$app->urlManager->createUrl(['/objects/view', 'id' => $model['id']]),
            ];
        },
        'columns' => [
            [
                'attribute' => 'name',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'section',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'address',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'status',
                'enableSorting' => false,
                'value' => function ($model) {
                    return $model->getStatusLabel();
                }
            ],
            [
                'attribute' => 'commission_agency',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'commission_manager',
                'enableSorting' => false,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-pencil"></i>',
                            '#!',
                            ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'modal', 'data-target' => '#modal' . $model->id, 'title' => 'Редактировать']
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-trash"></i>',
                            [
                                '/objects/delete',
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

<?php foreach($dataProvider->getModels() as $model) { ?>
    <?= $this->render('_object_modal', [
        'model' => $model,
    ]); ?>
<?php } ?>
<?php
$newModel = new House();
$newModel->name = Yii::$app->request->get('HouseSearch')['name'] ?: null;
?>
<?= $this->render('_object_modal', [
    'model' => $newModel,
]); ?>
