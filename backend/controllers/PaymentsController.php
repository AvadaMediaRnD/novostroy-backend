<?php

namespace backend\controllers;

use Yii,
    yii\web\NotFoundHttpException,
    yii\widgets\ActiveForm;
use backend\models\InvoiceSearch,
    backend\controllers\ZController;
use common\models\Cashbox,
    common\models\Payment,
    common\models\Rieltor,
    common\models\Flat,
    common\models\Article,
    common\models\User,
    common\models\Invoice;

/**
 * Payments controller
 */
class PaymentsController extends ZController {

    /**
     * Displays payments index page.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dateFrom = date('Y-m-d');
        $dateTo = date('Y-m-d');
        if ($searchModel->searchUidDateRange) {
            $dates = explode(' - ', $searchModel->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $dateFrom = date('Y-m-d', $tsFrom);
            $dateTo = date('Y-m-d', $tsTo);
        }

        $totalBalance = Invoice::getTotalBalance(null, $dateFrom, $dateTo);
        $totalBalanceUah = Invoice::getTotalBalance(3, $dateFrom, $dateTo);
        $totalBalanceUsd = Invoice::getTotalBalance(1, $dateFrom, $dateTo);
        $totalBalanceEur = Invoice::getTotalBalance(2, $dateFrom, $dateTo);
        $totalIn = Invoice::getTotalIn(null, $dateFrom, $dateTo);
        $totalInUah = Invoice::getTotalIn(3, $dateFrom, $dateTo);
        $totalInUsd = Invoice::getTotalIn(1, $dateFrom, $dateTo);
        $totalInEur = Invoice::getTotalIn(2, $dateFrom, $dateTo);
        $totalOut = Invoice::getTotalOut(null, $dateFrom, $dateTo);
        $totalOutUah = Invoice::getTotalOut(3, $dateFrom, $dateTo);
        $totalOutUsd = Invoice::getTotalOut(1, $dateFrom, $dateTo);
        $totalOutEur = Invoice::getTotalOut(2, $dateFrom, $dateTo);

        /**
         * Informer widget filter
         */
        $informerFilter = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'totalBalance' => $totalBalance,
                    'totalBalanceUah' => $totalBalanceUah,
                    'totalBalanceUsd' => $totalBalanceUsd,
                    'totalBalanceEur' => $totalBalanceEur,
                    'totalIn' => $totalIn,
                    'totalInUah' => $totalInUah,
                    'totalInUsd' => $totalInUsd,
                    'totalInEur' => $totalInEur,
                    'totalOut' => $totalOut,
                    'totalOutUah' => $totalOutUah,
                    'totalOutUsd' => $totalOutUsd,
                    'totalOutEur' => $totalOutEur,
                    'informerFilter' => $informerFilter,
        ]);
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $type in/out
     * @param integer $invoice_id
     * @param integer $flat_id
     * @param integer $agency_id
     * @param integer $user_id
     * @param integer $payment_id
     * @return mixed
     */
    public function actionCreate($type = '', $invoice_id = null, $flat_id = null, $agency_id = null, $user_id = null, $payment_id = null) {
        $model = new Invoice();
        $post = Yii::$app->request->post();
        if (isset($invoice_id)) {
            $modelClone = Invoice::findOne($invoice_id);
            if ($modelClone) {
                $model->setAttributes($modelClone->attributes);
            }
            $model->id = null;
            $model->uid = null;
        } else {
            if ($type == Invoice::TYPE_INCOME || $type == Invoice::TYPE_OUTCOME) {
                $model->type = $type;
            }
        }

        $model->generateUid();
        $model->uid_date = Yii::$app->formatter->asDate($model->uid_date ? strtotime($model->uid_date) : time());
        $model->status = Invoice::STATUS_COMPLETE;


        if (!isset($agency_id))
            $agency_id = $model->agency_id ?? null;
        if (!isset($user_id))
            $user_id = $model->user_id ?? null;

        $model->agency_id = $agency_id;
        $model->user_id = $user_id;

        $cashbox = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);

        // outcome commissions for user, agency
        $flatModel = Flat::findOne($flat_id);
        if ($flatModel) {
            $model->flat_id = $flatModel->id;
            $model->article_id = $agency_id ? Article::getIdCommissionAgency() : Article::getIdCommissionManager();
            $model->cashbox_id = $cashbox ? $cashbox->id : null;
            $model->price = $agency_id ? $flatModel->getCommissionAgencyPrice() : $flatModel->getCommissionManagerPrice();
        }

        // income for flat payment
        $paymentModel = Payment::findOne($payment_id);
        if ($paymentModel) {
            $model->article_id = Article::getIdPaymentFlat();
            $flatModel = $paymentModel->flat;
            $model->flat_id = $flatModel->id;
            $model->payment_id = $paymentModel->id;
            $model->client_id = $paymentModel->flat->client_id;
            $model->cashbox_id = $cashbox ? $cashbox->id : null;
            $model->rate = $cashbox ? $cashbox->rate : 0;
            $model->price = $paymentModel->price_plan;
        }

        if (!$model->company_name && isset($model->flat)) {
            $model->company_name = $model->flat->house->company_name;
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }

        if ($model->load($post)) {
            if ($model->cashbox_id) {
                $cashbox = Cashbox::findOne($model->cashbox_id);
            } else {
                $cashbox = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);
                $model->cashbox_id = $cashbox ? $cashbox->id : null;
            }

            $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
            if (!$model->rate) {
                $model->rate = $cashbox ? $cashbox->rate : 0;
            }
            if ($model->save()) {

                //// LOG
                $model->saveLog(__METHOD__ . ' payment_id:' . $payment_id);
                //// END LOG

                if ($model->flat_id) {
                    // payment
                    $paymentModel = $model->payment;
                    if (!$paymentModel) {
                        $paymentModel = new Payment();
                        $paymentModel->pay_date = date('Y-m-d');
                        $paymentModel->pay_number = Payment::find()->where(['flat_id' => $model->flat_id])->max('pay_number') + 1;
                        $paymentModel->flat_id = $model->flat_id;
                        $paymentModel->price_plan = $model->price;
                    }
                    $paymentModel->save();

                    $model->payment_id = $paymentModel->id;
                    $model->save(false);
                }

                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['update', 'id' => $model->id]);
            }
            // format back if errors
            $model->uid_date = Yii::$app->formatter->asDate($model->uid_date ? strtotime($model->uid_date) : time());
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $type in/out
     * @param integer $invoice_id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $model->uid_date = Yii::$app->formatter->asDate($model->uid_date ? strtotime($model->uid_date) : time());
        if (!$model->company_name) {
            $model->company_name = $model->flat->house->company_name;
        }

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }

        if ($model->load($post)) {
            if ($model->cashbox_id) {
                $cashbox = Cashbox::findOne($model->cashbox_id);
            } else {
                $cashbox = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);
                $model->cashbox_id = $cashbox ? $cashbox->id : null;
            }

            $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
            if (!$model->rate) {
                $model->rate = $cashbox ? $cashbox->rate : 0;
            }
            if ($model->save()) {

                if ($model->flat_id) {
                    // payment
                    $paymentModel = $model->payment;
                    if (!$paymentModel) {
                        $paymentModel = new Payment();
                        $paymentModel->pay_date = date('Y-m-d');
                        $paymentModel->pay_number = Payment::find()->where(['flat_id' => $model->flat_id])->max('pay_number') + 1;
                        $paymentModel->flat_id = $model->flat_id;
                        $paymentModel->price_plan = $model->price;
                    }
                    $paymentModel->save();

                    $model->payment_id = $paymentModel->id;
                    $model->save(false);
                }

                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['update', 'id' => $model->id]);
            }
            // format back if errors
            $model->uid_date = Yii::$app->formatter->asDate($model->uid_date ? strtotime($model->uid_date) : time());
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Invoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $paymentId = $model->payment_id ?? 0;

        $model->delete();
        $modelPayment = Payment::findOne(['id' => $paymentId]);
        if($modelPayment->pay_date < date('Y-m-d') && $modelPayment->price_plan > 0) {
            $saldo = 0 - $modelPayment->price_plan;
        }
        else {
            $saldo = 0.000;
        }
        
        $modelPayment->price_fact = 0.000;
        $modelPayment->price_saldo = $saldo;
        $modelPayment->update();

        return $this->redirect(['index']);
    }

    /**
     * Print an Invoice model to file and download
     * @param integer $id
     * @param string $format docx, pdf
     * @return mixed
     */
    public function actionPrint($id, $type = 'in', $format = 'pdf', $html = false) {
        $this->layout = false;
        $model = $this->findModel($id);

        if ($type === 'in') {
            $view = 'view-html';
        } else {
            $view = 'view-out-html';
        }

        $content = $this->render($view, [
            'model' => $model,
        ]);
        if ($html) {
            return $content;
        }

        if ($format == 'pdf') {
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetFont('dejavusans');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->AddPage();
            $pdf->writeHTML($content, false, false, true, false, '');
            $pdf->lastPage();
            $filePath = Yii::getAlias('@frontend/web/upload/Invoice/');
            $fileName = 'invoice-' . $model->id . '.pdf';
            return $pdf->Output($fileName, 'D');
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * 
     */
    public function actionAjaxDelete($id) {
        $model = Invoice::findOne($id);

        if ($model) {
            $paymentId = $model->payment_id ?? 0;     
            $model->delete();
            Payment::updateAll(['price_fact' => 0.000],['id' => $paymentId]);
        }

        return true;
    }

    /**
     * @param $cashbox_id
     * @return array
     */
    public function actionAjaxGetRate($cashbox_id = null) {
        $cashboxModel = Cashbox::findOne($cashbox_id);

        return [
            'rate' => $cashboxModel->rate,
        ];
    }

    /**
     * @param $payment_id
     * @return array
     */
    public function actionAjaxGetPrice($payment_id = null) {
        $paymentModel = Payment::findOne($payment_id);

        return [
            'price' => round($paymentModel->price_plan, 2),
        ];
    }

    /**
     * @param $type
     * @param $invoice_id
     * @param $agency_id
     * @param $user_id
     * @return array
     */
    public function actionAjaxGetCounterparties($type = null, $invoice_id = null, $agency_id = null, $user_id = null) {
        $rieltorsData = '<option value="">Выберите...</option>' . "\r\n";
        $usersData = '<option value="">Выберите...</option>' . "\r\n";

        $invoiceModel = Invoice::findOne($invoice_id);

        switch ($type) {
            case 1: {
                    $usersQuery = User::find()->where(['in', 'role', [User::ROLE_SALES_MANAGER, User::ROLE_MANAGER]])->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC]);
                    foreach ($usersQuery->all() as $user) {
                        $usersData .= '<option value="' . $user->id . '">' . $user->fullname . '</option>' . "\r\n";
                    }
                    break;
                }
            case 2: {
                    $rieltorsQuery = Rieltor::find()->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC]);
                    $rieltorsQuery->andFilterWhere(['agency_id' => $agency_id]);
                    foreach ($rieltorsQuery->all() as $rieltor) {
                        $rieltorsData .= '<option value="' . $rieltor->id . '">' . $rieltor->fullname . '</option>' . "\r\n";
                    }
                    break;
                }
            case 3: {
                    $usersQuery = User::find()->where(['in', 'role', [User::ROLE_ADMIN, User::ROLE_FIN_DIRECTOR]])->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC]);
                    foreach ($usersQuery->all() as $user) {
                        $usersData .= '<option value="' . $user->id . '">' . $user->fullname . '</option>' . "\r\n";
                    }
                    break;
                }
            case 4: {
                    $usersQuery = User::find()->where(['in', 'role', [User::ROLE_ACCOUNTANT]])->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC]);
                    foreach ($usersQuery->all() as $user) {
                        $usersData .= '<option value="' . $user->id . '">' . $user->fullname . '</option>' . "\r\n";
                    }
                    break;
                }
            default: {

                    break;
                }
        }

        return [
            'rieltors' => $rieltorsData,
            'users' => $usersData,
            'userId' => $invoiceModel->user_id ?: $user_id,
            'rieltorId' => $invoiceModel->rieltor_id,
            'agencyId' => $invoiceModel->agency_id ?: $agency_id,
        ];
    }

    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
