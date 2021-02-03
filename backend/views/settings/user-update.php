<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\User */
/* @var $modelForm backend\models\UserForm */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['/settings/users']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_user-form', [
    'model' => $model,
    'modelForm' => $modelForm,
]) ?>
