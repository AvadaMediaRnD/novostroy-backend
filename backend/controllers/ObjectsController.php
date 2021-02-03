<?php

namespace backend\controllers;

use Yii;
use backend\controllers\ZController as Controller,
    backend\models\HouseSearch;
use common\models\House,
    common\models\Flat;

/**
 * Objects controller
 */
class ObjectsController extends Controller {

    /**
     * Lists all House models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new HouseSearch();
        $dataProvider = $searchModel->searchComplex(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all House models.
     * @return mixed
     */
    public function actionIndexHouse() {
        $searchModel = new HouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-house', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single House model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new House model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new House();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index-house', 'HouseSearch[name]' => $model->name]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing House model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(['House' => Yii::$app->request->post('HouseSearch')]) && $model->save()) {
            return $this->redirect(['index-house', 'HouseSearch[name]' => $model->name]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing House model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $houseName = $model->name;
        $model->delete();

        $hasSections = House::find()->where(['name' => $houseName])->exists();
        if ($hasSections) {
            return $this->redirect(['index-house', 'HouseSearch[name]' => $houseName]);
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $house_id
     * @return array
     */
    public function actionAjaxGetItems($house_id = null) {
        $houseModel = House::findOne($house_id);

        $flatsData = '<option value="">Выберите...</option>' . "\r\n";

        if ($houseModel) {
            $flatsQuery = Flat::find()->where(['house_id' => $houseModel->id]);
            foreach ($flatsQuery->all() as $flat) {
                $flatsData .= '<option value="' . $flat->id . '">' . $flat->number . ', ' . $flat->getUnitTypeLabel($flat->unit_type) . '</option>' . "\r\n";
            }
        }

        return [
            'flats' => $flatsData,
        ];
    }

    /**
     * Finds the House model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return House the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = House::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
