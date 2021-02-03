<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Rieltor;
use kartik\date\DatePicker;
use yii\widgets\Pjax;
use common\models\User;
use kartik\select2\Select2;

/* @var $agencyModel \common\models\Agency */
/* @var $modelForm backend\models\UserForm */

?>

<?php
$form = ActiveForm::begin([
    'action' => ['settings/user-create'],
    'id' => 'user-add-form',
    'options' => ['enctype' => 'multipart/form-data', 'data-pjax' => true],
]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Создать пользователя</h4>
</div>

<div class="modal-body">
    <?= $form->field($modelForm, 'firstname')->textInput(['id' => 'inputFirstNameModal'])->label($modelForm->getAttributeLabel('firstname'), ['for' => 'inputFirstNameModal']) ?>
    <?= $form->field($modelForm, 'lastname')->textInput(['id' => 'inputLastNameModal'])->label($modelForm->getAttributeLabel('lastname'), ['for' => 'inputLastNameModal']) ?>
    <?= $form->field($modelForm, 'role')->widget(Select2::class, [
        'data' => User::getRoleOptions(),
        'language' => 'ru',
        'theme' => Select2::THEME_DEFAULT,
        'options' => [
            'id' => 'inputRoleModal',
            'placeholder' => 'Выберите...', 
            'class' => 'form-control',
        ],
        'pluginOptions' => [
            'minimumResultsForSearch' => -1, 
            'dropdownAutoWidth' => true,
            'allowClear' => true
        ],
    ])->label($modelForm->getAttributeLabel('role'), ['for' => 'inputRoleModal']) ?>
    <?= $form->field($modelForm, 'phone')->textInput(['id' => 'inputPhoneModal'])->label($modelForm->getAttributeLabel('phone'), ['for' => 'inputPhoneModal']) ?>
    <?= $form->field($modelForm, 'email', ['enableAjaxValidation' => true])->textInput(['id' => 'inputEmailModal'])->label($modelForm->getAttributeLabel('email'), ['for' => 'inputEmailModal']) ?>
</div>

<div class="modal-footer text-center">
    <?= Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    <?= Html::submitButton('Cохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

