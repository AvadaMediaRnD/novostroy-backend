<?php

namespace console\controllers;

use Yii,
    yii\console\Controller,
    yii\helpers\Console;
use common\models\Payment,
    common\models\Flat;

class CleanController extends Controller {

    // Clean empty payment without flats
    public function actionCleanPayments() {
        Yii::$app->db->createCommand('DELETE FROM `payment` WHERE `flat_id` IS NULL')->execute();
        Yii::$app->db->createCommand('SELECT * FROM `invoice` WHERE `flat_id` IS NULL')->execute();
        $this->stdout('Clean wrong payments was finished!' . PHP_EOL, Console::FG_GREEN);
    }

    // Correction price paln sign
    public function actionPaymentplanSigh() {
        // Test price plan sign
        foreach (Payment::find()->each(10) as $pay) {
            if ($pay->price_plan < 0) {
                $pay->price_plan = abs($pay->price_plan);
                $pay->update();
            }
        }

        // Test price fact sign
        foreach (Payment::find()->each(10) as $pay) {
            if ($pay->price_fact < 0) {
                $pay->price_fact = abs($pay->price_fact);
                $pay->update();
            }
        }

        // Set price saldo
        foreach (Payment::find()->each(10) as $pay) {           
            if ($pay->pay_date < date('Y-m-d')) {
                $pay->price_saldo = $pay->price_fact - $pay->price_plan;
            }
            else {
                if($pay->price_fact <= 0) {
                    $pay->price_saldo = 0.0000;
                }
                else {
                    $pay->price_saldo = $pay->price_fact - $pay->price_plan;
                }
            }
            $pay->update();
        }

        $this->stdout('Clean wrong payments sing was finished!' . PHP_EOL, Console::FG_GREEN);
    }

    // Remove zero pland and fact
    public function actionRemoveZero() {
        // Set price saldo
        foreach (Payment::find()->each(10) as $pay) {
            if ($pay->price_plan < 1 and $pay->price_fact < 1) {
                \common\models\Invoice::deleteAll(['payment_id' => $pay->id]);
                $pay->delete();
            }
        }

        $this->stdout('Remove wrong payments was finished!' . PHP_EOL, Console::FG_GREEN);
    }

    // Set price paid out
    public function actionSetPaidout() {
        foreach (Flat::find()->each(10) as $flat) {

            $paidInit = $flat->price_paid_init ?? 0;
            $square = ($flat->square == 0) ? 1 : $flat->square;

            if ($flat->price_sell_m <= 0) {
                if ($flat->price_m >= 0) {
                    $price = $flat->price_m;
                } else {
                    $price = 0;
                }
            } else {
                $price = $flat->price_sell_m;
            }

            $totalPrice = $price * $square;

            $paidOut = $totalPrice - $paidInit;

            if ($paidOut < 2) {
                $paidOut = 0;
            }

            $flat->price_paid_out = $paidOut;

            $flat->update();
        }

        $this->stdout('Set paid_out was finished!' . PHP_EOL, Console::FG_GREEN);
    }

    // Correctio payment sum in plan for flats
    public function actionCorrectionPayplans() {
        foreach (Flat::find()->groupBy(['id'])->each(10) as $flat) {
            $square = ($flat->square == 0) ? 1 : $flat->square;
            if ($flat->price_sell_m <= 0) {
                if ($flat->price_m >= 0) {
                    $price = $flat->price_m;
                } else {
                    $price = 0;
                }
            } else {
                $price = $flat->price_sell_m;
            }

            $totalPrice = $price * $square;

            $sumPayment = Payment::find()->where(['flat_id' => $flat->id])->sum('price_plan');

            $different = abs($totalPrice) - abs($sumPayment);

            if ($different < -50) {
                $this->stdout($flat->id . ' price:' . $totalPrice . ' payment:' . $sumPayment . PHP_EOL, Console::FG_GREEN);
            }
        }

        $this->stdout('Correction payment sum in plan was finished!' . PHP_EOL, Console::FG_GREEN);
    }

}
