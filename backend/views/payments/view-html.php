<?php

use yii\helpers\Html;
use common\helpers\PriceHelper;
use common\helpers\DateHelper;
use common\models\Cashbox;
use common\models\Flat;

/* @var $model \common\models\Invoice */

$companyName = '<span> </span> <span> </span> ' . ($model->company_name ? $model->company_name : $model->flat->house->company_name) . ' <span> </span> <span> </span> ';
$priceUah = $model->cashbox->currency == Cashbox::CURRENCY_UAH ? $model->price : ($model->price * $model->rate);
if ($priceUah != round($priceUah)) {
    $priceUah = round($priceUah, 2);
}

$aptTypeLabel = 'апатраменти';
switch ($model->flat->unit_type) {
    case Flat::TYPE_CAR_PLACE: {
        $aptTypeLabel = 'машиномісце';
        break;
    }
    case Flat::TYPE_COMMERCIAL: {
        $aptTypeLabel = 'комерцїйне приміщення';
        break;
    }
    case Flat::TYPE_FLAT: {
        $aptTypeLabel = 'апатраменти';
        break;
    }
    case Flat::TYPE_OFFICE: {
        $aptTypeLabel = 'офіс';
        break;
    }
    case Flat::TYPE_PARKING: {
        $aptTypeLabel = 'паркінг';
        break;
    }
    case Flat::TYPE_STORAGE: {
        $aptTypeLabel = 'комору';
        break;
    }
    default: {
        break;
    }
}

?><html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            p {
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body style="font-size: 12px">
        <?php /*<p style="font-size: 9px">Квитанция №<?= $model->uid ?></p>
        <p style="font-size: 12px">от <?= $model->getUidDate() ?></p>
        <p style="font-size: 12px">на сумму <?= PriceHelper::format($model->price, true, false, '') ?> <?= $model->cashbox->currency ?></p>
        <p style="font-size: 12px; font-weight: bold">итого <?= PriceHelper::format($model->price * $model->rate, true, false, '') ?> UAH</p>
        <p style="font-size: 12px; font-style: italic; color: gray">тестовый шаблон</p>
        <?php */ ?>
        
        <table style="font-size: 10px">
            <tr>
                <td></td>
                <td>
                    <div>Додаток 2<br/>
                        до Положення про ведення касових<br/>
                        операцій у національній валюті в Україні
                    </div>
                    <span style="font-weight: bold; text-align: right;">Типова форма N КО-1</span>
                </td>
            </tr>
        </table>
        <table style="font-size: 10px">
            <tr>
                <td></td>
                <td colspan="4">
                    <p>Ідентифікаційний<br/>
                        код ЄДРПОУ <u> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            35131579 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                        </u>
                    </p>
                </td>
                <td></td>
            </tr>
        </table>
        
        <br/><br/>
        
        <table style="font-size: 10px" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="10"><u> <?= $companyName ?> </u><br/>
                    <span style="font-size:6px">(найменування підприємства (установи, організації))</span>
                    <br/><br/>
                    <p style="text-align: center"><span style="font-size: 10px; font-weight: bold">Прибутковий касовий ордер №<u><?= $model->uid ?></u></span><br/>
                        <span>від "<u> <?= date('d', strtotime($model->uid_date)) ?> </u>" <u><?= DateHelper::getMonthName(date('n', strtotime($model->uid_date)), false, true, 'ua') ?></u> <u><?= date('Y', strtotime($model->uid_date)) ?></u>р.</span></p>

                    <table style="font-size: 8px; border: 1px solid #000;" cellpadding="1">
                        <tr>
                            <td border="1">Кореспон- дуючий рахунок, субраху- нок</td>
                            <td border="1">Код аналі- тичного рахунку</td>
                            <td border="1">Сума цифрами</td>
                            <td border="1">Код цільового призна- чення</td>
                            <td border="1"></td>
                        </tr>
                        <tr style="font-size: 7px">
                            <td style="border: none">
                                <br/><br/>
                            </td>
                            <td style="border: none">
                                <br/><br/>
                            </td>
                            <td style="border: none; text-align: center">
                                <?= $priceUah ?> грн
                            </td>
                            <td style="border: none">
                                <br/><br/>
                            </td>
                            <td style="border: none">
                                <br/><br/>
                            </td>
                        </tr>
                    </table>
                    <br/><br/><br/>
                    
                    
                    <p style="font-size: 8px">Прийнято від <?php if($model->client->fullname !== null && !empty($model->client->fullname)) { echo $model->client->fullname; } else { echo $model->flat->client->fullname; } ?><br/>
                        Підстава: <u><span> </span> <?php if($model->description === null || empty($model->description)) { echo 'Черговий внесок за'; } else { echo $model->description; } ?> <?php // echo $aptTypeLabel ?> № <?= $model->flat->numberWithIndex ?>, <?= $model->flat->house->section ? ('секції ' . $model->flat->house->section) : '' ?>, <?= $model->flat->floor ? ('поверх ' . $model->flat->floor) : '' ?> 
                            за адресою <?= $model->flat->house->address ?> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <br/>
                            Сума <?= PriceHelper::text($priceUah, 'UAH', false) ?> гривень<?= $model->cashbox->currency != Cashbox::CURRENCY_UAH ? (', еквівалент ' . round($model->price, 2) . ' ' . $model->cashbox->currency) : '' ?> <span> </span> <span> </span> <span> </span> <span> </span> 
                        </u>
                    </p>
                </td>
                <td width="5"> </td>
                <td width="10" valign="middle" style="text-align: center; border-left: 1px solid #000; border-right: 1px solid #000;">
                    <br/><br/><br/><br/><br/>
                    л<br/>i<br/>н<br/>i<br/>я<br/><br/><br/>в<br/>i<br/>д<br/>р<br/>i<br/>з<br/>у
                </td>
                <td width="5"> </td>
                <td colspan="10"><u> <?= $companyName ?> </u><br/>
                    <span style="font-size:6px">(найменування підприємства (установи, організації))</span>
                    <p style="text-align: center">Квитанція</p>до прибуткового касового ордера №<u><?= $model->uid ?></u><br/>
                    від "<u> <?= date('d', strtotime($model->uid_date)) ?> </u>" <u><?= DateHelper::getMonthName(date('n', strtotime($model->uid_date)), false, true, 'ua') ?></u> <u><?= date('Y', strtotime($model->uid_date)) ?></u>р.

                    <p style="font-size: 8px">Прийнято від <?php if($model->client->fullname !== null && !empty($model->client->fullname)) { echo $model->client->fullname; } else { echo $model->flat->client->fullname; } ?><br/>
                        Підстава: <u><span> </span> <?php if($model->description === null || empty($model->description)) { echo 'Черговий внесок за'; } else { echo $model->description; } ?> <?php // echo $aptTypeLabel ?> № <?= $model->flat->numberWithIndex ?>, <?= $model->flat->house->section ? ('секції ' . $model->flat->house->section) : '' ?>, <?= $model->flat->floor ? ('поверх ' . $model->flat->floor) : '' ?> 
                            за адресою <?= $model->flat->house->address ?> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> <span> </span> 
                            <br/>
                            Сума <?= PriceHelper::text($priceUah, 'UAH', false) ?> гривень<?= $model->cashbox->currency != Cashbox::CURRENCY_UAH ? (', еквівалент ' . round($model->price, 2) . ' ' . $model->cashbox->currency) : '' ?> <span> </span> <span> </span> <span> </span> <span> </span> 
                        </u>
                    </p>
                    <p style="font-size: 8px"><span> </span> <span> </span> <span> </span> М.П.</p>
                    <p style="font-size: 8px">Директор<br/>
                        ________________________________________________________<br/>
                        <span style="font-size:6px">
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span>
                            (підпис, прізвище, ініціали)</span>
                    </p>
                    <p style="font-size: 8px">Касир<br/>
                        ________________________________________________________<br/>
                        <span style="font-size:6px">
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span>
                            (підпис, прізвище, ініціали)</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="23">
                    <p style="font-size: 8px">Головний бухгалтер 
                        _____________________________________________________________<br/>
                        <span style="font-size:6px">
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span>
                            (підпис, прізвище, ініціали)</span>
                    </p>
                    <p style="font-size: 8px">Одержав касир 
                        _____________________________________________________________<br/>
                        <span style="font-size:6px">
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span> <span> </span>
                            <span> </span> <span> </span>
                            (підпис, прізвище, ініціали)</span>
                    </p>
                </td>
            </tr>
        </table>
     
        
    </body>
</html>