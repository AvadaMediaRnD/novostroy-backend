<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\House;
use kartik\select2\Select2;
use common\models\Flat;
use common\helpers\PriceHelper;
use common\models\Client;
use common\models\Agency;
use common\models\User;
use common\models\Payment;
use backend\models\PaymentSearch;
use backend\models\InvoiceSearch;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $model \common\models\Flat */
/* @var $modelForm backend\models\FlatForm */
/* @var $paymentDataProvider yii\data\ActiveDataProvider */

$searchModel = new PaymentSearch();
$searchModel->flat_id = $model->id;
$paymentDataProvider = $searchModel->search(Yii::$app->request->queryParams);
$nextPayment = Payment::find()
        ->select('id')
        ->where(['and', ['flat_id' => $model->id], ['is not', 'flat_id', null]])
        ->andWhere(['>=', 'pay_date', date('Y-m-d', time())])
        ->orderBy(['pay_date' => SORT_ASC])
        ->limit(1)
        ->one();

$searchModelInvoice = new InvoiceSearch();
$searchModelInvoice->flat_id = $model->id;
$invoiceDataProvider = $searchModelInvoice->search(Yii::$app->request->queryParams);
$invoiceDataProvider->query->andWhere(['or',
    ['is not', 'invoice.agency_id', null],
    ['is not', 'invoice.user_id', null],
]);
$invoiceDataProvider->query->andWhere(['type' => Invoice::TYPE_OUTCOME]);
?>

<?php $form = ActiveForm::begin(); ?>

<?php $tab = Yii::$app->session->get('tab_id', '#tab_1'); ?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li <?php if (!isset($tab) || empty($tab) || $tab === '#tab_1') { ?>class="active" <?php } ?>><a href="#tab_1" data-toggle="tab" aria-expanded="true">Описание</a></li>
                        <?php if ($model->isNewRecord) { ?>
                            <li class="disabled"><a href="#!" data-toggle="tooltip" data-placement="top" title="Для добавления платежей сохраните квартиру" aria-expanded="false">График платежей</a></li>
                        <?php } else { ?>
                            <li <?php if (isset($tab) && $tab === '#tab_2') { ?>class="active" <?php } ?>><a href="#tab_2" data-toggle="tab" aria-expanded="false">График платежей</a></li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane <?php
                        if (!isset($tab) || empty($tab) || $tab === '#tab_1') {
                            echo 'active';
                        }
                        ?> clearfix" id="tab_1">

                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                    $form->field($modelForm, 'status')->widget(Select2::class, [
                                        'data' => Flat::getStatusOptions(),
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
                                    ])
                                    ?>
                                </div>
                                <div class="col-xs-6 col-sm-3">
                                    <?= $form->field($modelForm, 'number')->textInput(['type' => 'number', 'min' => 0]) ?>
                                </div>
                                <div class="col-xs-6 col-sm-3">
                                    <?= $form->field($modelForm, 'number_index')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                    $form->field($modelForm, 'unit_type')->widget(Select2::class, [
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
                                    ])
                                    ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                    $form->field($modelForm, 'house_id')->widget(Select2::class, [
                                        'data' => House::getOptions(),
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
                                    ])
                                    ?>
                                    <?=
                                    $form->field($modelForm, 'n_rooms')->hiddenInput()->label(false)
                                    ?>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                    $form->field($modelForm, 'floor')->widget(Select2::class, [
                                        'data' => [-2 => -2, -1 => -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
                                        'language' => 'ru',
                                        'theme' => Select2::THEME_DEFAULT,
                                        'options' => [
                                            'placeholder' => 'Выберите...',
                                            'class' => 'form-control no-fade',
                                        ],
                                        'pluginOptions' => [
                                            'minimumResultsForSearch' => -1,
                                            'dropdownAutoWidth' => true,
                                            'allowClear' => true
                                        ],
                                    ])
                                    ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'square')->textInput(['type' => 'number', 'min' => 0, 'max' => 999, 'step' => 'any', 'class' => 'form-control no-fade nosquare-hide']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                            $form->field($modelForm, 'price_m')->textInput(['type' => 'number', 'min' => 0, 'step' => 'any', 'class' => 'form-control no-fade nosquare-hide', 'readonly' => $isPlan])
                                            ->label('Цена за м<sup>2</sup> (USD)')
                                    ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'price_total')->textInput(['id' => 'priceTotal', 'type' => 'number', 'min' => 0, 'step' => 'any', 'class' => 'form-control no-fade nosquare-show', 'readonly' => $isPlan]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                            $form->field($modelForm, 'price_sell_m')->textInput(['type' => 'number', 'min' => 0, 'step' => 'any', 'class' => 'form-control no-fade nosquare-hide', 'readonly' => $isPlan])
                                            ->label('Цена продажи за м<sup>2</sup> (USD)')
                                    ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'price_sell_total')->textInput(['id' => 'priceSellTotal', 'type' => 'number', 'min' => 0, 'step' => 'any', 'class' => 'form-control no-fade nosquare-show', 'readonly' => $isPlan]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                            $form->field($modelForm, 'price_discount_m')->textInput(['type' => 'number', 'step' => 'any', 'class' => 'form-control no-fade nosquare-hide', 'readonly' => true])
                                            ->label('Скидка за м<sup>2</sup> (USD)')
                                    ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'price_discount_total')->textInput(['id' => 'priceDiscountTotal', 'type' => 'number', 'step' => 'any', 'class' => 'form-control no-fade nosquare-show', 'readonly' => true]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">

                                    <?=
                                            $form->field($modelForm, 'price_paid_init')
                                            ->textInput(['type' => 'number', 'min' => 0, 'step' => 'any', 'class' => 'form-control no-fade', 'readonly' => $isPlan])
                                    ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                            $form->field($modelForm, 'price_paid_out')
                                            ->textInput(['type' => 'number', 'min' => 0, 'step' => 'any', 'class' => 'form-control no-fade', 'readonly' => $isPlan])
//->hint('Сумма, которая не будет учтена в расчетах платежей')
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'commission_manager')->textInput(['type' => 'number', 'min' => 0, 'step' => 'any']) ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'commission_manager_type')->dropDownList(Flat::getCommissionTypeOptions()) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'commission_agency')->textInput(['type' => 'number', 'min' => 0, 'step' => 'any']) ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($modelForm, 'commission_agency_type')->dropDownList(Flat::getCommissionTypeOptions()) ?>
                                </div>
                            </div>

                            <?php if (!$model->isNewRecord) { ?>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6">
                                        <?=
                                        $form->field($modelForm, 'client_id')->widget(Select2::class, [
                                            'data' => Client::getOptions(),
                                            'language' => 'ru',
                                            'theme' => Select2::THEME_DEFAULT,
                                            'options' => [
                                                'placeholder' => 'Выберите...',
                                                'class' => 'form-control',
                                                'onchange' => "
                                                    $.pjax.reload({
                                                        container: '#selectsAgencyUser', 
                                                        async: false,
                                                        data: 'client_id='+$(this).val(),
                                                        replace: false,
                                                    });
                                                "
                                            ],
                                            'pluginOptions' => [
                                                // 'minimumResultsForSearch' => -1, 
                                                'dropdownAutoWidth' => true,
                                                'allowClear' => true,
                                                'tags' => true,
                                            ],
                                        ])
                                        ?>

                                        <?php Pjax::begin(['id' => 'selectsAgencyUser']) ?>
                                        <?php
                                        $client = $model->client;
                                        if (Yii::$app->request->isPjax) {
                                            $client = Client::findOne((int) Yii::$app->request->get('client_id'));
                                            $modelForm->user_id = $client->user_id;
                                            $modelForm->agency_id = $client->agency_id;
                                        }
                                        $disableSelects = $client ? true : false;
                                        ?>

                                        <?=
                                        $form->field($modelForm, 'agency_id')->widget(Select2::class, [
                                            'data' => Agency::getOptions(),
                                            'language' => 'ru',
                                            'theme' => Select2::THEME_DEFAULT,
                                            'options' => [
                                                'id' => 'selectAgency',
                                                'placeholder' => 'Не указано...',
                                                'class' => 'form-control',
                                            ],
                                            'pluginOptions' => [
                                                // 'minimumResultsForSearch' => -1, 
                                                'dropdownAutoWidth' => true,
                                                'allowClear' => true,
                                            ],
                                        ])
                                        ?>

                                        <?=
                                        $form->field($modelForm, 'user_id')->widget(Select2::class, [
                                            'data' => User::getOptions([User::ROLE_MANAGER, User::ROLE_SALES_MANAGER]),
                                            'language' => 'ru',
                                            'theme' => Select2::THEME_DEFAULT,
                                            // 'disabled' => $disableSelects && $client->user_id,
                                            'options' => [
                                                'id' => 'selectUser',
                                                'placeholder' => 'Не указано...',
                                                'class' => 'form-control',
                                            ],
                                            'pluginOptions' => [
                                                // 'minimumResultsForSearch' => -1, 
                                                'dropdownAutoWidth' => true,
                                                'allowClear' => false,
                                            ],
                                        ])
                                        ?>

                                        <?php Pjax::end() ?>

                                    </div>

                                    <?php if (!$model->isNewRecord) { ?>
                                        <?php if ($model->agreement) { ?>
                                            <!--
                                                <div class="col-xs-12 col-sm-6">
                                                    <div>Статус: <b><?php // echo $model->agreement->getStatusLabel()          ?></b></div>
                                                    <div class="dashed-box">№ <span><?= $model->agreement->uid ?></span> от <span><?= $model->agreement->getUidDate() ?></span></div>
                                                    <div class="margin-bottom-15">
                                                        <div class="display-block">
                                                            <a href="<?php // echo Yii::$app->urlManager->createUrl(['/contracts/update', 'id' => $model->agreement->id])          ?>" class="margin-right-15"><u>Договор</u></a> 
                                            <?php if ($model->agreement->scan_file) { ?>
                                                                                                    <a target="_blank" href="<?php // echo Yii::$app->urlManagerFrontend->createAbsoluteUrl($model->agreement->scan_file)          ?>"><u>Скан договора</u></a>
                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            -->
                                        <?php } else { ?>
                                            <!--
                                                <div class="col-xs-12 col-sm-6">
                                                    <div>Договор не оформлен</div>
                                            <?php if ($model->client) { ?>
                                                                                            <div class="margin-bottom-15">
                                                                                                <a href="<?php // echo Yii::$app->urlManager->createUrl(['/contracts/create', 'flat_id' => $model->id])        ?>" style="margin-top: 5px;" class="btn btn-default">Оформить договор</a>
                                                                                            </div>
                                            <?php } else { ?>
                                                                                            <div class="margin-bottom-15">
                                                                                                <a href="#!" style="margin-top: 5px;" class="btn btn-default disabled">Оформить договор</a>
                                                                                            </div>
                                                                                            <p><i class="fa fa-warning text-orange"></i> Для оформления договора установите покупателя квартиры</p>
                                            <?php } ?>
                                                </div>
                                            -->
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if (!$model->isNewRecord) { ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="box">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Комиссионные</h3>
                                                <?php
                                                if ($model->client) {
                                                    if (isset($modelForm->agency_id)) {
                                                        $agencyId = $modelForm->agency_id;
                                                    } else {
                                                        $agencyId = $model->client->agency_id;
                                                    }
                                                    if (isset($modelForm->user_id)) {
                                                        $userId = $modelForm->user_id;
                                                    } else {
                                                        $userId = $model->client->user_id;
                                                    }
                                                    ?> 
                                                    <div class="box-tools">
                                                        <a href="<?= Yii::$app->urlManager->createUrl(['/flats/create-commissions', 'flat_id' => $model->id, 'user_id' => $userId, 'agency_id' => $agencyId]) ?>" class="btn btn-default btn-sm">
                                                            <span class="hidden-xs">Выплатить комиссионные</span><i class="fa fa-money visible-xs" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <?php if ($model->client) { ?>
                                                <?php Pjax::begin(['id' => 'invoices']) ?>
                                                <?php
                                                echo GridView::widget([
                                                    'dataProvider' => $invoiceDataProvider,
                                                    'filterModel' => null,
                                                    'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap'],
                                                    'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                                                    'pager' => [
                                                        'class' => 'yii\widgets\LinkPager',
                                                        'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                                                    ],
                                                    'footerRowOptions' => ['style' => 'font-weight: bold'],
                                                    'columns' => [
                                                        [
                                                            'attribute' => 'searchCounterparty',
                                                            //                                                        'headerOptions' => ['style' => 'width: 150px; min-width: 150px'],
                                                            //                                                        'contentOptions' => ['style' => 'white-space: normal'],
                                                            'enableSorting' => false,
                                                            'label' => 'Контрагент',
                                                            'value' => function ($model) {
                                                                return $model->getCounterpartyLabel();
                                                            }
                                                        ],
                                                        [
                                                            'label' => 'Тип',
                                                            'value' => function ($model) {
                                                                if ($model->agency_id || $model->rieltor_id) {
                                                                    return 'Агентство';
                                                                }
                                                                if ($model->user_id && ($model->user->role == User::ROLE_ADMIN || $model->user->role == User::ROLE_FIN_DIRECTOR)) {
                                                                    return 'Директор';
                                                                }
                                                                if ($model->user_id && ($model->user->role == User::ROLE_SALES_MANAGER || $model->user->role == User::ROLE_MANAGER)) {
                                                                    return 'Отдел продаж';
                                                                }
                                                                return '';
                                                            }
                                                        ],
                                                        //                                                    [
                                                        //                                                        'label' => 'Менеджер (риелтор)',
                                                        //                                                        'headerOptions' => ['style' => 'width: 150px; min-width: 150px'],
                                                        //                                                        'contentOptions' => ['style' => 'white-space: normal'],
                                                        //                                                        'value' => function ($model) {
                                                        //                                                            if ($model->rieltor) {
                                                        //                                                                return $model->rieltor->fullname;
                                                        //                                                            }
                                                        //                                                            if ($model->user) {
                                                        //                                                                return $model->user->fullname;
                                                        //                                                            }
                                                        //                                                            return null;
                                                        //                                                        }
                                                        //                                                    ],
                                                        [
                                                            'attribute' => 'status',
                                                            'enableSorting' => false,
                                                            'value' => function ($model) {
                                                                return $model->getStatusLabel();
                                                            }
                                                        ],
                                                        [
                                                            'label' => 'Комиссия',
                                                            'enableSorting' => false,
                                                            'value' => function ($model) {
                                                                $value = 0;
                                                                if ($model->agency_id || $model->rieltor_id) {
                                                                    $value = $model->flat->commission_agency;
                                                                    if ($model->flat->commission_agency_type == Flat::COMMISSION_TYPE_PERCENT) {
                                                                        $value .= '%';
                                                                    }
                                                                } else {
                                                                    $value = $model->flat->commission_manager;
                                                                    if ($model->flat->commission_manager_type == Flat::COMMISSION_TYPE_PERCENT) {
                                                                        $value .= '%';
                                                                    }
                                                                }
                                                                return $value;
                                                            }
                                                        ],
                                                        [
                                                            'attribute' => 'price',
                                                            'enableSorting' => false,
                                                            'value' => function ($model) {
                                                                return Yii::$app->formatter->asDecimal(str_replace(',', '', $model->price));
                                                            }
                                                        ],
                                                        [
                                                            'label' => 'Дата оплаты',
                                                            'enableSorting' => false,
                                                            'value' => function ($model) {
                                                                return Yii::$app->formatter->asDate($model->created_at);
                                                            }
                                                        ],
                                                        [
                                                            'class' => 'yii\grid\ActionColumn',
                                                            'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                                                            'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                                                            'buttons' => [
                                                                'update' => function ($url, $model, $key) {
                                                                    return Html::a('<i class="fa fa-pencil"></i>',
                                                                                    ['/payments/update', 'id' => $model['id']],
                                                                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'modal', 'data-invoice_id' => $model->id]
                                                                    );
                                                                },
                                                                'delete' => function ($url, $model, $key) {
                                                                    return Html::a('<i class="fa fa-trash"></i>',
                                                                                    '#!',
                                                                                    ['class' => 'btn btn-default btn-sm delete-invoice', 'data-toggle' => 'tooltip', 'data-invoice_id' => $model->id]
                                                                    );
                                                                },
                                                            ]
                                                        ],
                                                    ],
                                                ]);
                                                ?>
                                                <?php Pjax::end() ?>
                                            <?php } else { ?>
                                                <div class="box-body"><p>Не указан покупатель</p></div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Описание:</h3>
                                    <!-- tools box -->
                                    <!--<div class="pull-right box-tools">
                                        <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                                            <i class="fa fa-minus"></i></button>
                                        <button type="button" class="btn btn-default btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                                            <i class="fa fa-times"></i></button>
                                    </div>-->
                                    <!-- /. tools -->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body pad">

                                    <?= $form->field($modelForm, 'description', ['enableClientValidation' => false])->textarea(['rows' => 8, 'class' => 'compose-textarea editor-init form-control'])->label(false) ?>

                                </div>
                            </div>
                        </div>
                        <div class="tab-pane <?php
                        if (isset($tab) && $tab === '#tab_2') {
                            echo 'active';
                        }
                        ?> clearfix" id="tab_2">

                            <?php if (!$model->isNewRecord) { ?>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="box">

                                            <div class="box-header with-border">
                                                <h3 class="box-title">Платежи</h3>
                                                <?php if (!$model->hasAgreementSigned()) { ?>
                                                    <div class="box-tools">
                                                        <a href="#!" class="btn btn-default btn-sm hidden-xs" data-toggle="modal" data-target="#modal-payment">
                                                            Добавить
                                                        </a>
                                                        <a href="#!" class="btn btn-default btn-sm visible-xs" data-toggle="modal" data-target="#modal-payment">
                                                            <i class="fa fa-plus visible-xs" aria-hidden="true"></i>
                                                        </a>

                                                        <?php if ($isPaid === false) { ?>
                                                            <a href="#!" class="btn btn-default btn-sm hidden-xs" data-toggle="modal" data-target="#modal-payment-many">
                                                                Пакетное добавление
                                                            </a>
                                                        <?php } ?>
                                                        <a href="#!" class="btn btn-default btn-sm visible-xs" data-toggle="modal" data-target="#modal-payment-many">
                                                            <i class="fa fa-plus visible-xs" aria-hidden="true"></i>
                                                        </a>

                                                        <a id="del_select_elem" href="javascript:;" class="btn btn-default btn-sm hidden-xs">
                                                            Удалить выбранные
                                                        </a>
                                                        <a href="#!" class="btn btn-default btn-sm visible-xs">
                                                            <i class="fa fa-plus visible-xs" aria-hidden="true"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <?php Pjax::begin(['id' => 'payments']) ?>
                                            <?php
                                            $actionButtonsTemplate = '{accept} {update} {delete}';
                                            $actionButtonsHeaderOptions = ['style' => 'width: 120px; min-width: 120px'];
                                            if ($model->hasAgreementSigned()) {
                                                $actionButtonsTemplate = '{accept}';
                                                $actionButtonsHeaderOptions = ['style' => 'width: 80px; min-width: 80px'];
                                            }
                                            ?>
                                            <?php
                                            echo GridView::widget([
                                                'dataProvider' => $paymentDataProvider,
                                                'filterModel' => null,
                                                'showFooter' => true,
                                                'id' => 'paygrid',
                                                'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap'],
                                                'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                                                'pager' => [
                                                    'class' => 'yii\widgets\LinkPager',
                                                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                                                ],
                                                'rowOptions' => function ($model, $index, $widget, $grid) use ($nextPayment) {
                                                    $options = [];
                                                    if ($nextPayment && $model->id == $nextPayment->id) {
                                                        $options['class'] = 'bg-gray';
                                                    }
                                                    return $options;
                                                },
                                                'footerRowOptions' => ['style' => 'font-weight: bold'],
                                                'columns' => [
                                                    [
                                                        'attribute' => 'pay_number',
                                                        'enableSorting' => false,
                                                    ],
                                                    [
                                                        'attribute' => 'pay_date',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->getPayDate();
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'price_plan',
                                                        'enableSorting' => false,
                                                        'footer' => PriceHelper::format(Payment::find()->where(['and', ['flat_id' => $model->id], ['is not', 'flat_id', null]])->sum('price_plan'), false),
                                                        'value' => function ($model) {
                                                            return PriceHelper::format($model->price_plan, false);
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'price_fact',
                                                        'enableSorting' => false,
                                                        'footer' => PriceHelper::format(Payment::find()->where(['and', ['flat_id' => $model->id], ['is not', 'flat_id', null]])->sum('price_fact'), false),
                                                        'value' => function ($model) {
                                                            return PriceHelper::format($model->price_fact, false);
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'price_saldo',
                                                        'label' => 'Задолженность',
                                                        'enableSorting' => false,
                                                        'footer' => Yii::$app->formatter->asDecimal(floatval(Payment::find()->where(['and', ['flat_id' => $model->id], ['is not', 'flat_id', null]])->sum('price_saldo')) * -1, 2),
                                                        'value' => function ($model) {
                                                            $result = Yii::$app->formatter->asDecimal(floatval($model->price_saldo) * -1, 2);
                                                            return $result;
                                                        }
                                                    ],
                                                    [
                                                        'class' => 'yii\grid\ActionColumn',
                                                        'template' => '<div class="btn-group">' . $actionButtonsTemplate . '</div>',
                                                        'headerOptions' => $actionButtonsHeaderOptions,
                                                        'buttons' => [
                                                            'accept' => function ($url, $model, $key) {
                                                                $dateFilter = date("Y-m-d", strtotime("+1 month", strtotime(date('Y-m-01'))));
                                                                if ($model->pay_date < $dateFilter) {
                                                                    $invoice = Invoice::find()->where(['payment_id' => $model->id])->one();
                                                                    if ($invoice) {
                                                                        return Html::a('<i class="fa fa-credit-card"></i>',
                                                                                        [
                                                                                            '/payments/update',
                                                                                            'id' => $invoice->id,
                                                                                        ],
                                                                                        ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Посмотреть/Принять платеж']
                                                                        );
                                                                    }
                                                                    return Html::a('<i class="fa fa-credit-card"></i>',
                                                                                    [
                                                                                        '/payments/create',
                                                                                        'type' => 'income',
                                                                                        'payment_id' => $model->id,
                                                                                    ],
                                                                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Принять платеж']
                                                                    );
                                                                }
                                                            },
                                                            'update' => function ($url, $model, $key) {
                                                                return Html::a('<i class="fa fa-pencil"></i>',
                                                                                '#!',
                                                                                ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'modal', 'data-target' => '#modal-payment', 'data-payment_id' => $model->id]
                                                                );
                                                            },
//                                                            'delete' => function ($url, $model, $key) {
//                                                                return Html::a('<i class="fa fa-trash"></i>',
//                                                                    [
//                                                                        '/flats/delete-payment',
//                                                                        'id' => $model->id,
//                                                                    ],
//                                                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 1, 'pjax-container' => 'payments', 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
//                                                                );
//                                                            },
                                                            'delete' => function ($url, $model, $key) {
                                                                if ($model->is_price_left) {
                                                                    return Html::a('<i class="fa fa-trash"></i>',
                                                                                    '#!',
                                                                                    ['class' => 'btn btn-default btn-sm', 'onclick' => "confirm('Это остаточный платеж. Он будет автоматически удален, когда остаток станет = 0'); return false;"]
                                                                    );
                                                                }
                                                                return Html::a('<i class="fa fa-trash"></i>',
                                                                                '#!',
                                                                                ['class' => 'btn btn-default btn-sm delete-payment', 'data-toggle' => 'tooltip', 'data-payment_id' => $model->id]
                                                                );
                                                            },
                                                        ]
                                                    ],
                                                    [
                                                        'class' => 'yii\grid\CheckboxColumn',
                                                    //'checkboxOptions' => ["value" => ArrayHelper::getValue(Invoice::find()->where(['payment_id' => $model->id])->asArray()->one(),'id')]
                                                    ],
                                                ],
                                            ]);
                                            ?>
                                            <p class="text-bold text-right">
                                                Остаток: <?= PriceHelper::format($model->getPayments()->sum('`price_plan` - `price_fact`'), false) ?>
                                            </p>
                                            <?php Pjax::end() ?>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>

                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="form-group">
                                    <?= $form->field($modelForm, 'tab_id')->hiddenInput()->label(false) ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/flats/index']) ?>" class="btn btn-default">Отменить</a>
                                    <button type="submit" class="btn btn-success">Сохранить</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<JS
    $(document).ready(function() {
        $('#pjax-payment-modal').on('pjax:end', function() {
            console.log('form submitted');
            $('#modal-payment').modal('hide');
            $('body').removeClass('modal-open').css('padding-right', '');
            $.pjax.reload({container: '#payments'}); //Reload GridView
        });
        
        $("ul.nav > li > a").on('click',function(e){
            let tabID = $(this).attr('href');
            
            $("input#flatform-tab_id").val(tabID);
        });
    });
JS
);
?>
<?php Pjax::begin(['id' => 'pjax-payment-modal', 'enablePushState' => false]) ?>
<!-- Modal -->
<div class="modal fade" id="modal-payment" tabindex="-1" role="dialog" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?=
            $this->render('_payment-modal-form', [
                'model' => new Payment(),
                'flatModel' => $model,
            ]);
            ?>
        </div>
    </div>
</div>
<!-- Modal -->
<?php Pjax::end() ?>

<!-- Modal -->
<div class="modal fade" id="modal-payment-many" tabindex="-1" role="dialog" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?=
            $this->render('_payment-modal-many-form', [
                'model' => new Payment(),
                'flatModel' => $model,
            ]);
            ?>
        </div>
    </div>
</div>
<!-- Modal -->

<?php
$urlAjaxGetClient = Yii::$app->urlManager->createUrl(['/clients/ajax-get-client', 'id' => '']);
$urlAjaxGetPaymentForm = Yii::$app->urlManager->createUrl(['/flats/ajax-get-payment-form', 'flat_id' => $model->id, 'payment_id' => '']);
$urlAjaxDeletePayment = Yii::$app->urlManager->createUrl(['/flats/delete-payment', 'id' => '']);
$urlAjaxDeleteInvoice = Yii::$app->urlManager->createUrl(['/payments/ajax-delete', 'id' => '']);
$urlAjaxPaketDeletePayment = Yii::$app->urlManager->createUrl(['/flats/delete-pakpayment']);
$this->registerJs(<<<JS
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
    
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        updatePriceInputsStateByType();
    });
    
    $(document).on('blur', 'input[name="FlatForm\\[square\\]"]', function () {
        updatePrice();
        updatePriceSell();
        updatePriceDiscountM();
        updatePriceDiscount();
        updatePriceRest();
    });
    
    $(document).on('blur', 'input[name="FlatForm\\[price_m\\]"]', function () {
        updatePrice();
        updatePriceDiscountM();
    });
        
    $(document).on('blur', 'input[name="FlatForm\\[price_paid_init\\]"]', function () {
        let price = parseFloat($("input#priceTotal").val());
        let firstPay = parseFloat($(this).val());
        
        if(price < firstPay) {
            $(this).val(price);
        }
        updatePriceRest();
    });
        
    $(document).on('blur', 'input[name="FlatForm\\[price_sell_total\\]"]', function () {
        updatePriceRest();
    });
    
    $(document).on('blur', 'input[name="FlatForm\\[price_sell_m\\]"]', function () {
        updatePriceSell();
        updatePriceDiscountM();
        updatePriceRest();
    });
    
    $(document).on('blur', 'input[name="FlatForm\\[price_discount_m\\]"]', function () {
        updatePriceDiscount();
    });
    
    $(document).on('change', 'select[name="FlatForm\\[client_id\\]"]', function () {
        $.get('{$urlAjaxGetClient}'+$(this).val(), function(data) {
            if ('{$model->agency_id}' == '') {
                $('select[name="FlatForm\\[agency_id\\]"]').val(data.agency_id).trigger('change.select2');
            }
            $('select[name="FlatForm\\[user_id\\]"]').val(data.user_id).trigger('change.select2');
        });
    });
            
    $(document).on('change', 'select[name="FlatForm\\[unit_type\\]"]', function () {
        updatePriceInputsStateByType();
    });
            
    $(document).on('blur', '#priceTotal', function () {
        var price = parseFloat($(this).val());
        var priceSell = parseFloat($('#priceSellTotal').val());
        $('#priceDiscountTotal').val(price - priceSell);
            
        var square = parseFloat($('#flatform-square').val());
        var priceMRound = Math.round((price / square) * 100) / 100;
        var priceDiscountMRound = Math.round(((price - priceSell) / square) * 100) / 100;
        $('#flatform-price_m').val(priceMRound);
        $('#flatform-price_discount_m').val(priceDiscountMRound);
            
        updatePriceRest();
    });
    $(document).on('blur', '#priceSellTotal', function () {
        var price = parseFloat($('#priceTotal').val());
        var priceSell = parseFloat($(this).val());
        $('#priceDiscountTotal').val(price - priceSell);
            
        var square = parseFloat($('#flatform-square').val());
        var priceSellMRound = Math.round((priceSell / square) * 100) / 100;
        var priceDiscountMRound = Math.round(((price - priceSell) / square) * 100) / 100;
        $('#flatform-price_sell_m').val(priceSellMRound);
        $('#flatform-price_discount_m').val(priceDiscountMRound);
    });
    $(document).on('blur', '#priceDiscountTotal', function () {
        var price = parseFloat($('#priceTotal').val());
        var priceDiscount = parseFloat($(this).val());
        $('#priceSellTotal').val(price - priceDiscount);
    });
        
    $(document).on('show.bs.modal', '#modal-payment', function(e) {
        if (e.namespace === 'bs.modal') {
            var modalContent = $(this).find('.modal-content');
            var invoker = $(e.relatedTarget);
            var paymentId = invoker.attr('data-payment_id') || 0;
            modalContent.html('<div class="modal-body">загрузка...</div>');
            $.get('{$urlAjaxGetPaymentForm}'+paymentId, function(data) {
                modalContent.html(data);
            });
        }
    });
            
    $(document).on('click', '.delete-payment', function(e) {
        e.preventDefault();
        console.log('deleting');    
            
        if (confirm("Вы уверены, что хотите удалить этот элемент?")) {
            var paymentId = $(this).attr('data-payment_id') || 0;
            $.post('{$urlAjaxDeletePayment}'+paymentId, function(data) {
                $.pjax.reload({container: "#payments"}); //Reload GridView
            });
        } 
        return false;
    });
            
    $(document).on('click', '#del_select_elem', function(e) {
        e.preventDefault(); 
            
        if (confirm("Вы уверены, что хотите удалить выбранные элементы?")) {
            let keys = $('#paygrid').yiiGridView('getSelectedRows');
            console.log(keys);
            $.post('{$urlAjaxPaketDeletePayment}', {arrElemnts: keys}, function(data) {
                $.pjax.reload({container: "#payments"}); //Reload GridView
            });
        } 
        return false;
    });
            
    $(document).on('click', '.delete-invoice', function(e) {
        e.preventDefault();
        console.log('deleting');    
            
        if (confirm("Вы уверены, что хотите удалить этот элемент?")) {
            var invoiceId = $(this).attr('data-invoice_id') || 0;
            $.post('{$urlAjaxDeleteInvoice}'+invoiceId, function(data) {
                $.pjax.reload({container: "#invoices"}); //Reload GridView
            });
        } 
        return false;
    });
    
    function updatePrice() {
        console.log('updatePrice');
        var price = parseFloat($('input[name="FlatForm\\[price_m\\]"]').val()) || 0;
        var square = parseFloat($('input[name="FlatForm\\[square\\]"]').val()) || 0;
        var result = Math.round((price * square) * 100) / 100; //.toFixed(2);
        $('input#priceTotal').val(result);
    }
    
    function updatePriceSell() {
        console.log('updatePriceSell');
        var price = parseFloat($('input[name="FlatForm\\[price_sell_m\\]"]').val()) || 0;
        var square = parseFloat($('input[name="FlatForm\\[square\\]"]').val()) || 0;
        var result = Math.round((price * square) * 100) / 100; //.toFixed(2);
        $('input#priceSellTotal').val(result);
            
        updatePriceRest();
    }
            
    function updatePriceRest() {           
        var pricepaid = parseFloat($('input[name="FlatForm\\[price_paid_init\\]"]').val()) || 0;
        var pricesell = parseFloat($('input#priceSellTotal').val()) || 0;
        var result = Math.round(pricesell - pricepaid); //.toFixed(2);
        $('input#flatform-price_paid_out').val(result);
    }
    
    function updatePriceDiscount() {
        console.log('updatePriceDiscount');
        var price = parseFloat($('input[name="FlatForm\\[price_discount_m\\]"]').val()) || 0;
        var square = parseFloat($('input[name="FlatForm\\[square\\]"]').val()) || 0;
        var result = Math.round((price * square) * 100) / 100; //.toFixed(2);
        $('input#priceDiscountTotal').val(result);
    }
    
    function updatePriceDiscountM() {
        console.log('updatePriceDiscountM');
        var price = parseFloat($('input[name="FlatForm\\[price_m\\]"]').val()) || 0;
        var priceSell = parseFloat($('input[name="FlatForm\\[price_sell_m\\]"]').val()) || 0;
        var result = Math.round((price - priceSell) * 100) / 100; //.toFixed(2);
        $('input[name="FlatForm\\[price_discount_m\\]"]').val(result).trigger('blur');
    }
            
    function updatePriceInputsStateByType() {
        var type = $('select[name="FlatForm\\[unit_type\\]"]').val();
        if (type == 'car_place' || type == 'parking') {
            $('.nosquare-hide').attr('readonly', true).parent('.form-group').css({opacity: '0.3'}).hide();
            $('.nosquare-show').attr('readonly', false).parent('.form-group');
        } else {
            $('.nosquare-hide').attr('readonly', false).parent('.form-group').css({opacity: '1'}).show();
            //$('.nosquare-show').attr('readonly', true).parent('.form-group');
            $('input[name="FlatForm\\[price_m\\]"]').trigger('blur');
        }
    }
JS
);
