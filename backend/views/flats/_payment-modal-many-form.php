<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Payment;
use kartik\date\DatePicker;
use yii\widgets\Pjax;

/* @var $flatModel \common\models\Flat */
/* @var $model \common\models\Payment */

$paymentNumberStart = Payment::find()->where(['flat_id' => $flatModel->id])->max('pay_number') + 1;
$payDate = date('Y-m-d', time());
$model->price_plan = 0;
?>

<?php
$form = ActiveForm::begin([
    'action' => ['flats/update-payments-many', 'id' => $flatModel->id],
    'id' => 'payment-many-form',
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Пакетное добавление платежей</h4>
</div>

<div class="modal-body">
    <div class="row payment-many-item">
        <div class="col-xs-2">
            <label>№</label>
        </div>
        <div class="col-xs-4">
            <label><?= $model->getAttributeLabel('pay_date') ?></label>
        </div>
        <div class="col-xs-4">
            <label><?= $model->getAttributeLabel('price_plan') ?></label>
        </div>
        <div class="col-xs-2">
            
        </div>
    </div>
    <div id="payments-many-container">
        <?php for ($k = 0; $k < 1; $k++) { ?>
            <?php 
            $model->pay_number = $paymentNumberStart; 
            $model->pay_date = Yii::$app->formatter->asDate($payDate);
            ?>
            <?= $this->render('_payment-modal-many-form-item', [
                'model' => $model,
                'flatModel' => $flatModel,
                'k' => $k,
            ]); ?>

            <?php 
            $paymentNumberStart++;
            $payDate = date('Y-m-d', strtotime('+1 month', strtotime($payDate)));
            ?>
        <?php } ?>
    </div>
    <div class="form-inline">
    <input type="text" name="form_count" value="1" class="form-control inline" style="width: 64px;" />
    <button type="button" class="btn btn-default add-payment-many-item">+ Добавить</button>
    <button type="button" class="btn btn-default calc-payments">Пересчитать</button>
    </div>
</div>

<div class="modal-footer text-center">
    <?= Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    <?= Html::submitButton('Cохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php 
$urlAjaxAddPaymentManyForm = Yii::$app->urlManager->createUrl(['/flats/ajax-get-payment-many-form', 'flat_id' => $flatModel->id, 'form_id' => '']);
$urlAjaxGetPaymentManyPrice = Yii::$app->urlManager->createUrl(['/flats/ajax-get-payment-many-price', 'flat_id' => $flatModel->id, 'count' => '']);
$this->registerJs(<<<JS
    var paymentManyFormId = {$paymentNumberStart};
    
    $(document).on('click', '.delete-payment-many-item', function(e) {
        e.preventDefault();
        $(this).parents('.payment-many-item').remove();
        paymentManyFormId--;
        return false;
    });
    
    $(document).on('click', '.add-payment-many-item', function(e) {
        e.preventDefault();
        var paymentId = $(this).attr('data-payment_id') || 0;
        var payDate = $('#payments-many-container').children('.payment-many-item').last().find('.pay-date').val();
        var formCount = $('input[name="form_count"]').val();
    
        $.get('{$urlAjaxAddPaymentManyForm}'+paymentManyFormId+'&pay_date='+payDate+'&count='+formCount, function(data) {
            paymentManyFormId = paymentManyFormId + parseInt(formCount);
            $('#payments-many-container').append(data.form);
        });
        
        return false;
    });
        
    $(document).on('click', '.calc-payments', function(e) {
        e.preventDefault();
        var paymentsCount = $('#payments-many-container').children('.payment-many-item').length;

        $.get('{$urlAjaxGetPaymentManyPrice}'+paymentsCount, function(data) {
            console.log(data);
            $('#payments-many-container').find('.pay-price_plan').val(data.price_plan);
        });
        
        return false;
    });
JS
);
