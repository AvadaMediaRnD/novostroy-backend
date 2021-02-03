<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\ViewHouseTotal;
use common\models\ViewTotalPlanFact;
use backend\models\FlatSearch;
use common\models\Flat;
use backend\models\FlatForm;
use common\models\Payment;
use backend\models\FlatImportForm;
use common\models\Client;
use common\models\Agency;
use common\models\Cashbox;
use yii\web\NotFoundHttpException;

/**
 * Flats controller
 */
class FlatsController extends ZController {

    /**
     * Displays flats index page.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new FlatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $houseIds = Yii::$app->user->identity->getHouseIds();

        $countQuery = ViewTotalPlanFact::find();
        $countQuery->andWhere(['or', ['in', 'house_id', $houseIds], ['is', 'house_id', null]]);
        $house_id = $searchModel->house_id;
        if (!$house_id) {
            $countQuery->select([
                'house_id',
                'year',
                'month',
                'year_month',
                new \yii\db\Expression('SUM(`price_plan_total`) AS `price_plan_total`'),
                new \yii\db\Expression('SUM(`price_fact_total`) AS `price_fact_total`'),
                new \yii\db\Expression('SUM(`price_saldo_total`) AS `price_saldo_total`'),
                new \yii\db\Expression('SUM(`price_debt_total`) AS `price_debt_total`')
            ]);
        }
        //$countQuery->andFilterWhere(['>=', 'year_month', $ymFrom]);
        //$countQuery->andFilterWhere(['<=', 'year_month', $ymTo]);
        $countQuery->andFilterWhere(['house_id' => $house_id]);
        $countQuery->groupBy('year_month');

        // informers
        $houseTotalsQuery = ViewHouseTotal::find()->select([
            new \yii\db\Expression('SUM(`flats_total`) AS `flats_total`'),
            new \yii\db\Expression('SUM(`flats_available`) AS `flats_available`'),
            new \yii\db\Expression('SUM(`flats_sold`) AS `flats_sold`'),
            new \yii\db\Expression('SUM(`square_total`) AS `square_total`'),
            new \yii\db\Expression('SUM(`square_available`) AS `square_available`'),
            new \yii\db\Expression('SUM(`square_sold`) AS `square_sold`'),
            new \yii\db\Expression('SUM(`price_total`) AS `price_total`'),
            new \yii\db\Expression('SUM(`price_available`) AS `price_available`'),
            new \yii\db\Expression('SUM(`price_sold`) AS `price_sold`'),
        ]);
        $houseTotalsQuery->andWhere(['or', ['in', 'id', $houseIds], ['is', 'id', null]]);
        $houseTotalsQuery->andFilterWhere(['id' => $house_id]);
        $houseTotals = $houseTotalsQuery->asArray()->one();

        $flatsTotal = $houseTotals['flats_total'];
        $flatsTotalAvailable = $houseTotals['flats_available'];
        $flatsTotalSold = $houseTotals['flats_sold'];
        $squareTotal = round($houseTotals['square_total']);
        $squareTotalAvailable = round($houseTotals['square_available']);
        $squareTotalSold = round($houseTotals['square_sold']);
        $priceTotalPlan = $countQuery->sum('price_plan_total');
        $priceTotalFact = $countQuery->sum('price_fact_total');
        $priceTotalRemain = $countQuery->sum('price_debt_total'); //$houseTotals['price_available'];
        
        
        /**
         * Widget from flats
         */
        $priceTotalPlan = $dataProvider->query->sum('`price_sell_m` * `square`');
        
        /**
         * Informer widget filter
         */
        $informerFilter = (isset(Yii::$app->request->queryParams['FlatSearch'])) ? Yii::$app->request->queryParams['FlatSearch'] : '';
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'flatsTotal' => $flatsTotal,
            'flatsTotalAvailable' => $flatsTotalAvailable,
            'flatsTotalSold' => $flatsTotalSold,
            'squareTotal' => $squareTotal,
            'squareTotalAvailable' => $squareTotalAvailable,
            'squareTotalSold' => $squareTotalSold,
            'priceTotalPlan' => $priceTotalPlan,
            'priceTotalFact' => $priceTotalFact,
            'priceTotalRemain' => $priceTotalRemain,
            'informerFilter' => $informerFilter,
        ]);
    }

    /**
     * Creates a new Flat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Flat();
        $modelForm = new FlatForm();
        $modelForm->scenario = 'create';
        $modelForm->load(['FlatForm' => $model->attributes]);
        $modelForm->unit_type = Flat::TYPE_FLAT;
        $modelForm->floor = 0;
        $modelForm->status = Flat::STATUS_UNAVAILABLE;

        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            Yii::$app->session->setFlash('success', 'Данные сохранены');
            return $this->redirect(['update', 'id' => $modelForm->id]);
        }

        return $this->render('create', [
                    'model' => $model,
                    'modelForm' => $modelForm,
        ]);
    }

    /**
     * Updates an existing Flat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $payment_id
     * @return mixed
     */
    public function actionUpdate($id, $payment_id = null) {
        $model = $this->findModel($id);
        $modelForm = new FlatForm();
        $modelForm->load(['FlatForm' => $model->attributes]);
        $modelForm->agency_id = (isset($modelForm->agency_id)) ? $modelForm->agency_id : 0;
        $modelForm->user_id = (isset($model->client->user_id)) ? $model->client->user_id : 0;

        $modelForm->price_m = round($modelForm->price_m, 2);
        $modelForm->price_sell_m = round($modelForm->price_sell_m, 2);
        $modelForm->price_discount_m = round($modelForm->price_discount_m, 2);
        $modelForm->price_paid_init = round($modelForm->price_paid_init, 2);

        $modelForm->price_total = round($model->getPrice(), 2);
        $modelForm->price_sell_total = round($model->getPriceSell(), 2);
        $modelForm->price_discount_total = round($model->getPriceDiscount(), 2);

        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            Yii::$app->session->setFlash('success', 'Данные сохранены');
            return $this->redirect(['update', 'id' => $modelForm->id]);
        }

        if ($payment_id) {
            $payment = Payment::findOne($payment_id);
        } else {
            $payment = new Payment();
        }

        if ($payment->load(Yii::$app->request->post())) {
            $payment->pay_date = date('Y-m-d', strtotime($payment->pay_date));
            $payment->flat_id = $model->id;
            $payment->save();
        }

        return $this->render('update', [
                    'model' => $model,
                    'modelForm' => $modelForm,
        ]);
    }

    /**
     * Deletes an existing Flat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 
     */
    public function actionDeletePayment($id) {
        $model = Payment::findOne($id);
        if ($model) {
            $model->delete();
        }

        return true;
    }

    /**
     * 
     */
    public function actionDeletePakpayment() {

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $arrId = (isset($post['arrElemnts'])) ? $post['arrElemnts'] : [];
            foreach ($arrId as $item) {
                $model = Payment::findOne($item);
                if ($model) {
                    $model->delete();
                }
            }
        }

        return true;
    }

    /**
     * Import xlsx file
     */
    public function actionImport() {
        $importForm = new FlatImportForm();

        if (Yii::$app->request->isAjax) {
            return \yii\widgets\ActiveForm::validate($importForm);
        }

        if (Yii::$app->request->isPost) {
            if ($importForm->load(Yii::$app->request->post())) {
                $importForm->import();
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Create payment commissions for manager or agency
     * @param integer $flat_id flat to pay commission for
     * @param integer $user_id manager
     * @param integer $agency_id agency
     */
    public function actionCreateCommissions($flat_id, $user_id = null, $agency_id = null) {
        $flat = Flat::findOne($flat_id);
        if (!$flat) {
            return $this->goBack();
        }
        if (!$user_id && !$agency_id) {
            return $this->goBack();
        }

        $user = User::findOne($user_id);
        $agency = Agency::findOne($agency_id);

        $cashbox = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);

        if ($user) {
            $user->createCommissionForFlat($flat, $cashbox, true);
        }
        if ($agency) {
            $agency->createCommissionForFlat($flat, $cashbox, true);
        }

        return $this->goBack();
    }

    /**
     * Print a report to file and download.
     * @return mixed
     */
    public function actionReport() {
        $get = Yii::$app->request->get();

        $invoiceFilePath = Flat::getReportFile($get['date_from'], $get['date_to']);
        return Yii::$app->response->sendFile(Yii::getAlias('@frontend/web') . $invoiceFilePath);
    }

    /**
     * Add many payments.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatePaymentsMany($id) {
        $model = $this->findModel($id);

        if ($model && Yii::$app->request->post()) {
            $paymentsData = Yii::$app->request->post('Payment');
            foreach ($paymentsData as $paymentData) {
                $pricePlan = floatval($paymentData['price_plan']);
                $payNumber = (int) $paymentData['pay_number'];
                if ($pricePlan && $payNumber) {
                    $payment = new Payment();
                    $payment->flat_id = $model->id;
                    $payment->pay_number = $payNumber;
                    $payment->pay_date = date('Y-m-d', strtotime($paymentData['pay_date']));
                    $payment->price_plan = $pricePlan;
                    $payment->save();
                }
            }
            Yii::$app->session->setFlash('success', 'Данные сохранены');
        }

        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * @param integer $flat_id
     * @param integer $form_id
     * @param integer $count
     * @return mixed
     */
    public function actionAjaxGetPaymentManyForm($flat_id, $form_id = 1, $pay_date = '', $count = 1) {
        $payDate = $pay_date ? date('Y-m-d', strtotime('+1 month', strtotime($pay_date))) : date('Y-m-d');
        $flat = $this->findModel($flat_id);

        $form = '';
        for ($i = 0; $i < $count; $i++) {
            $model = new Payment();
            $model->flat_id = $flat->id;
            $model->pay_number = $form_id;
            if ($payDate) {
                $model->pay_date = Yii::$app->formatter->asDate($payDate);
            }
            $model->price_plan = round($model->price_plan, 2);

            $form .= $this->renderAjax('_payment-modal-many-form-item', [
                'model' => $model,
                'flatModel' => $flat,
                'k' => $form_id,
            ]);
            $payDate = date('Y-m-d', strtotime('+1 month', strtotime($payDate)));
            $form_id++;
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'form' => $form,
            'payDate' => $payDate,
        ];
    }

    /**
     * @param integer $flat_id
     * @param integer $form_id
     * @return mixed
     */
    public function actionAjaxGetPaymentManyPrice($flat_id, $count = 0) {
        if ($count < 1) {
            $count = 1;
        }
        $flat = $this->findModel($flat_id);
        $paymentPriceLeft = $flat->getPayments()->andWhere(['is_price_left' => 1])->sum('price_plan') / $count;

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'price_plan' => round($paymentPriceLeft, 2),
        ];
    }

    /**
     * 
     */
    public function actionAjaxGetPaymentForm($flat_id, $payment_id = null) {
        $flat = $this->findModel($flat_id);
        $model = Payment::findOne($payment_id);
        if (!$model) {
            $model = new Payment();
            $model->flat_id = $flat->id;
            $model->pay_number = Payment::find()->where(['flat_id' => $flat->id])->max('pay_number') + 1;
            $model->pay_date = date('Y-m-d', time());
        }

        if ($model->pay_date) {
            $model->pay_date = Yii::$app->formatter->asDate($model->pay_date);
        }
        $model->price_plan = round($model->price_plan, 2);

        return $this->renderAjax('_payment-modal-form', [
                    'model' => $model,
                    'flatModel' => $flat,
        ]);
    }

    /**
     * @param $flat_id
     * @return array
     */
    public function actionAjaxGetItems($flat_id = null) {
        $flatModel = Flat::findOne($flat_id);

        $paymentsData = '<option value="">Выберите...</option>' . "\r\n";
        $clientsData = '<option value="">Выберите...</option>' . "\r\n";

        if ($flatModel) {
            $paymentsQuery = Payment::find()->where(['flat_id' => $flatModel->id]);
            foreach ($paymentsQuery->all() as $payment) {
                $paymentsData .= '<option value="' . $payment->id . '">' . $payment->pay_number . '</option>' . "\r\n";
            }

            $clientsQuery = Client::find();
            foreach ($clientsQuery->all() as $client) {
                if ($client->id == $flatModel->client_id) {
                    $clientsData .= '<option value="' . $client->id . '" selected="selected">' . $client->fullname . '</option>' . "\r\n";
                } else {
                    $clientsData .= '<option value="' . $client->id . '">' . $client->fullname . '</option>' . "\r\n";
                }
            }
        }

        return [
            'payments' => $paymentsData,
            'clients' => $clientsData,
            'clientId' => $flatModel->client_id,
        ];
    }

    /**
     * 
     * @param integer $id
     * @return array
     */
    public function actionAjaxGetFlat($id) {
        if (!$id || (int) $id == 0) {
            return null;
        }
        $model = $this->findModel($id);
        $data = $model->attributes;
        $data['address'] = $model->house->address;
        $data['price'] = $model->getPriceSell();
        return $data;
    }

    /**
     * Finds the Flat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Flat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Flat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
