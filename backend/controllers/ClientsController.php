<?php
namespace backend\controllers;

use Yii;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Client;
use backend\models\ClientSearch;
use backend\models\ClientImportForm;

/**
 * Clients controller
 */
class ClientsController extends ZController
{
    /**
     * Displays clients index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $clientsCount = Client::find()->count();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientsCount' => $clientsCount,
        ]);
    }
    
    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();
        
        if ($model->load(Yii::$app->request->post())) {
            $model->birthdate = date('Y-m-d', strtotime($model->birthdate));
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                $model->birthdate = date('d.m.Y', strtotime($model->birthdate));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $model->birthdate = date('d.m.Y', strtotime($model->birthdate));
        
        if ($model->load(Yii::$app->request->post())) {
            $model->birthdate = date('Y-m-d', strtotime($model->birthdate));
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Данные сохранены');
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                $model->birthdate = date('d.m.Y', strtotime($model->birthdate));
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * Deletes an existing Client model.
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
     * Import xlsx file
     */
    public function actionImport()
    {
        $importForm = new ClientImportForm();
        
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
     * 
     * @param integer $id
     * @return array
     */
    public function actionAjaxGetClient($id)
    {
        if (!$id || (int)$id == 0) {
            return null;
        }
        $model = $this->findModel($id);
        return $model;
    }
    
    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    } 
}
