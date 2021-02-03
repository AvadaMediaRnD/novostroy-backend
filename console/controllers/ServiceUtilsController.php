<?php


namespace console\controllers;


use common\models\House;
use Yii;
use yii\console\Controller;

class ServiceUtilsController extends Controller
{
    public function actionGenerateSyncToken(array $houseIds)
    {
        if (!empty($houseIds)){
            foreach ($houseIds as $houseId){
                $house = House::findOne(['id' => $houseId]);
                if ($house){
                    do {
                        $syncToken = Yii::$app->security->generateRandomString(16);
                        $syncIdExists = House::find()->where(['sync_id' => $syncToken])->count();
                    } while ($syncIdExists);
                    $house->sync_id = $syncToken;
                    $house->save();
                    $house = null;
                }
            }
        }
    }
}