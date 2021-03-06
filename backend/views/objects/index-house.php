<?php

use yii\helpers\Html,
    yii\grid\GridView,
    yii\helpers\ArrayHelper;
use common\models\House,
    common\models\Flat,
    common\models\Invoice;

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

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap'],
        'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
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
                'value' => function ($model) {
                    return $model->getNameSection();
                }
            ],
            [
                'attribute' => 'flatsCount',
                'label' => 'Кол-во квартир',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'width: 200px;'],
                'value' => function ($model) {
                    return Flat::find()->where(['house_id' => $model->id])
                                    ->andWhere(['unit_type' => Flat::TYPE_FLAT])
                                    ->count();
                }
            ],
            [
                'label' => 'Кол-во квартир продано',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'width: 200px;'],
                'value' => function ($model) {
                    $сount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_FLAT])
                            ->andWhere(['in', 'status', [Flat::STATUS_SOLD]])
                            ->count();
                    return $сount;
                }
            ],
            [
                'label' => 'Кол-во квартир в продаже',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'width: 200px;'],
                'value' => function ($model) {
                    $сount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_FLAT])
                            ->andWhere(['in', 'status', [Flat::STATUS_AVAILABLE]])
                            ->count();
                    return $сount;
                }
            ],
            [
                'label' => 'Кол-во коммерческих/ парко-мест/ кладовок/ машино-мест',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'white-space: normal;'],
                'value' => function ($model) {
                    $commercialCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_COMMERCIAL])
                            ->count();
                    $parkingCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_PARKING])
                            ->count();
                    $storageCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_STORAGE])
                            ->count();
                    $carCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_CAR_PLACE])
                            ->count();
                    return "$commercialCount/$parkingCount/$storageCount/$carCount";
                }
            ],
            [
                'label' => 'Кол-во коммерческих/ парко-мест/ кладовок/ машино-мест продано',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'white-space: normal;'],
                'value' => function ($model) {
                    $commercialCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_COMMERCIAL])
                            ->andWhere(['in', 'status', [Flat::STATUS_SOLD]])
                            ->count();
                    $parkingCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_PARKING])
                            ->andWhere(['in', 'status', [Flat::STATUS_SOLD]])
                            ->count();
                    $storageCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_STORAGE])
                            ->andWhere(['in', 'status', [Flat::STATUS_SOLD]])
                            ->count();
                    $carCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_CAR_PLACE])
                            ->andWhere(['in', 'status', [Flat::STATUS_SOLD]])
                            ->count();
                    return "$commercialCount/$parkingCount/$storageCount/$carCount";
                }
            ],
            [
                'label' => 'Кол-во коммерческих/ парко-мест/ кладовок/ машино-мест в продаже',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'white-space: normal;'],
                'value' => function ($model) {
                    $commercialCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_COMMERCIAL])
                            ->andWhere(['in', 'status', [Flat::STATUS_AVAILABLE]])
                            ->count();
                    $parkingCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_PARKING])
                            ->andWhere(['in', 'status', [Flat::STATUS_AVAILABLE]])
                            ->count();
                    $storageCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_STORAGE])
                            ->andWhere(['in', 'status', [Flat::STATUS_AVAILABLE]])
                            ->count();
                    $carCount = Flat::find()
                            ->where(['house_id' => $model->id])
                            ->andWhere(['unit_type' => Flat::TYPE_CAR_PLACE])
                            ->andWhere(['in', 'status', [Flat::STATUS_AVAILABLE]])
                            ->count();
                    return "$commercialCount/$parkingCount/$storageCount/$carCount";
                }
            ],
            [
                'label' => 'Сумма скидок/сумма наценок',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'white-space: normal;'],
                'value' => function ($model) {
                    $disAddQuery = Flat::find()->select([
                                'SUM(IF(`price_discount_m` > 0, (`price_discount_m`) * `square`, 0)) AS `total_discount`',
                                'SUM(IF(`price_discount_m` > 0 AND `square` <= 0, `price_discount_m`, 0)) AS `total_discount_parking`',
                                'SUM(IF(`price_discount_m` < 0, (`price_discount_m`) * `square` * -1, 0)) AS `total_added`',
                                'SUM(IF(`price_discount_m` < 0 AND `square` <= 0, (`price_discount_m`) * -1, 0)) AS `total_added_parking`'
                            ])->where(['house_id' => $model->id])->andWhere(['!=', 'price_sell_m', 0])->andWhere(['flat.status' => Flat::STATUS_SOLD])->asArray();
                    $disAddResult = $disAddQuery->one();
                    $totalDiscount = round($disAddResult['total_discount'] + $disAddResult['total_discount_parking'], 2);
                    $totalAdded = round($disAddResult['total_added'] + $disAddResult['total_added_parking'], 2);
                    return "$totalDiscount/$totalAdded";
                }
            ],
            [
                'label' => 'Сумма выплаченной комиссии агенство / менеджер',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'white-space: normal;'],
                'value' => function ($model) {
                    $sameFlatsIds = ArrayHelper::getColumn(Flat::find()->where(['house_id' => $model->id])->andWhere(['flat.status' => Flat::STATUS_SOLD])->all(), 'id');
                    $sumCommissionAgency = Invoice::find()->where(['in', 'flat_id', $sameFlatsIds])->andWhere(['is not', 'invoice.agency_id', null])->andWhere(['invoice.type' => Invoice::TYPE_OUTCOME, 'invoice.status' => Invoice::STATUS_COMPLETE])->sum('price');
                    $sumCommissionManager = Invoice::find()->where(['in', 'flat_id', $sameFlatsIds])->andWhere(['is not', 'invoice.user_id', null])->andWhere(['invoice.type' => Invoice::TYPE_OUTCOME, 'invoice.status' => Invoice::STATUS_COMPLETE])->sum('price');

                    $commissionAgency = round($sumCommissionAgency, 2);
                    $commissionManager = round($sumCommissionManager, 2);
                    return "$commissionAgency/$commissionManager";
                }
            ],
            [
                'label' => 'Сумма по не проданным',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'white-space: normal;'],
                'value' => function ($model) {
                    $disAddQuery = Flat::find()->select([
                                'SUM(IF(`square` > 0, (`price_sell_m`) * `square`, 0)) AS `price_rest`',
                                'SUM(IF(`square` <= 0, `price_sell_m`, 0)) AS `price_rest_parking`'
                            ])->where(['house_id' => $model->id])->andWhere(['flat.status' => Flat::STATUS_AVAILABLE])->asArray();
                    $disAddResult = $disAddQuery->one();
                    $priceRest = round($disAddResult['price_rest'] + $disAddResult['price_rest_parking'], 2);
                    return "$priceRest";
                }
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
    ]);
    ?>

</div>

<?php foreach ($dataProvider->getModels() as $model) { ?>
    <?=
    $this->render('_object_modal', [
        'model' => $model,
    ]);
    ?>
<?php } ?>
<?php
$newModel = new House();
$newModel->name = Yii::$app->request->get('HouseSearch')['name'] ?: null;
?>
<?=
$this->render('_object_modal', [
    'model' => $newModel,
]);
?>
