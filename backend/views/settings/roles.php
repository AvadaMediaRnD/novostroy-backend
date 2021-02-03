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

$this->registerCss('
input[type="checkbox"][readonly] {
  pointer-events: none;
}
');
?>

<div class="row">
    <div class="col-xs-12">
        <h2 class="page-header">Роли</h2>
    </div>
</div>
<?= Html::beginForm(['settings/update-roles-access', 'post']); ?>
<?php Pjax::begin(['id' => 'pjax-grid-view']); ?>
<div class="box">
    <?=
    GridView::widget([
        'dataProvider' => $provider,
        'filterModel' => null,
        'options' => [
            'style' => 'overflow:auto;'
        ],
        'tableOptions' => [
            'class' => 'table table-bordered table-hover table-striped',
        ],
        'layout' => "<div class='box-header with-border'>
            <h3 class='box-title'>Список ролей</h3>
          "
        //.SelectPajaxPageSizeWidget::widget(['pageSize' => 1, 'pajaxId' => 'pjax-grid-view'])
        . "</div>\n<div class='box-body'>{items}</div>\n
        <div class='col-xs-12 text-center'>
        <div class='form-group'>
        <a href='#!' class='btn btn-default'>Отменить</a>
        <button type='submit' class='btn btn-success'>Сохранить</button>
                            </div></div>
        ",
        //<div style ='overflow:auto;'>{pager}",
        //.SelectPajaxPageSizeWidget::widget(['pageSize' => 1, 'pajaxId' => 'pjax-grid-view'])."</div>",
        'columns' => [
            ['label' => 'Роль',
                'format' => 'html',
                'value' => function ($model) {
                    $roleOptions = \common\models\User::getRoleOptions();
                    return isset($roleOptions[$model->id]) ? $roleOptions[$model->id] : $model->name;
                },
            ],
            [
                'header' => 'Статистика',
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'statistic',
                'options' => [
                    'uncheck' => 'empty'
                ],
                'checkboxOptions' => function($model) {
                    return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->statistic || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id];
                },
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Помещения',
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'flats',
                'options' => [
                    'uncheck' => 'empty'
                ],
                'checkboxOptions' => function($model) {
                    return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->flats || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id];
                },
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Касса',
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'payments',
                'options' => [
                    'uncheck' => true
                ],
                'checkboxOptions' => function($model) {
                    return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->payments || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id];
                },
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Покупатели',
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'clients',
                'options' => [
                    'uncheck' => true
                ],
                'checkboxOptions' => function($model) {
                    return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->clients || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id];
                },
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Aгенства',
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'agency',
                'options' => [
                    'uncheck' => true
                ],
                'checkboxOptions' => function($model) {
                    return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->agency || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id];
                },
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Объекты',
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'objects',
                'options' => [
                    'uncheck' => true
                ],
                'checkboxOptions' => function($model) {
                    return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->objects || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id];
                },
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            /*
              [
              'header' => 'Договора',
              'class' => 'yii\grid\CheckboxColumn',
              'name' => 'contracts',
              'options' => [
              'uncheck' => true
              ],
              'checkboxOptions' => function($model) { return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->contracts || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id]; },
              'headerOptions' => ['class' => 'text-center'],
              'contentOptions' => ['class' => 'text-center'],
              ],
             * 
             */
            [
                'header' => 'Настройки',
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'settings',
                'options' => [
                    'uncheck' => true
                ],
                'checkboxOptions' => function($model) {
                    return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->settings || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id];
                },
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
//        [
//            'header' => 'Приложение',
//            'class' => 'yii\grid\CheckboxColumn',
//            'name' => 'application',
//            'options' => [
//                'uncheck' => true
//            ],
//            'checkboxOptions' => function($model) { return ['value' => 'check_' . $model->id, 'readonly' => $model->name == 'admin', 'disabled' => $model->name == 'admin', 'checked' => $model->application || $model->name == 'admin', 'uncheck' => 'uncheck_' . $model->id]; },
//            'headerOptions' => ['class' => 'text-center'],
//            'contentOptions' => ['class' => 'text-center'],
//        ],
        ],
    ]);
    ?>
</div>
<?php Pjax::end(); ?>
<?= Html::endForm(); ?>
