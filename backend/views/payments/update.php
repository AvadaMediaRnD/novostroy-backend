<?php

use yii\helpers\Html;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $model \common\models\Invoice */

$this->title = Yii::t('app', $model->type == Invoice::TYPE_INCOME ? 'Приходная ведомость' : 'Расходная ведомость') . ' №' . $model->uid;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Касса'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
