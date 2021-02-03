<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\House;
use kartik\select2\Select2;
use common\models\Flat;
use common\helpers\PriceHelper;
use common\models\Client;
use common\models\Agency;
use common\models\User;
use kartik\date\DatePicker;
use common\models\Agreement;
use common\models\AgreementTemplate;

/* @var $this yii\web\View */
/* @var $model \common\models\Agreement */
/* @var $modelForm backend\models\AgreementForm */
/* @var $paymentDataProvider yii\data\ActiveDataProvider */

$flatQuery = Flat::find()->orderBy(['number' => SORT_ASC]);
$flatOptions = ArrayHelper::map($flatQuery->all(), 'id', 'number');

$houseQuery = House::find()->orderBy(['name' => SORT_ASC]);
$houseOptions = ArrayHelper::map($houseQuery->all(), 'id', 'nameSection');
$flatModel = Flat::findOne($modelForm->flat_id);
$houseId = $flatModel->house_id;

$clientQuery = Client::find();
$clientOptions = ArrayHelper::map($clientQuery->all(), 'id', 'fullname');

$templateQuery = AgreementTemplate::find();
$templateOptions = ArrayHelper::map($templateQuery->all(), 'id', 'name');

?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="row">
    <div class="col-xs-12 col-md-7 col-lg-6">
        <div class="page-header-spec">
            <?= $form->field($modelForm, 'uid', [
                'template' => '<div class="input-group">
                        <div class="input-group-addon">
                            №
                        </div>{input}
                    </div>',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
            ])->textInput() ?>
            <span>от</span>
            <?= $form->field($modelForm, 'uid_date', ['template' => '{input}'])->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ]
            ]) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $model->isNewRecord ? 'Оформление договора' : 'Редактирование договора'; ?></h3>
            </div>
            <div class="box-body">
                
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#info">Информация</a></li>
                    <li><a data-toggle="tab" href="#ukr">Корректировать поля в договоре</a></li>
                </ul>
                <div class="tab-content">
                    <div id="info" class="tab-pane fade in active">
                        <div class="margin-top-15"></div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <?php echo $form->field($modelForm, 'flat_id', ['options' => ['class' => 'form-group userSelect']])->widget(Select2::className(), [
                                    'data' => $flatOptions,
                                    'language' => 'ru',
                                    'theme' => Select2::THEME_DEFAULT,
                                    'options' => [
                                        'placeholder' => 'Выберите...', 
                                        'class' => 'form-control',
                                        'onchange'=>'
                                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flats/ajax-get-items', 'flat_id' => ''])) . '"+$(this).val(), function (data) {
                                                $("select#agreementform-client_id").html(data.clients).trigger("change");
                                            });
                                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flats/ajax-get-flat', 'id' => ''])) . '"+$(this).val(), function (data) {
                                                updateFlatData(data);
                                            });
                                        '
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]) ?>
                                <?php echo $form->field($modelForm, 'client_id', ['options' => ['class' => 'form-group userSelect']])->widget(Select2::className(), [
                                    'data' => $clientOptions,
                                    'language' => 'ru',
                                    'theme' => Select2::THEME_DEFAULT,
                                    'options' => [
                                        'placeholder' => 'Выберите...', 
                                        'class' => 'form-control',
                                        'onchange'=>'
                                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/clients/ajax-get-client', 'id' => ''])) . '"+$(this).val(), function (data) {
                                                updateClientData(data);
                                            });
                                        '
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]) ?>
                                
                                
                                    
                                <?php echo $form->field($modelForm, 'agreement_template_id', ['options' => ['class' => 'form-group userSelect']])->widget(Select2::className(), [
                                    'data' => $templateOptions,
                                    'language' => 'ru',
                                    'theme' => Select2::THEME_DEFAULT,
                                    'options' => [
                                        'placeholder' => 'Выберите...', 
                                        'class' => 'form-control',
                                        'onchange'=>'

                                        '
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])->hint('При смене шаблона будет перезаписано содержимое файла договора') ?>
                                
                                <?= $form->field($modelForm, 'is_refresh')->checkbox() ?>
                                
                                <div class="row">
                                    <div class="col-xs-6">
                                        <?= $form->field($modelForm, 'scan_file')->fileInput()->label('Загрузить скан договора') ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?php if ($model->scan_file && file_exists(Yii::getAlias('@frontend/web' . $model->scan_file))) { ?>
                                            <a class="pull-right" target="_blank" href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl($model->scan_file) ?>">
                                                Посмотреть скан <i class="fa fa-external-link"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($modelForm, 'agreement_file')->fileInput()->label('Загрузить файл договора вручную') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label for="inputContragent">Объект</label>
                                    <?php echo Select2::widget([
                                        'name' => 'house_id',
                                        'value' => $houseId,
                                        'data' => $houseOptions,
                                        'language' => 'ru',
                                        'theme' => Select2::THEME_DEFAULT,
                                        'options' => [
                                            'placeholder' => 'Выберите...', 
                                            'class' => 'form-control',
                                            'onchange' => '
                                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/objects/ajax-get-items', 'house_id' => ''])) . '"+$(this).val(), function (data) {
                                                    $("select#agreementform-flat_id").html(data.flats);
                                                });
                                            ',
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ]) ?>
                                </div>

                                <?php echo $form->field($modelForm, 'status', ['options' => ['class' => 'form-group userSelect']])->widget(Select2::className(), [
                                    'data' => Agreement::getStatusOptions(),
                                    'language' => 'ru',
                                    'theme' => Select2::THEME_DEFAULT,
                                    'options' => [
                                        'placeholder' => 'Выберите...', 
                                        'class' => 'form-control',
                                    ],
                                    'pluginOptions' => [
                                        'minimumResultsForSearch' => -1,
                                        'allowClear' => true
                                    ],
                                ]) ?>

                                <?php if (!$model->isNewRecord && $model->file) { ?>
                                    <?php /* <div class="form-group">
                                        <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/update-content', 'id' => $model->id]) ?>" class="btn btn-default" style="margin-top: 24px;">Редактировать договор</a>
                                    </div> */ ?>

                                    <div class="form-group">
                                        <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/print', 'id' => $model->id, 'format' => 'docx']) ?>" class="btn btn-default" style="margin-top: 24px;">Распечатать в DOCX</a>
                                        <?php /*
                                        <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/print', 'id' => $model->id, 'format' => 'pdf']) ?>" class="btn btn-default" style="margin-top: 24px;">Распечатать в PDF</a>
                                        */ ?>
                                    </div>
                                <?php } else { ?>
                                    <p><i class="fa fa-warning text-orange"></i> Для печати договора, выберите шаблон и сохраните данные</p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <h4>Реквизиты покупателя:</h4>
                                <?= $form->field($modelForm, 'lastname')->textInput() ?>
                                <?= $form->field($modelForm, 'firstname')->textInput() ?>
                                <?= $form->field($modelForm, 'middlename')->textInput() ?>
                                <?= $form->field($modelForm, 'address')->textInput() ?>
                                <?= $form->field($modelForm, 'birthdate')->widget(DatePicker::className(), [
                                    'type' => DatePicker::TYPE_INPUT,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd.mm.yyyy'
                                    ]
                                ]) ?>
                                <?= $form->field($modelForm, 'inn')->textInput() ?>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <h4>Помещение:</h4>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <?= $form->field($modelForm, 'number')->textInput(['type' => 'number', 'min' => 0]) ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?= $form->field($modelForm, 'number_index')->textInput() ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <?= $form->field($modelForm, 'unit_type')->widget(Select2::class, [
                                            'data' => Flat::getUnitTypeOptions(),
                                            'language' => 'ru',
                                            'theme' => Select2::THEME_DEFAULT,
                                            'options' => [
                                                'placeholder' => 'Выберите...', 
                                                'class' => 'form-control',
                                            ],
                                            'pluginOptions' => [
                                                'minimumResultsForSearch' => -1, 
                                                'dropdownAutoWidth' => true,
                                                'allowClear' => true
                                            ],
                                        ]) ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?= $form->field($modelForm, 'square')->textInput()->label('Площадь (м<sup>2</sup>)', ['encodeLabel' => 'false']) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <?= $form->field($modelForm, 'floor')->textInput() ?>
                                    </div>
                                    <div class="col-xs-6">
                                        <?= $form->field($modelForm, 'n_rooms')->textInput() ?>
                                    </div>
                                </div>
                                <?= $form->field($modelForm, 'flat_address')->textInput() ?>
                                <?= $form->field($modelForm, 'price')->textInput() ?>
                            </div>
                        </div>
                        <h4>Паспортные данные:</h4>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">
                                <?= $form->field($modelForm, 'passport_series')->textInput() ?>
                            </div>
                            <div class="col-xs-12 col-sm-3">
                                <?= $form->field($modelForm, 'passport_number')->textInput() ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <?= $form->field($modelForm, 'passport_from')->textInput() ?>
                            </div>
                        </div>
                        <h4>Контакты:</h4>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <?= $form->field($modelForm, 'phone')->textInput() ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <?= $form->field($modelForm, 'email')->textInput() ?>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Примечание:</h3>
                            </div>
                            <div class="box-body pad">
                                <?= $form->field($modelForm, 'description')->textarea(['rows' => 8, 'class' => 'compose-textarea editor-init form-control'])->label(false) ?>
                            </div>
                        </div>
                        
                    </div>
                    <div id="ukr" class="tab-pane fade">
                        <div class="margin-top-15"></div>
                        
                        <div class="row">
                            <div class="col-xs-6">
                                <?= $form->field($modelForm, 'tpl_house_address')->textInput() ?>
                            </div>
                            <div class="col-xs-6">
                                <?= $form->field($modelForm, 'tpl_client_fullname')->textInput() ?>
                            </div>
                            <div class="col-xs-6">
                                <?= $form->field($modelForm, 'tpl_client_fullname_short')->textInput() ?>
                            </div>
                            <div class="col-xs-6">
                                <?= $form->field($modelForm, 'tpl_client_birthdate_text')->textInput() ?>
                            </div>
                            <div class="col-xs-6">
                                <?= $form->field($modelForm, 'tpl_client_passport_from')->textInput() ?>
                            </div>
                            <div class="col-xs-12">
                                <?= $form->field($modelForm, 'plan_image')->fileInput()->label('План квартиры (880х500)') ?>
                                
                                <?php if ($model->getPlanImage()) { ?>
                                    <a target="_blank" href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl($model->getPlanImage()) ?>">
                                        Посмотреть план <i class="fa fa-external-link"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <?php if (!$model->isNewRecord) { ?>
                    <?php /*<div class="row">
                        <div class="col-xs-12">
                            <div class="form-group text-right">
                                <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/print', 'id' => $model->id, 'format' => 'docx']) ?>" class="btn btn-default">Распечатать в DOCX</a>
                                
                                <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/print', 'id' => $model->id, 'format' => 'pdf']) ?>" class="btn btn-default">Распечатать в PDF</a>
                                
                            </div>
                        </div>
                    </div>*/ ?>
                <?php } ?>
                <div class="row">
                    <div class="col-xs-12 text-right">
                        <div class="form-group">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/index']) ?>" class="btn btn-default margin-bottom-15">Отменить</a>
                            <button type="submit" class="btn btn-success margin-bottom-15">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->registerJs(<<<JS
    
    function updateClientData(data) {
        if (data) {
            $('#agreementform-firstname').val(data.firstname);
            $('#agreementform-middlename').val(data.middlename);
            $('#agreementform-lastname').val(data.lastname);
            $('#agreementform-address').val(data.address);
            $('#agreementform-inn').val(data.inn);
            $('#agreementform-passport_series').val(data.passport_series);
            $('#agreementform-passport_number').val(data.passport_number);
            $('#agreementform-passport_from').val(data.passport_from);
            $('#agreementform-phone').val(data.phone);
            $('#agreementform-email').val(data.email);
        }
    }
    
    function updateFlatData(data) {
        if (data) {
            $('#agreementform-number').val(data.number);
            $('#agreementform-square').val(data.square);
            $('#agreementform-floor').val(data.floor);
            $('#agreementform-flat_address').val(data.address);
            $('#agreementform-price').val(data.price);
        }
    }
    
    $(function () {
        //Add text editor
        function addTextEditor() {
            $("textarea.compose-textarea.editor-init").wysihtml5({
                locale: 'ru-RU',
                toolbar: {
                    "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                    "emphasis": true, //Italics, bold, etc. Default true
                    "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                    "html": false, //Button which allows you to edit the generated HTML. Default false
                    "link": false, //Button to insert a link. Default true
                    "image": false, //Button to insert an image. Default true,
                    "color": false, //Button to change color of font
                    "blockquote": false, //Blockquote
                    "fa": true,
                    "size": 'none' //default: none, other options are xs, sm, lg
                }
            }).removeClass('editor-init');
        }
        
        addTextEditor();
    
    });
    
JS
, \yii\web\View::POS_END);

if ($model->isNewRecord) {
    $this->registerJs(<<<JS
    
    $('#agreementform-client_id').trigger('change');
    
JS
    );
}
