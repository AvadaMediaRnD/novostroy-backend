<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Client */

$this->title = 'Карточка покупателя: ' . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Покупатели'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
