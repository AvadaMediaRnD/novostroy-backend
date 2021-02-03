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
use common\models\Payment;
use backend\models\PaymentSearch;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use backend\models\InvoiceSearch;
use common\models\Invoice;
use common\models\Article;

/* @var $this yii\web\View */
/* @var $model \common\models\User */
/* @var $modelForm backend\models\UserForm */

$invoiceSearchModel = new InvoiceSearch();
$invoiceSearchModel->user_id = $model->id;
$invoiceDataProvider = $invoiceSearchModel->search(Yii::$app->request->queryParams);
$invoiceDataProvider->query
        ->andWhere(['type' => Invoice::TYPE_OUTCOME])
        ->andWhere(['article_id' => Article::getIdCommissionManager()]);
$invoicePriceSum = Invoice::find()->where(['user_id' => $model->id])
        ->andWhere(['type' => Invoice::TYPE_OUTCOME])
        ->andWhere(['article_id' => Article::getIdCommissionManager()])
        ->sum('price');
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Редактировать профиль пользователя</h3>
                <?php if (!$model->isNewRecord && in_array($model->role, [User::ROLE_MANAGER, User::ROLE_SALES_MANAGER])) { ?>
                    <div class="box-tools">
                        <a class="btn btn-sm btn-default" href="#invoices">Показать комиссионные</a>
                    </div>
                <?php } ?>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="userAvatar">
                            <img class="img-circle pull-left img-responsive" src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getAvatar(), 'w' => 160, 'h' => 160, 'fit' => 'crop']) ?>" alt="User Avatar">
                            <?= $form->field($modelForm, 'image')->fileInput(['accept' => 'image/*']) ?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <?=
                        $form->field($modelForm, 'status')->widget(Select2::class, [
                            'data' => User::getStatusOptions(),
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
                        <?php if (Yii::$app->user->identity->role == User::ROLE_ADMIN) { ?>
                            <?=
                            $form->field($modelForm, 'role')->widget(Select2::class, [
                                'data' => User::getRoleOptions(),
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
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <?= $form->field($modelForm, 'lastname')->textInput() ?>
                        <?= $form->field($modelForm, 'firstname')->textInput() ?>
                        <?= $form->field($modelForm, 'middlename')->textInput() ?>
                        <?=
                        $form->field($modelForm, 'birthdate')->widget(DatePicker::className(), [
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'removeButton' => false,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy'
                            ]
                        ])
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <?= $form->field($modelForm, 'description')->textarea(['rows' => 10, 'style' => 'height: 256px']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <h2 class="page-header">Контактные данные</h2>
                        <?= $form->field($modelForm, 'phone')->textInput() ?>
                        <?= $form->field($modelForm, 'viber')->textInput() ?>
                        <?= $form->field($modelForm, 'telegram')->textInput() ?>
                        <?= $form->field($modelForm, 'email')->textInput() ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <h2 class="page-header">Изменить пароль</h2>
                        <?=
                        $form->field($modelForm, 'password', [
                            'template' => "{label}\n
                                    <div class=\"input-group\">{input}\n
                                        <span class=\"input-group-btn\">
                                            <button class=\"btn btn-default\" type=\"button\" onclick=\"generatePassword('.pass-value')\">
                                                Сгенерировать
                                            </button>
                                            <button type=\"button\" class=\"btn btn-primary\" id=\"showPass\">
                                                <i class=\"fa fa-eye\" aria-hidden=\"true\"></i>
                                            </button>
                                        </span>
                                    </div>\n{hint}\n{error}"
                        ])->passwordInput(['maxlength' => true, 'class' => 'form-control pass-value'])
                        ?>
                        <?= $form->field($modelForm, 'password2')->passwordInput(['maxlength' => true, 'class' => 'form-control pass-value']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/settings/users']) ?>" class="btn btn-default">Отменить</a>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <?php if (!$model->isNewRecord && in_array($model->role, [User::ROLE_MANAGER, User::ROLE_SALES_MANAGER])) { ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Комиссионные</h3>
                        </div>

                        <?php Pjax::begin(['id' => 'invoices']) ?>
                        <?php
                        echo GridView::widget([
                            'dataProvider' => $invoiceDataProvider,
                            'filterModel' => null,
                            'showFooter' => true,
                            'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap'],
                            'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                            'pager' => [
                                'class' => 'yii\widgets\LinkPager',
                                'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                            ],
                            'footerRowOptions' => ['style' => 'font-weight: bold'],
                            'columns' => [
                                [
                                    'attribute' => 'searchNumber',
                                    'label' => '№ кв.',
                                    'enableSorting' => false,
                                    'format' => 'html',
                                    'value' => function ($model) {
                                        if ($model->flat_id) {
                                            return Html::a($model->flat->number, ['/flats/update', 'id' => $model->flat_id]);
                                        }
                                        $flatNumber = $model->flat['number'] ?? '';
                                        return $flatNumber;
                                    }
                                ],
                                [
                                    'attribute' => 'searchHouse',
                                    'label' => 'Дом (секция)',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                        return $model->flat['house'] ? $model->flat['house']->getNameSection() : null;
                                    }
                                ],
                                [
                                    'attribute' => 'searchSquare',
                                    'label' => 'Площадь (м<sup>2</sup>)',
                                    'encodeLabel' => false,
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                        return $model->flat['square'];
                                    }
                                ],
                                [
                                    'attribute' => 'searchFlatPrice',
                                    'label' => 'Стоимость (у.е.)',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                        return PriceHelper::format($model->flat['price'], false);
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                        return $model->getStatusLabel();
                                    }
                                ],
                                [
                                    'attribute' => 'searchCommission',
                                    'label' => 'Комиссия',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                        return $model->flat['commission_agency'] . ($model->flat['commission_agency_type'] == Flat::COMMISSION_TYPE_PERCENT ? '%' : '');
                                    }
                                ],
                                [
                                    'attribute' => 'searchPrice',
                                    'label' => 'Сумма',
                                    'enableSorting' => false,
                                    'footer' => PriceHelper::format($invoicePriceSum),
                                    'value' => function ($model) {
                                        return PriceHelper::format($model->price);
                                    }
                                ],
                                [
                                    'attribute' => 'uid_date',
                                    'label' => 'Дата оплаты',
                                    'enableSorting' => false,
                                    'value' => function ($model) {
                                        return $model->getUidDate();
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                                    'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                                    'buttons' => [
                                        'update' => function ($url, $model, $key) {
                                            return Html::a('<i class="fa fa-pencil"></i>',
                                                            ['/payments/update', 'id' => $model->id],
                                                            ['class' => 'btn btn-default btn-sm']
                                            );
                                        },
                                        'delete' => function ($url, $model, $key) {
                                            return Html::a('<i class="fa fa-trash"></i>',
                                                            '#!',
                                                            ['class' => 'btn btn-default btn-sm delete-payment', 'data-toggle' => 'tooltip', 'data-payment_id' => $model->id]
                                            );
                                        },
                                    ]
                                ],
                            ],
                        ]);
                        ?>
                        <?php Pjax::end() ?>

                    </div>
                </div>
            </div>
        <?php } ?>


    </div>
</div>

<?php
$urlAjaxDeletePayment = Yii::$app->urlManager->createUrl(['/flats/delete-payment', 'id' => '']);
$urlAjaxDeleteInvoice = Yii::$app->urlManager->createUrl(['/payments/ajax-delete', 'id' => '']);

$this->registerJs(<<<JS
    function generatePassword(targetSelector) {
        var pass = Math.random().toString(36).substring(4);
        $('input'+targetSelector).val(pass);
        $('span'+targetSelector).text(pass);
    }
        
    $(document).on('click', '.delete-payment', function(e) {
        e.preventDefault(); 
            
        if (confirm("Вы уверены, что хотите удалить этот элемент?")) {
            var paymentId = $(this).attr('data-payment_id') || 0;
            $.post('{$urlAjaxDeleteInvoice}'+paymentId, function(data) {
                $.pjax.reload({container: "#invoices"}); //Reload GridView
            });
        } 
        return false;
    });
JS
        , \yii\web\View::POS_END);
