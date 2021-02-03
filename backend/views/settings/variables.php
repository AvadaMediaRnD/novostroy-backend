<?php 

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;
use common\models\SystemConfig;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SystemConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Переменные';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12 text-right">
        <a href="<?= Yii::$app->urlManager->createUrl(['/settings/variable-create']) ?>">
            <button type="button" class="btn btn-success margin-bottom-15"><i class="fa fa-plus"></i> Создать переменную</button>
        </a>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/settings/variables']) ?>" class="btn btn-default btn-sm">
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
                'data-href' => Yii::$app->urlManager->createUrl(['/settings/variable-view', 'id' => $model['id']]),
            ];
        },
        'columns' => [
            [
                'attribute' => 'value_raw',
            ],
            [
                'attribute' => 'key',
            ],
            [
                'attribute' => 'description',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::decode($model->description);
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
                                '/settings/variable-update',
                                'id' => $model->id,
                            ],
                            ['class' => 'btn btn-primary btn-sm', 'title' => 'Редактировать']
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-trash"></i>',
                            [
                                '/settings/variable-delete',
                                'id' => $model->id,
                            ],
                            ['class' => 'btn btn-danger btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                        );
                    },
                ]
            ],
        ],
    ]); ?>

</div>
