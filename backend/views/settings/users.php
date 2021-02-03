<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12 col-lg-5">
        <h2 class="page-header">Пользователи</h2>
    </div>
    <div class="col-xs-12 col-lg-7 text-right">
        <button type="button" class="btn btn-success margin-bottom-15" data-toggle="modal" data-target="#modal1"><i class="fa fa-user-plus"></i> Создать пользователя</button>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Список пользователи</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/settings/users']) ?>" class="btn btn-default btn-sm">
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/settings/user-update', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'searchFullname',
                        'label' => 'Пользователь',
                        'value' => function ($model) {
                            return $model->fullname;
                        }
                    ],
                    [
                        'attribute' => 'role',
                        'enableSorting' => false,
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => User::getRoleOptions(),
                            'model' => $searchModel,
                            'attribute' => 'role',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'label' => 'Роль',
                        'value' => function ($model) {
                            return $model->getRoleLabel();
                        }
                    ],
                    [
                        'attribute' => 'phone',
                        'label' => 'Телефон',
                        'value' => function ($model) {
                            return $model->phone . '';
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'format' => 'email',
                        'value' => function ($model) {
                            return $model->email . '';
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Статус',
                        'enableSorting' => false,
                        'format' => 'html',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => User::getStatusOptions(),
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
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{activate} {disable} {invite} {update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 145px; min-width: 145px'],
                        'buttons' => [
                            'disable' => function ($url, $model, $key) {
                                if ($model->status != User::STATUS_ACTIVE || $model->id == Yii::$app->user->id || $model->role > Yii::$app->user->identity->role) {
                                    return '';
                                }
                                return Html::a('<i class="fa fa-power-off"></i>',
                                    [
                                        '/settings/user-disable',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-info btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Отключить']
                                );
                            },
                            'activate' => function ($url, $model, $key) {
                                if ($model->status == User::STATUS_ACTIVE || $model->id == Yii::$app->user->id || $model->role > Yii::$app->user->identity->role) {
                                    return '';
                                }
                                return Html::a('<i class="fa fa-power-off"></i>',
                                    [
                                        '/settings/user-activate',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-info btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Активировать']
                                );
                            },
                            'invite' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-repeat"></i>',
                                    [
                                        '/settings/user-invite',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-warning btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Отправить приглашение']
                                );
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/settings/user-update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-primary btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/settings/user-delete',
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
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal1" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <?= $this->render('_user-add-modal-form', [
                'modelForm' => new backend\models\UserForm(),
            ]); ?>
        </div>
    </div>
</div>
<!-- Modal -->

<?php 
if (Yii::$app->session->hasFlash('success')) {
    $this->registerJs('alert("'.Yii::$app->session->getFlash('success').'");');
}
