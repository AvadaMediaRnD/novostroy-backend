<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Agency */

$this->title = Yii::t('app', 'Новое агентство');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Агентства'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
