<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\SystemConfig */

$this->title = 'Переменная ' . $model->key;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Переменные'), 'url' => ['variables']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_variable-form', [
    'model' => $model,
]) ?>
