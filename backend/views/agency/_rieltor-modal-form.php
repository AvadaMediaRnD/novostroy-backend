<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Rieltor;
use kartik\date\DatePicker;
use yii\widgets\Pjax;

/* @var $agencyModel \common\models\Agency */
/* @var $model \common\models\Rieltor */

?>

<?php
$form = ActiveForm::begin([
    'action' => ['agency/update', 'id' => $agencyModel->id, 'rieltor_id' => $model->id],
    'id' => 'rieltor-form',
    'options' => ['data-pjax' => true],
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title"><?= $model->isNewRecord ? 'Новый риелтор' : 'Редактировать риелтора' ?></h4>
</div>

<div class="modal-body">
    <?= $form->field($model, 'firstname')->textInput(['id' => 'inputFirstNameModal'])->label($model->getAttributeLabel('firstname'), ['for' => 'inputFirstNameModal']) ?>
    <?= $form->field($model, 'lastname')->textInput(['id' => 'inputLastNameModal'])->label($model->getAttributeLabel('lastname'), ['for' => 'inputLastNameModal']) ?>
    <?= $form->field($model, 'phone')->textInput(['id' => 'inputPhoneModal'])->label($model->getAttributeLabel('phone'), ['for' => 'inputPhoneModal']) ?>
    <?= $form->field($model, 'email')->textInput(['id' => 'inputEmailModal'])->label($model->getAttributeLabel('email'), ['for' => 'inputEmailModal']) ?>
    <?= $form->field($model, 'is_director')->checkbox(['id' => 'inputIsDirectorModal'])->label(false, ['for' => 'inputIsDirectorModal']) ?>
</div>

<div class="modal-footer text-center">
    <?= Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    <?= Html::submitButton('Cохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

