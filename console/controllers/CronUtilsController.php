<?php

namespace console\controllers;

use yii\console\Controller,
    yii\helpers\Console;
use common\models\Payment;

class CronUtilsController extends Controller {

    // Set Saldo = Plan if pay data was
    public function actionGenerateSaldo() {
        foreach (Payment::find()->where(['<', 'pay_date', date('Y-m-d')])->andWhere(['<=', 'price_fact', 0])->each(10) as $payment) {

            if ($payment->price_plan > 0) {
                $payment->price_saldo = 0 - $payment->price_plan;
                $payment->updated_at = time();
                $payment->update();
            }
        }

        $this->stdout('Refresh saldo was finished!' . PHP_EOL, Console::FG_GREEN);
    }

}
