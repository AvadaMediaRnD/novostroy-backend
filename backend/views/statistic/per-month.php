<?php

use yii\widgets\LinkPager,
    yii\helpers\Url,
    yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use common\helpers\PriceHelper;
use dosamigos\multiselect\MultiSelect;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\product\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $models \common\models\Flat[] */
/* @var $months array */
/* @var $houses \common\models\House[] */
/* @var $pages yii\data\Pagination */

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJS("

$('#test').change(function() {
    data = $('#test').select2('data');
    newUrl = removeParam('sections', window.location.href);

    data.map(function(a) {return a.text;}).forEach(function(element, index) {

        if(!(window.location.href.indexOf('?') > -1) && index == 0) {
            newUrl = newUrl + '?sections[' + index + ']=' + element;
        } else {
            newUrl = newUrl +  '&sections[' + index + ']=' + element;
        }
    });
    console.log(newUrl);

    window.location.href = newUrl;
});

function removeParam(key, sourceURL) {
    var rtn = sourceURL.split('?')[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf('?') !== -1) ? sourceURL.split('?')[1] : '';
    if (queryString !== '') {
        params_arr = queryString.split('&');
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split('=')[0];
            console.log(param);
            if (param.includes(key)) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + '?' + params_arr.join('&');
    }
    return rtn;
}

");
?>

<div class="row">
    <div class="col-xs-12 col-lg-5">
        <h2 class="page-header">Сводка ежемесячных платежей:</h2>
    </div>
    <div class="col-xs-12 col-lg-7">
        <div class="menu-align text-right">
            <?php
            ActiveForm::begin([
                'method' => 'get',
                'action' => Url::current(['date_range' => null, 'sections' => null]),
                'options' => ['class' => 'form'],
            ])
            ?>
            <div class="daterange margin-right-15 text-left display-inline">
                <label class="control-label">Период:</label>
                <div class="input-group drp-container">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <?=
                    DateRangePicker::widget([
                        'name' => 'date_range',
                        'value' => Yii::$app->request->get('date_range'),
                        'convertFormat' => true,
                        'useWithAddon' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'd.m.Y',
                                'separator' => ' - ',
                            ],
                            'opens' => 'left'
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'onchange' => 'form.submit();',
                        ],
                    ]);
                    ?>
                </div>
                <!-- /.input group -->
            </div>
            <div class="btn-group margin-bottom text-left display-inline">
                <label style="display:block;">Объект:</label>
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $selectedHouseName ? $selectedHouseName : 'Выберите действие' ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="<?= Url::to(['/statistic/month', 'date_range' => Yii::$app->request->get('date_range')]) ?>">Все объекты</a></li>
                    <?php foreach ($houseNames as $houseName) { ?>
                        <li><a href="<?= Url::to(['/statistic/month', 'house_name' => $houseName, 'date_range' => Yii::$app->request->get('date_range')]) ?>"><?= $houseName ?></a></li>
                    <?php } ?>
                </ul>
            </div>

            <?php if ($selectedHouseName): ?>
                <div class="btn-group margin-bottom text-left display-inline">
                    <label style="display:block;">Секция:</label>

                    <?php
                    echo MultiSelect::widget([
                        'id' => 'sections',
                        "options" => ['multiple' => 'multiple'], // for the actual multiselect
                        'data' => $sectionsNames, // data as array
                        'value' => $sections, // if preselected
                        'name' => 'sections', // name for the form
                        'clientOptions' => [
                            'includeSelectAllOption' => true,
                            'numberDisplayed' => 3,
                            'buttonWidth' => 120,
                            'selectAllText' => 'Все',
                            'nonSelectedText' => 'Не выбрано',
                            'nSelectedText' => ' секций',
                            'allSelectedText' => 'Все секции',
                            'onDropdownHide' => new \yii\web\JsExpression('function(e){$(".form").submit();}')
                        ],
                    ]);
                    ?>

                    <?php /* echo Select2::widget([
                      'name' => 'sections',
                      'value' =>  Yii::$app->request->get('sections'), // initial value (will be ordered accordingly and pushed to the top)
                      'data' => $sectionsNames,
                      //'maintainOrder' => true,
                      'options' => ['id'=> 'test', 'placeholder' => 'Select a color ...', 'multiple' => true],
                      'pluginOptions' => [
                      'tags' => true,
                      'maximumInputLength' => 10
                      ],
                      ]); */ ?>

                </div>
            <?php endif; ?>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Таблица платежей</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/statistic/month']) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Очистить фильтры</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <?php // Pjax::begin();  ?>
            <div class="box-body table-responsive double-scroll no-padding">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th style="min-width: 100px;">Площадь</th>
                            <th style="min-width: 120px;">Дом (Секция)</th>
                            <th style="min-width: 120px;">Цена</th>
                            <th style="min-width: 120px;">Цена за м<sup>2</sup></th>
                            <th style="min-width: 120px;">Факт</th>
                            <th style="min-width: 120px;">Остаток</th>
                            <th style="min-width: 120px;">Задолженность</th>
                            <?php foreach ($months as $month) { ?>
                                <th style="min-width: 120px;">План (<?= $month ?>)</th>
                                <th style="min-width: 120px;">Факт</th>
                                <th style="min-width: 120px;">Задолженность</th>
                                <th style="min-width: 120px;">Остаток</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($models) { ?>
                            <?php
                            foreach ($models as $model) {
                                $query = $model->getPayments();
                                $priceFact = $query->sum('price_fact');
                                $pricePlan = $query->sum('price_plan');
                                $priceLeft = $pricePlan - $priceFact;
                                $priceDebt = ($query->sum('price_saldo') !== null) ? 0 - $query->sum('price_saldo') : 0;
                                ?>
                                <tr data-href="#">
                                    <td><?= $model->number ?></td>
                                    <td><?= $model->square ?> м<sup>2</sup></td>
                                    <td><?= $model->house ? $model->house->getNameSection() : '' ?></td>
                                    <td><?= PriceHelper::format($model->price_sell_m * $model->square, false) ?></td>
                                    <td><?= PriceHelper::format($model->price_sell_m, false) ?></td>
                                    <td><?= PriceHelper::format($priceFact, false) ?></td>
                                    <td><?= PriceHelper::format($priceLeft, false) ?></td>
                                    <td><?= PriceHelper::format($priceDebt, false) ?></td>
                                    <?php
                                    foreach ($months as $k => $month) {
                                        $queryMonth = $model->getPayments()
                                                ->andWhere(['between', 'pay_date', date('Y-m-01', strtotime($k)), date('Y-m-t', strtotime($k))]);
                                        $queryToMonth = $model->getPayments()
                                                ->andWhere(['<=', 'pay_date', date('Y-m-t', strtotime($k))]);
                                        $pricePlanMonth = $queryMonth->sum('price_plan');
                                        $priceFactMonth = $queryMonth->sum('price_fact');
                                        $priceLeftMonth = $pricePlan - $queryToMonth->sum('price_fact');
                                        //$priceDebtMonth = ($queryToMonth->sum('price_saldo') > 0) ? '-'.$queryToMonth->sum('price_saldo') : round($queryToMonth->sum('price_saldo')) * -1;
                                        if(\yii\helpers\ArrayHelper::getValue($queryMonth->asArray()->one(), 'pay_date') > date('Y-m-d') && $priceFactMonth <= 0) {
                                            $priceDebtMonth = 0;
                                        }
                                        else {
                                            $priceDebtMonth = $pricePlanMonth - $priceFactMonth;
                                        }
                                        ?>
                                        <td><?= PriceHelper::format($pricePlanMonth, false) ?></td>
                                        <td><?= PriceHelper::format($priceFactMonth, false) ?></td>
                                        <td><?= PriceHelper::format($priceDebtMonth, false) ?></td>
                                        <td><?= PriceHelper::format($priceLeftMonth, false) ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?=
                LinkPager::widget([
                    'pagination' => $pages,
                    'options' => [
                        'class' => 'pagination pagination-sm no-margin pull-right',
                    ]
                ]);
                ?>
            </div>
            <?php // Pjax::end(); ?>
        </div>
    </div>
</div>

<?php
$script = <<< JS
    $(document).ready(function() {
        $('.double-scroll').doubleScroll();
    });
JS;

$this->registerJs($script, yii\web\View::POS_READY);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/jquery.doubleScroll.js', ['depends' => [\yii\web\JqueryAsset::className()], 'position' => yii\web\View::POS_END]);
?>