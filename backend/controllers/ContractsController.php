<?php
namespace backend\controllers;

use Yii;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use backend\models\AgreementSearch;
use common\models\Agreement;
use backend\models\AgreementForm;
use backend\models\AgreementTemplateSearch;
use common\models\AgreementTemplate;
use backend\models\AgreementTemplateForm;
use yii\widgets\ActiveForm;
use common\models\Flat;

/**
 * Contracts controller
 */
class ContractsController extends ZController
{
    /**
     * Displays contacts index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AgreementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Creates a new Agreement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $flat_id
     * @return mixed
     */
    public function actionCreate($flat_id = null)
    {
        $model = new Agreement();
        $modelForm = new AgreementForm();
        $modelForm->load(['AgreementForm' => $model->attributes]);
        $modelForm->load(['AgreementForm' => $model->agreementClient->attributes]);
        $modelForm->load(['AgreementForm' => $model->agreementFlat->attributes]);
        $modelForm->status = Agreement::STATUS_DRAFT;
        $modelForm->uid = Agreement::generateUid();
        $modelForm->uid_date = Yii::$app->formatter->asDate($model->uid_date ? strtotime($model->uid_date) : time());
        $modelForm->agreement_template_id = AgreementTemplate::find()->orderBy(['is_default' => SORT_DESC])->limit(1)->asArray()->one()['id'];
                
        if ($flat_id) {
            $flatModel = Flat::findOne($flat_id);
            if ($flatModel) {
                $clientAttributes = $flatModel->client->attributes;
                unset($clientAttributes['id']);
                unset($clientAttributes['status']);
                $flatAttributes = $flatModel->attributes;
                unset($flatAttributes['id']);
                unset($flatAttributes['status']);
                $modelForm->load(['AgreementForm' => $clientAttributes]);
                $modelForm->load(['AgreementForm' => $flatAttributes]);
                $modelForm->flat_id = $flatModel->id;
                $modelForm->flat_address = $flatModel->house->address;
                $modelForm->price = round($flatModel->price, 2);
                $modelForm->birthdate = Yii::$app->formatter->asDate($modelForm->birthdate ? strtotime($modelForm->birthdate) : null);
            }
        }
        
        if (Yii::$app->request->isAjax && $modelForm->load(Yii::$app->request->post())) {
            return ActiveForm::validate($modelForm);
        }
        
        if ($modelForm->load(Yii::$app->request->post())) {
            $modelForm->uid_date = $modelForm->uid_date ? date('Y-m-d', strtotime($modelForm->uid_date)) : null;
            $modelForm->birthdate = $modelForm->birthdate ? date('Y-m-d', strtotime($modelForm->birthdate)) : null;
            if ($modelForm->save()) {
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['update', 'id' => $modelForm->id]);
            }
            // format back if errors
            $modelForm->uid_date = Yii::$app->formatter->asDate($modelForm->uid_date ? strtotime($modelForm->uid_date) : time());
            $modelForm->birthdate = Yii::$app->formatter->asDate($modelForm->birthdate ? strtotime($modelForm->birthdate) : null);
        }

        return $this->render('create', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]);
    }

    /**
     * Updates an existing Agreement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelForm = new AgreementForm();
        $modelForm->load(['AgreementForm' => $model->attributes]);
        $clientAttributes = $model->agreementClient->attributes;
        unset($clientAttributes['id']);
        $flatAttributes = $model->agreementFlat->attributes;
        unset($flatAttributes['id']);
        $modelForm->load(['AgreementForm' => $clientAttributes]);
        $modelForm->load(['AgreementForm' => $flatAttributes]);
        $modelForm->uid_date = Yii::$app->formatter->asDate(strtotime($modelForm->uid_date));
        $modelForm->birthdate = Yii::$app->formatter->asDate(strtotime($modelForm->birthdate));
        $modelForm->flat_address = $model->agreementFlat->address;
        $modelForm->price = round($modelForm->price, 2);
        $modelForm->description = $model->description;
        
        if (Yii::$app->request->isAjax && $modelForm->load(Yii::$app->request->post())) {
            return ActiveForm::validate($modelForm);
        }
        
        if ($modelForm->load(Yii::$app->request->post())) {
            $modelForm->uid_date = $modelForm->uid_date ? date('Y-m-d', strtotime($modelForm->uid_date)) : null;
            $modelForm->birthdate = $modelForm->birthdate ? date('Y-m-d', strtotime($modelForm->birthdate)) : null;
            if ($modelForm->save()) {
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['update', 'id' => $modelForm->id]);
            }
            // format back if errors
            $modelForm->uid_date = Yii::$app->formatter->asDate(strtotime($modelForm->uid_date));
            $modelForm->birthdate = Yii::$app->formatter->asDate(strtotime($modelForm->birthdate));
        }

        return $this->render('update', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]);
    }
    
    /**
     * Updates content of an existing Agreement model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateContent($id)
    {
        $model = $this->findModel($id);
        
        if ($model && Yii::$app->request->post()) {
            $content = Yii::$app->request->post('content');
            $model->writeFileContent($content);
           
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('content', [
            'model' => $model,
        ]);
    }
    
    /**
     * Deletes an existing Agreement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * Displays agreement templates index page.
     *
     * @return string
     */
    public function actionTemplateIndex()
    {
        $searchModel = new AgreementTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('template-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Creates a new AgreementTemplate model.
     *
     * @return string
     */
    public function actionTemplateCreate()
    {
        $model = new AgreementTemplate();
        $modelForm = new AgreementTemplateForm();
        $modelForm->load(['AgreementTemplateForm' => $model->attributes]);
        
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            return $this->redirect(['template-index']);
        }
        
        return $this->render('template-create', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]);
    }
    
    /**
     * Updates a new AgreementTemplate model.
     * @param integer $id
     * @return string
     */
    public function actionTemplateUpdate($id)
    {
        $model = AgreementTemplate::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $modelForm = new AgreementTemplateForm();
        $modelForm->load(['AgreementTemplateForm' => $model->attributes]);
        $modelForm->content = $model->readFileContent();
        
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            return $this->redirect(['template-index']);
        }
        
        return $this->render('template-update', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]);
    }
    
    /**
     * Copy a AgreementTemplate model.
     * @param integer $id
     * @return string
     */
    public function actionTemplateCopy($id)
    {
        $model = AgreementTemplate::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $modelClone = new AgreementTemplate();
        $modelClone->is_default = 0;
        $modelClone->name = $model->name . ' (копия)';
        if ($modelClone->save()) {
            // files 
            $pathFullBase = Yii::getAlias('@frontend/web' . $model->file);
            if (file_exists($pathFullBase)) {
                $pathParts = pathinfo($pathFullBase);
                $path = '/upload/AgreementTemplate/tpl-' . $modelClone->id . '.' . $pathParts['extension']; 
                $pathFull = Yii::getAlias('@frontend/web' . $path);
                $dir = dirname($pathFull);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                file_put_contents($pathFull, file_get_contents($pathFullBase));
                if (file_exists("$pathFullBase.html")) {
                    file_put_contents("$pathFull.html", file_get_contents("$pathFullBase.html"));
                }
                
                $modelClone->file = $path;
                $modelClone->save();
            }
        }
        
        return $this->redirect(['template-index']);
    }
    
    /**
     * @param integer $id
     */
    public function actionTemplateDelete($id)
    {
        $model = AgreementTemplate::findOne($id);
        if ($model) {
            $model->delete();
        }
        
        return $this->redirect(['template-index']);
    }
    
    /**
     * View template as PDF file AgreementTemplate model.
     * @param integer $id
     * @return string
     */
    public function actionTemplateViewPdf($id)
    {
        $this->layout = false;
        $model = AgreementTemplate::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $content = $this->render('template-view-html', [
            'model' => $model,
        ]);
        
//        $dompdf = new \Dompdf\Dompdf();
//        $dompdf->set_option('isHtml5ParserEnabled', true);
//        //$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
//        $dompdf->loadHtml($content, 'UTF-8');
//        $dompdf->render();
//        $output = $dompdf->output();
//        file_put_contents(Yii::getAlias('@frontend/web/upload/AgreementTemplate/tpl-' . $model->id . '.pdf'), $output);
        
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetFont('dejavusans');
        $pdf->AddPage();
        $pdf->writeHTML($content, true, false, true, false, '');
        $pdf->lastPage();
        $filePath = Yii::getAlias('@frontend/web/upload/AgreementTemplate/');
        $fileName = 'agreement-tpl-' . $model->id . '.pdf';
        return $pdf->Output($fileName, 'D');
    }
    
    public function actionTemplateSetDefault($id)
    {
        $model = AgreementTemplate::findOne($id);
        if ($model) {
            $model->is_default = 1;
            $model->save();
        }
        return $this->redirect(['template-index']);
    }
    
    /**
     * Print an Agreement model to file and download
     * @param integer $id
     * @param string $format docx, pdf
     * @return mixed
     */
    public function actionPrint($id, $format = 'pdf')
    {
        $this->layout = false;
        $model = $this->findModel($id);
        
        $content = $this->render('view-html', [
            'model' => $model,
        ]);
        
        $content = str_replace('<p style="', '<p style="line-height: 1; margin: 0; padding: 0;', $content);

        if ($format == 'pdf') {
            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetMargins(10, 10, 10, true);
            $pdf->setCellPaddings(0,0,0,0);
            $pdf->SetFont('freesans');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->AddPage();
            $pdf->writeHTML($content, true, false, true, false, '');
            $pdf->lastPage();
            $filePath = Yii::getAlias('@frontend/web/upload/Agreement/');
            $fileName = 'agreement-' . $model->id . '.pdf';
            
            if (!file_exists($filePath . $fileName)) {
                Yii::$app->session->setFlash('warning', 'Файл договора не найден. Создайте новый файл из шаблона или загрузите файл вручную.');
                return $this->goBack();
            }
            
            return $pdf->Output($fileName, 'D');
        } elseif ($format == 'docx') {
            $filePath = Yii::getAlias('@frontend/web/upload/Agreement/' . $model->id . '/');
            $fileName = 'agreement-' . $model->uid . '.docx';
            
            if (!file_exists($filePath . $fileName)) {
                Yii::$app->session->setFlash('warning', 'Файл договора не найден. Создайте новый или загрузить файл вручную.');
                return $this->goBack();
            }
            
            return Yii::$app->response->sendFile($filePath . $fileName);
        }
        
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * Finds the Agreement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Agreement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agreement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    } 
}
