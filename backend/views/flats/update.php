<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Flat */
/* @var $modelForm backend\models\FlatForm */

$this->title = 'Карточка помещения' . 
    ($model->number ? (': №' . $model->number . ($model->house->name ? (', ' . $model->house->getNameSection()) : '')) : '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Помещения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'modelForm' => $modelForm,
    'isPaid' => $isPaid,
    'isPlan' => $isPlan
]) ?>
