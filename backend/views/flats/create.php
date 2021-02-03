<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Flat */
/* @var $modelForm backend\models\FlatForm */

$this->title = Yii::t('app', 'Новое помещение');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Помещения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?=

$this->render('_form', [
    'model' => $model,
    'modelForm' => $modelForm,
    'isPaid' => $isPaid,
    'isPlan' => $isPlan
])
?>
