<?php
namespace backend\controllers;

use Yii;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use backend\models\AgencySearch;
use common\models\Agency;
use common\models\Rieltor;
use backend\models\AgencyImportForm;

/**
 * Agency controller
 */
class AgencyController extends ZController
{
    /**
     * Displays agency index page.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AgencySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $agenciesCount = Agency::find()->count();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'agenciesCount' => $agenciesCount,
        ]);
    }
    
    /**
     * Creates a new Agency model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Agency();
        $model->status = Agency::STATUS_DISABLED;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Данные сохранены');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Agency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $rieltor_id
     * @return mixed
     */
    public function actionUpdate($id, $rieltor_id = null)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Данные сохранены');
            return $this->redirect(['update', 'id' => $model->id]);
        }
        
        if ($rieltor_id) {
            $rieltor = Rieltor::findOne($rieltor_id);
        } else {
            $rieltor = new Rieltor();
        }

        if ($rieltor->load(Yii::$app->request->post())) {
            $rieltor->agency_id = $model->id;
            $rieltor->save();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * Deletes an existing Agency model.
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
     * 
     */
    public function actionDeleteRieltor($id)
    {
        $model = Rieltor::findOne($id);
        if ($model) {
            $model->delete();
        }
        
        return true;
    }
    
    /**
     * Import xlsx file
     */
    public function actionImport()
    {
        $importForm = new AgencyImportForm();
        
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
     */
    public function actionAjaxGetRieltorForm($agency_id, $rieltor_id = null)
    {
        $agency = $this->findModel($agency_id);
        $model = Rieltor::findOne($rieltor_id);
        if (!$model) {
            $model = new Rieltor();
            $model->agency_id = $agency->id;
        }
        
        return $this->renderAjax('_rieltor-modal-form', [
            'model' => $model, 
            'agencyModel' => $agency,
        ]);
    }
    
    /**
     * @param $agency_id
     * @return array
     */
    public function actionAjaxGetItems($agency_id = null)
    {
        $agencyModel = Agency::findOne($agency_id);

        $rieltorsData = '<option value="">Выберите...</option>'."\r\n";
        
        if ($agencyModel) {
            $rieltorsQuery = Rieltor::find()->where(['agency_id' => $agencyModel->id]);
            foreach ($rieltorsQuery->all() as $rieltor) {
                $rieltorsData .= '<option value="' . $rieltor->id . '">' . 'Риелтор ('.$agencyModel->name.') - ' . $rieltor->fullname . '</option>'."\r\n";
            }
        }

        return [
            'rieltors' => $rieltorsData,
        ];
    }
    
    /**
     * Finds the Agency model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Agency the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Agency::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    } 
}
