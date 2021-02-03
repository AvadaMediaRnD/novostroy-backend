<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\House;
use kartik\select2\Select2;
use common\models\Flat;
use common\models\Client;
use common\models\Agency;
use common\models\User;
use common\models\Payment;
use kartik\date\DatePicker;
use common\models\Article;
use common\models\Cashbox;
use common\models\Invoice;
use common\models\Rieltor;

/* @var $this yii\web\View */
/* @var $model \common\models\Invoice */

$articleOptions = ArrayHelper::map(Article::find()->where(['type' => $model->type])->all(), 'id', 'name');

$houseQuery = House::find();
$houseOptions = ArrayHelper::map($houseQuery->all(), 'id', 'nameSection');
if (isset($model->flat)) {
    $houseId = $model->flat->house_id;
    $flatQuery = Flat::find()->where(['house_id' => $model->flat->house_id]);
} else {
    $houseId = 0;
    $flatQuery = Flat::find()->where(['house_id' => 0]);
}


$flatOptions = ArrayHelper::map($flatQuery->all(), 'id', function ($model) {
            return $model->number . ', ' . $model->getUnitTypeLabel();
        });

$dateFilter = date("Y-m-d", strtotime("+1 month", strtotime(date('Y-m-01'))));
$paymentQuery = Payment::find()->select(['*', 'CONCAT("№ ", pay_number, " от ", pay_date) AS pay_numb'])->where(['flat_id' => $model->flat_id])->andWhere(['<','pay_date',$dateFilter]);

$paymentOptions = ArrayHelper::map($paymentQuery->asArray()->all(), 'id', 'pay_numb');

$cashboxQuery = Cashbox::find();
$cashboxOptions = ArrayHelper::map($cashboxQuery->all(), 'id', 'name');

$clientQuery = Client::find();
$clientOptions = ArrayHelper::map($clientQuery->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC])->all(), 'id', 'fullname');

$counterpartyOptions = [
    1 => 'Отдел продаж',
    2 => 'Агенство',
    3 => 'Директор',
    4 => 'Прочее'
];
$counterpartyType = null;
if ($model->type == Invoice::TYPE_OUTCOME) {
    if ($model->user && $model->user->role == User::ROLE_ADMIN) {
        $counterpartyType = 3;
    } elseif ($model->user && $model->user->role == User::ROLE_FIN_DIRECTOR) {
        $counterpartyType = 3;
    } elseif ($model->user && ($model->user->role == User::ROLE_SALES_MANAGER || $model->user->role == User::ROLE_MANAGER)) {
        $counterpartyType = 1;
    } elseif ($model->user && ($model->user->role == User::ROLE_ACCOUNTANT)) {
        $counterpartyType = 4;
    } elseif ($model->rieltor || $model->agency) {
        $counterpartyType = 2;
    }
}

$userQuery = User::find();
if ($counterpartyType == 1) {
    $userQuery->where(['in', 'role', [User::ROLE_SALES_MANAGER, User::ROLE_MANAGER]]);
} elseif ($counterpartyType == 3) {
    $userQuery->where(['in', 'role', [User::ROLE_ADMIN, User::ROLE_FIN_DIRECTOR]]);
} elseif ($counterpartyType == 4) {
    $userQuery->where(['in', 'role', [User::ROLE_ACCOUNTANT]]);
} else {
    $userQuery->where(['in', 'role', [User::ROLE_ADMIN, User::ROLE_FIN_DIRECTOR, User::ROLE_SALES_MANAGER]]);
}
$usersOptions = ArrayHelper::map($userQuery->all(), 'id', 'fullname');

$agencyQuery = Agency::find();
$agenciesOptions = ArrayHelper::map($agencyQuery->all(), 'id', function ($model) {
            return 'Агентство - ' . $model->name;
        });

$rieltorQuery = Rieltor::find();
$rieltorsOptions = ArrayHelper::map($rieltorQuery->all(), 'id', function ($model) {
            return 'Риелтор (' . $model->agency->name . ') - ' . $model->fullname;
        });
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-xs-12 col-md-7 col-lg-6">
        <div class="page-header-spec">
            <?=
            $form->field($model, 'uid', [
                'template' => '<div class="input-group">
                        <div class="input-group-addon">
                            №
                        </div>{input}
                    </div>',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
            ])->textInput()
            ?>
            <span>от</span>
            <?=
            $form->field($model, 'uid_date', ['template' => '{input}'])->widget(DatePicker::className(), [
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ]
            ])
            ?>
        </div>
    </div>
    <div class="col-xs-12 col-md-5 col-lg-6">
        <?php
        if (!$model->isNewRecord) {
            $invoiceType = (isset($model->type) && $model->type === 'outcome') ? 'out' : 'in';
            ?>
            <div class="btn-group pull-right margin-bottom">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Выберите действие <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/payments/create', 'invoice_id' => $model->id]) ?>">Копировать</a></li>
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/payments/delete', 'id' => $model->id]) ?>" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?">Удалить</a></li>
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/payments/print', 'id' => $model->id, 'type' => $invoiceType]) ?>">Распечатать в PDF</a></li>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php if ($model->type == Invoice::TYPE_OUTCOME) { ?>
                    <div class="form-group">
                        <label for="inputContragent">Контрагент</label>
                        <?php
                        echo Select2::widget([
                            'name' => 'counterpartyType',
                            'value' => $counterpartyType,
                            'data' => $counterpartyOptions,
                            'language' => 'ru',
                            'theme' => Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => 'Выберите...',
                                'class' => 'form-control',
                                'onchange' => '
                                    var val = $(this).val();
                                    $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/payments/ajax-get-counterparties', 'type' => ''])) . '"+$(this).val()+"&invoice_id=' . $model->id . '&agency_id=' . $model->agency_id . '&user_id=' . $model->user_id . '", function( data ) {
                                        $("select#invoice-agency_id").html(data.agencies).val(data.agencyId);
                                        $("select#invoice-rieltor_id").html(data.rieltors).val(data.rieltorId);
                                        $("select#invoice-user_id").html(data.users).val(data.userId);
                                        $(".userSelect, .agencySelect, .rieltorSelect").trigger("change").hide();

                                        if (val == 2) {
                                            $(".agencySelect").show();
                                            $(".rieltorSelect").show();
                                            $("select#invoice-user_id").val("");
                                        } else if (val == 1) {
                                            $(".userSelect").show().find("label").text("Менеджер");
                                            $("select#invoice-agency_id").val("");
                                            $("select#invoice-rieltor_id").val("");
                                        } else if (val == 3) {
                                            $(".userSelect").show().find("label").text("Директор");
                                            $("select#invoice-agency_id").val("");
                                            $("select#invoice-rieltor_id").val("");
                                        } else if (val == 4) {
                                            $(".userSelect").show().find("label").text("Пользователь");
                                            $("select#invoice-agency_id").val("");
                                            $("select#invoice-rieltor_id").val("");
                                        }
                                        
                                        console.log(data);
                                    });
                                ',
                            ],
                            'pluginOptions' => [
                                'minimumResultsForSearch' => -1,
                                'allowClear' => true
                            ],
                        ])
                        ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?php if ($model->type == Invoice::TYPE_OUTCOME) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <?php
                            echo $form->field($model, 'user_id', ['options' => ['class' => 'form-group userSelect']])->widget(Select2::className(), [
                                'data' => $usersOptions,
                                'language' => 'ru',
                                'theme' => Select2::THEME_DEFAULT,
                                'options' => [
                                    'placeholder' => 'Выберите...',
                                    'class' => 'form-control',
                                    'value' => $model->user_id ?? null,
                                    'onchange' => '

                                    '
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])
                            ?>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php
                            echo $form->field($model, 'agency_id', ['options' => ['class' => 'form-group agencySelect']])->widget(Select2::className(), [
                                'data' => $agenciesOptions,
                                'language' => 'ru',
                                'theme' => Select2::THEME_DEFAULT,
                                'options' => [
                                    'placeholder' => 'Выберите...',
                                    'class' => 'form-control',
                                    'value' => $model->agency_id ?? null,
                                    'onchange' => '
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/agency/ajax-get-items', 'agency_id' => ''])) . '"+$(this).val(), function( data ) {
                                            $("select#invoice-rieltor_id").html(data.rieltors);
                                            console.log(data);
                                        });
                                    '
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])
                            ?>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?php
                            echo $form->field($model, 'rieltor_id', ['options' => ['class' => 'form-group rieltorSelect']])->widget(Select2::className(), [
                                'data' => $rieltorsOptions,
                                'language' => 'ru',
                                'theme' => Select2::THEME_DEFAULT,
                                'options' => [
                                    'placeholder' => 'Выберите...',
                                    'class' => 'form-control',
                                    'value' => $model->rieltor_id ?? null,
                                    'onchange' => '

                                    '
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ])
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php
                echo $form->field($model, 'article_id')->widget(Select2::className(), [
                    'data' => $articleOptions,
                    'language' => 'ru',
                    'theme' => Select2::THEME_DEFAULT,
                    'options' => [
                        'placeholder' => 'Выберите...',
                        'class' => 'form-control',
                        'onchange' => '
                            
                        '
                    ],
                    'pluginOptions' => [
                        'minimumResultsForSearch' => -1,
                        'allowClear' => true
                    ],
                ])
                ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label for="house_id">Объект:</label>
                    <?php
                    echo Select2::widget([
                        'name' => 'house_id',
                        'value' => $houseId,
                        'data' => $houseOptions,
                        'language' => 'ru',
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => 'Выберите...',
                            'class' => 'form-control',
                            'onchange' => '
                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/objects/ajax-get-items', 'house_id' => ''])) . '"+$(this).val(), function( data ) {
                                    $("select#invoice-flat_id").html(data.flats);
                                });
                            ',
                        ],
                        'pluginOptions' => [
                            'minimumResultsForSearch' => -1,
                            'allowClear' => true
                        ],
                    ])
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php
                $flatLink = $model->flat ? ('(<a href="' . Yii::$app->urlManager->createUrl(['/flats/update', 'id' => $model->flat_id]) . '" target="_blank">посмотреть</a>)') : '';
                echo $form->field($model, 'flat_id', [
                    'template' => "{label} {$flatLink} {input}{hint}{error}"
                ])->widget(Select2::className(), [
                    'data' => $flatOptions,
                    'language' => 'ru',
                    'theme' => Select2::THEME_DEFAULT,
                    'options' => [
                        'placeholder' => 'Выберите...',
                        'class' => 'form-control',
                        'onchange' => '
                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flats/ajax-get-items', 'flat_id' => ''])) . '"+$(this).val(), function( data ) {
                                $("select#invoice-payment_id").html(data.payments);
                                $("select#invoice-client_id").val(data.clientId).trigger("change");
                                console.log(data);
                            });
                        '
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
                ?>

            </div>
            <div class="col-xs-12 col-sm-6">
                <?php if ($model->type == Invoice::TYPE_INCOME) { ?>
                    <?php
                    echo $form->field($model, 'payment_id')->widget(Select2::className(), [
                        'data' => $paymentOptions,
                        'language' => 'ru',
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => 'Выберите...',
                            'class' => 'form-control',
                            'onchange' => '
                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/payments/ajax-get-price', 'payment_id' => ''])) . '"+$(this).val(), function( data ) {
                                    $("input#invoice-price").val(data.price);
                                    console.log(data);
                                });
                            '
                        ],
                        'pluginOptions' => [
                            'minimumResultsForSearch' => -1,
                            'allowClear' => true
                        ],
                    ])
                    ?>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php if ($model->type == Invoice::TYPE_INCOME) { ?>
                    <?php
                    echo $form->field($model, 'client_id')->widget(Select2::className(), [
                        'data' => $clientOptions,
                        'language' => 'ru',
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => 'Выберите...',
                            'class' => 'form-control',
                            'onchange' => '

                            '
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
                    ?>
                <?php } ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'rate')->textInput(['type' => 'number', 'min' => 0, 'step' => '0.0001']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php
                echo $form->field($model, 'cashbox_id')->widget(Select2::className(), [
                    'data' => $cashboxOptions,
                    'language' => 'ru',
                    'theme' => Select2::THEME_DEFAULT,
                    'options' => [
                        'placeholder' => 'Выберите...',
                        'class' => 'form-control',
                        'onchange' => '
                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/payments/ajax-get-rate', 'cashbox_id' => ''])) . '"+$(this).val(), function( data ) {
                                $("input#invoice-rate").val(data.rate);
                                console.log(data);
                            });
                        '
                    ],
                    'pluginOptions' => [
                        'minimumResultsForSearch' => -1,
                        'allowClear' => true
                    ],
                ])
                ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'price', ['inputOptions' => ['class' => 'form-control']])->textInput(['type' => 'text', 'value' => str_replace(',', '', $model->price), 'oninput' => "up(this)"]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'company_name')->textInput() ?>
            </div>
            <div class="col-xs-12 col-sm-6">
                <?=
                $form->field($model, 'status')->checkbox([
                    'value' => Invoice::STATUS_COMPLETE,
                    'uncheck' => Invoice::STATUS_WAITING,
                    'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>',
                        ], false)->label('Проведена')
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/payments/index']) ?>" class="btn btn-default">Отменить</a>
                    <button type="submit" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

</div>
<?php ActiveForm::end(); ?>

<?php
$script = <<<JS
    $('select[name="counterpartyType"]').trigger('change');
    
    function up(e) {
        if (e.value.indexOf(".") != '-1') {
            e.value=e.value.substring(0, e.value.indexOf(".") + 3);
        }
        if (e.value.indexOf(",") != '-1') {
            e.value=e.value.substring(0, e.value.indexOf(",") + 3);
            e.value=e.value.replace(/,/g,".");
        }
    }
JS;

$this->registerJs($script, yii\web\View::POS_END);
?>
