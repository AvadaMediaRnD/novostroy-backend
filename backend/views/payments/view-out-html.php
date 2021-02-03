<?php

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
            $aptTypeLabel = 'комерцийне приміщення';
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
?>
<!doctype html>
<html lang="ru">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    </head>
    <body style="color: #000; font-size: 10px; font-style: italic;">

        <p style="font-size: 12px; font-weight: bold; text-align: center;">АКТ<br/>Об оказанных услугах</p>

        <table style="width: 100%; border: none;">
            <tr>
                <td>г. Одесса</td>
                <td style="text-align: right;">___________</td>
            </tr>
            <tr>
                <td>Заказчик: ОК «ГРАНИТ»</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>Исполнитель: АН Доминанта</td>
                <td>&nbsp;</td>
            </tr>
        </table>

		<div style="line-height: 10px;"></div>

        <ol>
            <li>Исполнитель, передал, а Заказчик принял услуги в соответствии с Договором о предоставлении услуг от <?= $model->uid_date ?> года.</li>
        </ol>

		<p style="text-align: center;">Информация о заинтересованном лице:</p>

        <table style="width: 100%; border: 1px solid #000; border-collapse: collapse;" cellpadding="5">
            <tr>
                <td style="width: 260px; border: 1px solid #000;">Дата фиксации клиента</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;"></td>
            </tr>
            <tr>
                <td style="width: 260px; border: 1px solid #000;">ФИО заинтересованного лица</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;"><?= $model->flat->client->fullname ?></td>
            </tr>
            <tr>
                <td style="width: 260px; border: 1px solid #000;">Номер договора, подписанного заинтересованным лицом</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;"></td>
            </tr>
            <tr>
                <td style="width: 260px; border: 1px solid #000;">Дата договора, подписанного заинтересованным лицом</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;"></td>
            </tr>
            <tr>
                <td style="width: 260px; border: 1px solid #000;">Параметры недвижимости ( Адрес, номер, секция, этаж, площадь)</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;"><?= $model->flat->house->address ?> <?= $model->flat->numberWithIndex ? (', № ' . $model->flat->numberWithIndex) : '' ?> <?= $model->flat->house->section ? (', секция ' . $model->flat->house->section) : '' ?> <?= $model->flat->floor ? (', этаж ' . $model->flat->floor) : '' ?>, <?= $model->flat->square ?>м<sup>2</sup></td>
            </tr>
            <tr>
                <td style="width: 260px; border: 1px solid #000;">Стоимость имущественных прав</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;"><?php if($model->flat->square > 0) { echo round($model->flat->price_sell_m * $model->flat->square, 2); } else {  echo round($model->flat->price_sell_m, 2);  } ?> у.е.</td>
            </tr>
        </table>

        <ol start="2">
            <?php $typeWho = (isset($model->agency_id) && !empty($model->agency_id)) ? 'agency' : 'manager'; ?>
            <?php
            switch ($typeWho) {
                case 'agency':
                    $payType = ($model->flat->commission_agency_type == Flat::COMMISSION_TYPE_PERCENT) ? 'percent' : 'fixprice';
                    $payVal = $model->flat->commission_agency ?? 0;
                    break;
                case 'manager':
                    $payType = ($model->flat->commission_manager_type == Flat::COMMISSION_TYPE_PERCENT) ? 'percent' : 'fixprice';
                    $payVal = $model->flat->commission_manager ?? 0;
                    break;
                default:
                    $payType = ($model->flat->commission_agency_type == Flat::COMMISSION_TYPE_PERCENT) ? 'percent' : 'fixprice';
                    $payVal = $model->flat->commission_agency ?? 0;
                    break;
            }
            ?>
            <?php
            switch ($payType) {
                case 'percent': echo '<li>Размер Вознаграждения Исполнителя ' . $payVal . '% от суммы Договора составляет ' . round($model->price, 2) . '  у.е. без НДС.</li>';
                    break;
                case 'fixprice': echo '<li>Размер Вознаграждения Исполнителя составляет ' . round($model->price, 2) . '  у.е. без НДС.</li>';
                    break;
                default: echo '<li>Размер Вознаграждения Исполнителя составляет ' . round($model->price, 2) . '  у.е. без НДС.</li>';
                    break;
            }
            ?>
            <li>Заказчик не имеет претензий к Исполнителю.</li>
            <li>Исполнитель не имеет претензий к Заказчику.</li>
        </ol>

        <div style="line-height: 10px;"></div>
        <div style="line-height: 10px;"></div>
        <div style="line-height: 10px;"></div>

        <table style="width: 100%; border: none; text-align: center;">
            <tr>
                <td>_____________________</td>
                <td>_____________________</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Заказчик</td>
                <td style="font-weight: bold;">Исполнитель</td>
            </tr>
        </table>

        <div style="line-height: 10px;"></div>
        <div style="line-height: 10px;"></div>
        <div style="line-height: 10px;"></div>
        <div style="line-height: 10px;"></div>
        <div style="line-height: 10px;"></div>
        <div style="line-height: 10px;"></div>

        <p style="font-style: normal; margin-top: 200px;">Заказчик___________________</p>
    </body>
</html>