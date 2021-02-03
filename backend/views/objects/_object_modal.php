<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\House;
use kartik\select2\Select2;
use common\models\User;
use yii\helpers\ArrayHelper;

$model->userIds = ArrayHelper::getColumn($model->users, 'id');
$userOptions = User::getOptions([
    User::ROLE_MANAGER,
    User::ROLE_ACCOUNTANT,
    User::ROLE_DEFAULT,
    User::ROLE_FIN_DIRECTOR,
    User::ROLE_SALES_MANAGER,
    User::ROLE_VIEWER_FLAT,
]);
?>

<!-- Modal -->
<div class="modal fade" id="modal<?= $model->id ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                'action' => $model->isNewRecord ? ['objects/create'] : ['objects/update', 'id' => $model->id],
                'id' => 'house-form' . $model->id,
            ]);
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= $model->isNewRecord ? 'Новый объект' : 'Редактировать объект' ?></h4>
            </div>

            <div class="modal-body">
                <?= $form->field($model, 'name')->textInput(['id' => 'inputNameModal' . $model->id])->label($model->getAttributeLabel('name'), ['for' => 'inputNameModal' . $model->id]) ?>
                <?= $form->field($model, 'section')->textInput(['id' => 'inputSectionModal' . $model->id])->label($model->getAttributeLabel('section'), ['for' => 'inputSectionModal' . $model->id]) ?>
                <?= $form->field($model, 'address')->textInput(['id' => 'inputAddressModal' . $model->id])->label($model->getAttributeLabel('address'), ['for' => 'inputAddressModal' . $model->id]) ?>
                <?= $form->field($model, 'company_name')->textInput(['id' => 'inputCompanyNameModal' . $model->id])->label($model->getAttributeLabel('company_name'), ['for' => 'inputCompanyNameModal' . $model->id]) ?>
                <?= $form->field($model, 'status')->dropDownList(House::getStatusOptions(), ['id' => 'inputStatusModal' . $model->id])->label($model->getAttributeLabel('status'), ['for' => 'inputStatusModal' . $model->id]) ?>
                <!--
                <div class="row">
                    <div class="col-sm-6">
                        <?php // echo $form->field($model, 'commission_agency')->textInput(['id' => 'inputCommissionAgencyModal' . $model->id, 'type' => 'number', 'step' => 'any'])->label($model->getAttributeLabel('commission_agency'), ['for' => 'inputCommissionAgencyModal' . $model->id]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?php // echo $form->field($model, 'commission_manager')->textInput(['id' => 'inputCommissionManagerModal' . $model->id, 'type' => 'number', 'step' => 'any'])->label($model->getAttributeLabel('commission_manager'), ['for' => 'inputCommissionManagerModal' . $model->id]) ?>
                    </div>
                </div>
                -->
                <?= $form->field($model, 'userIds')->widget(Select2::class, [
                    'data' => $userOptions,
                    'options' => ['id'=> 'userIds' . $model->id, 'placeholder' => 'Выберите...', 'multiple' => true],
                    'pluginOptions' => [
                        'tags' => true,
                    ],
                ]); ?>
            </div>
            
            <div class="modal-footer text-center">
                <?= Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
                <?= Html::submitButton('Cохранить', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- Modal -->
