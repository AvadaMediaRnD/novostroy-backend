<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;
use common\models\Agency;
use kartik\export\ExportMenu;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use backend\models\AgencyImportForm;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AgencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $agenciesCount integer */

$this->title = 'Агентства';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Владельцы квартир</h2>-->
    <!--</div>-->
    <div class="col-xs-12 col-sm-6">
        <div class="info-box" style="width:250px;">
            <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Зарегистрировано</span>
                <span class="info-box-number"><?= $agenciesCount ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/agency/create']) ?>">Добавить агентство</a></li>
                <li><a href="#!" onclick="$('#export-xlsx a').click()">Скачать xls</a></li>
                <li><a href="#!" data-toggle="modal" data-target="#modalFile">Загрузить xls</a></li>
            </ul>
            <?php
            $gridColumns = [
                'id',
                'name',
                'phone:raw',
                'email',
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    }
                ],
                [
                    'attribute' => 'description',
                    'value' => function ($model) {
                        return strip_tags($model->description);
                    }
                ],
                [
                    'label' => 'Риелторы',
                    'value' => function ($model) {
                        $rieltorNames = \yii\helpers\ArrayHelper::getColumn($model->getRieltors()->orderBy(['is_director' => SORT_DESC])->all(), 'fullname');
                        return implode(',', $rieltorNames);
                    }
                ],
            ];
            echo ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
                'showConfirmAlert' => false,
                'showColumnSelector' => false,
                'container' => ['class' => 'btn-group hide', 'role' => 'group'],
                'filename' => 'export-agency-' . date('Ymd'),
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_CSV => false,
                    ExportMenu::FORMAT_EXCEL => false,
                    ExportMenu::FORMAT_EXCEL_X => [
                        'label' => Yii::t('kvexport', 'Excel 2007+'),
                        'icon' => 'floppy-remove',
                        'iconOptions' => ['class' => 'text-success'],
                        'linkOptions' => [],
                        'options' => ['id' => 'export-xlsx', 'title' => Yii::t('kvexport', 'Microsoft Excel 2007+ (xlsx)')],
                        'alertMsg' => Yii::t('kvexport', 'The EXCEL 95+ (xls) export file will be generated for download.'),
                        'mime' => 'application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'extension' => 'xlsx',
                        'writer' => ExportMenu::FORMAT_EXCEL_X,
                    ],
                ],
                'dropdownOptions' => [
                    'label' => 'Выгрузить в XLS',
                    'class' => 'btn btn-secondary'
                ],
                'boxStyleOptions' => [
                    ExportMenu::FORMAT_EXCEL_X => [
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_NONE,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                            'inside' => [
                                'borderStyle' => Border::BORDER_NONE,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ]
                        ],
                    ],
                ],
                'headerStyleOptions' => [
                    ExportMenu::FORMAT_EXCEL_X => [
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => [
                                'argb' => 'FFE5E5E5',
                            ],
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_NONE,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                            'inside' => [
                                'borderStyle' => Border::BORDER_NONE,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ]
                        ],
                    ],
                ],
                'onRenderDataCell' => function($cell, $content, $model, $key, $index, $widget) {
                    /* @var $cell PhpOffice\PhpSpreadsheet\Cell\Cell */
                    $cell->setValueExplicit($content, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                },
            ]);
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Список агетнств</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/agency/index']) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Очистить фильтры</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap'],
                'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return [
                        'data-href' => Yii::$app->urlManager->createUrl(['/agency/update', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'name',
                    ],
                    [
                        'attribute' => 'searchFullname',
                        'label' => 'Директор',
                        'value' => function ($model) {
                            if (isset($model->rieltorDirector->fullname)) {
                                return $model->rieltorDirector->fullname;
                            } else {
                                return '';
                            }
                        }
                    ],
                    [
                        'attribute' => 'phone',
                        'value' => function ($model) {
                            return $model->phone . '';
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'format' => 'email',
                        'value' => function ($model) {
                            return $model->email . '';
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'enableSorting' => false,
                        'format' => 'html',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Agency::getStatusOptions(),
                            'model' => $searchModel,
                            'attribute' => 'status',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        }
                    ],
                    [
                        'attribute' => 'searchSellsTotal',
                        'label' => 'Кол-во продаж',
                        'filter' => false,
                        'value' => function ($model) {
                            return $model->getAgreements()->count();
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                                [
                                                    '/agency/update',
                                                    'id' => $model->id,
                                                ],
                                                ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                                [
                                                    '/agency/delete',
                                                    'id' => $model->id,
                                                ],
                                                ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                                );
                            },
                        ]
                    ],
                ],
            ]);
            ?>

        </div>
    </div>
</div>

<!-- Modal -->
<?php $agencyImportForm = new AgencyImportForm(['format' => 'xlsx']); ?>
<div class="modal fade" id="modalFile" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'action' => ['agency/import'],
                        'enableAjaxValidation' => true,
                        'options' => ['enctype' => 'multipart/form-data'],
            ]);
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Импорт из xlsx</h4>
            </div>

            <div class="modal-body">
                <?= $form->field($agencyImportForm, 'format')->hiddenInput()->label(false) ?>
                <?= $form->field($agencyImportForm, 'file')->fileInput(['accept' => '.csv, .ods, .xls, .xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel']) ?>
                <?= $form->field($agencyImportForm, 'is_only_new', ['enableClientValidation' => false])->checkbox() ?>

                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#hint-types"><i class="fa fa-question-circle"></i> Помощь по заполнению</button>
                <div id="hint-types" class="collapse margin-top-15">
                    <p>1. <i class="fa fa-warning text-orange"></i> При загрузке система попытается найти агентство с указанном в файле <b>ID</b> или <b>названием</b>. 
                        Если такое агентство есть в базе, данные будут обновлены.
                        Иначе будет добавлено новое агентство.
                    </p>
                    <p>2. Чтобы добавить только новые агентства, установите флаг "<?= $agencyImportForm->getAttributeLabel('is_only_new') ?>".</p>
                    <p>3. Доступные значения для поля <b>Статус</b>:
                    <ul>
                        <?php
                        foreach (Agency::getStatusOptions() as $status) {
                            echo "<li>{$status}</li>";
                        }
                        ?>
                    </ul>
                    </p>
                    <p>4. Для заполнения риелторов агентства, перечислите их ФИО через запятую в соответствующей колонке xlsx файла. Первый риелтор в списке будет считаться директором агентства.
                    </p>
                </div>

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