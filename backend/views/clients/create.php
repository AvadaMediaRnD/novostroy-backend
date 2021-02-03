<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Client */

$this->title = Yii::t('app', 'Новый покупатель');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Покупатели'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
