<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Agreement */
/* @var $modelForm backend\models\AgreementForm */

$this->title = 'Редактирование договора';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Договора'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Договор №' . $model->uid, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<?= $this->render('_content-form', [
    'model' => $model,
]) ?>
