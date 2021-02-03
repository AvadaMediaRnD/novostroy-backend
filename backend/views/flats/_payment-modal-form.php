<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Payment;
use kartik\date\DatePicker;
use yii\widgets\Pjax;

/* @var $flatModel \common\models\Flat */
/* @var $model \common\models\Payment */

?>

<?php
$form = ActiveForm::begin([
    'action' => ['flats/update', 'id' => $flatModel->id, 'payment_id' => $model->id],
    'id' => 'payment-form',
    'options' => ['data-pjax' => true],
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title"><?= $model->isNewRecord ? 'Новый платеж' : 'Редактировать платеж' ?></h4>
</div>

<div class="modal-body">
    <?= $form->field($model, 'pay_number')->textInput(['id' => 'inputPayNumberModal', 'type' => 'number', 'min' => 1, 'step' => 1])->label($model->getAttributeLabel('pay_number'), ['for' => 'inputPayNumberModal']) ?>
    <?php // $form->field($model, 'pay_date')->textInput(['id' => 'inputPayDateModal'])->label($model->getAttributeLabel('pay_date'), ['for' => 'inputPayDateModal']) ?>
    <?= $form->field($model, 'pay_date')->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_COMPONENT_PREPEND,
        'removeButton' => false,
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd.mm.yyyy'
        ],
        'options' => ['id' => 'inputPayDateModal'],
    ])->label($model->getAttributeLabel('pay_date'), ['for' => 'inputPayDateModal']) ?>
    <?= $form->field($model, 'price_plan')->textInput(['id' => 'inputPricePlanModal', 'type' => 'number', 'min' => 0, 'step' => 'any'])->label($model->getAttributeLabel('price_plan'), ['for' => 'inputPricePlanModal']) ?>
</div>

<div class="modal-footer text-center">
    <?= Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    <?= Html::submitButton('Cохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

