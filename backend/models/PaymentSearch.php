<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Payment;

/**
 * PaymentSearch represents the model behind the search form of `common\models\Payment`.
 */
class PaymentSearch extends Payment {
    
    public $house_id, $sections, $date_range;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'pay_number', 'created_at', 'updated_at', 'flat_id', 'house_id'], 'integer'],
            [['pay_date', 'price_plan', 'price_fact', 'price_saldo', 'sections', 'date_range'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {

        $query = Payment::find();

        //$query->andFilterWhere(['invoice.status' => 1]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pay_number' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['pay_number' => $this->pay_number]);
        $query->andWhere(['and', ['flat_id' => $this->flat_id], ['is not', 'flat_id', null]]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function informerSearch($params) {

        $query = Payment::find()->joinWith(['invoices', 'flat']);
        
        $query->andFilterWhere(['flat.status' => \common\models\Flat::STATUS_SOLD]);

        //$query->andFilterWhere(['invoice.status' => 1]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['pay_number' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (isset($params['FlatSearch']['date_range']) && !empty($params['FlatSearch']['date_range'])) {
            $arrRange = explode(' - ', $params['FlatSearch']['date_range']);
            $query->andFilterWhere(['between', 'pay_date', date('Y-m-d', strtotime($arrRange[0])), date('Y-m-d', strtotime($arrRange[1]))]);
        }

        if (isset($params['FlatSearch']['house_name']) && !isset($params['FlatSearch']['sections'])) {
            $arrHouses = \yii\helpers\ArrayHelper::getColumn(\common\models\House::find()->where(['name' => $params['FlatSearch']['house_name']])->asArray()->all(), 'id');
            $arrFlats =  \yii\helpers\ArrayHelper::getColumn(\common\models\Flat::find()->where(['in','house_id',$arrHouses])->asArray()->all(), 'id');
            $query->andFilterWhere(['in', 'payment.flat_id', $arrFlats]);
        } elseif (isset($params['FlatSearch']['house_name']) && isset($params['FlatSearch']['sections'])) {
            $arrHouses = \yii\helpers\ArrayHelper::getColumn(\common\models\House::find()->where(['name' => $params['FlatSearch']['house_name']])->andWhere(['in', 'section', $params['FlatSearch']['sections']])->asArray()->all(), 'id');
            $arrFlats =  \yii\helpers\ArrayHelper::getColumn(\common\models\Flat::find()->where(['in','house_id',$arrHouses])->asArray()->all(), 'id');
            $query->andFilterWhere(['in', 'payment.flat_id', $arrFlats]);
        }

        return $dataProvider;
    }

}
