<?php

namespace console\controllers;
use common\models\Cashbox;
use yii\console\Controller;

class PrivatBankController extends Controller
{
    /**
     * Cron task to update Cashbox rates
     */
    public function actionUpdateRates()
    {
        $result = Cashbox::syncRatesOnPrivatbankApi();
        print($result ? "\nRates updated" : "\nRates not updated");
    }

}
