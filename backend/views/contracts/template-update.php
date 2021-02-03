<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\AgreementTemplate */
/* @var $modelForm backend\models\AgreementTemplateForm */

$this->title = Yii::t('app', 'Шаблон договора');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Договора'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Шаблоны договоров'), 'url' => ['template-index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_template-form', [
    'model' => $model,
    'modelForm' => $modelForm,
]) ?>