<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Agreement */
/* @var $modelForm backend\models\AgreementForm */

$this->title = Yii::t('app', 'Договор');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Договора'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Оформить договор';
?>

<?= $this->render('_form', [
    'model' => $model,
    'modelForm' => $modelForm,
]) ?>
