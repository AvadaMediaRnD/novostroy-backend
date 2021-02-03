<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Flat;
use common\models\User;

/**
 * FlatSearch represents the model behind the search form of `common\models\Flat`.
 */
class FlatSearch extends Flat {

    public $searchPriceSell;
    public $searchPaymentBefore;
    public $searchPaymentPaid;
    public $searchPaymentLeft;
    public $searchSquareFrom;
    public $searchSquareTo;
    public $searchPriceSellFrom;
    public $searchPriceSellTo;
    public $searchNumberWithIndex;
    public $searchAgency;
    public $searchClient;
    public $searchClientPhone;
    public $searchStatus;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'number', 'n_rooms', 'floor', 'status', 'created_at', 'updated_at', 'house_id', 'client_id', 'agency_id'], 'integer'],
            [['square', 'price_m', 'price_sell_m', 'price_discount_m', 'commission_agency', 'commission_manager'], 'number'],
            [['description', 'unit_type', 'number_index'], 'safe'],
            [['searchPriceSell', 'searchPaymentBefore', 'searchPaymentPaid', 'searchPaymentLeft',
            'searchSquareFrom', 'searchSquareTo', 'searchPriceSellFrom', 'searchPriceSellTo',
            'searchNumberWithIndex', 'searchAgency', 'searchClient', 'searchClientPhone', 'searchStatus'], 'safe'],
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
        $query = Flat::find()->joinWith(['house', 'agency', 'client'])
                ->select([
            'flat.*',
            new \yii\db\Expression('(`price_sell_m` * `square`) as `price_sell`')
        ]);

        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->andWhere(['or', ['in', 'house_id', $houseIds], ['is', 'house_id', null]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['house_id'] = [
            'asc' => ['house.name' => SORT_ASC],
            'desc' => ['house.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['searchPriceSell'] = [
            'asc' => ['price_sell' => SORT_ASC],
            'desc' => ['price_sell' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['searchNumberWithIndex'] = [
            'asc' => ['number' => SORT_ASC, 'number_index' => SORT_ASC],
            'desc' => ['number' => SORT_DESC, 'number_index' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->client_id) {
            $query->andFilterWhere(['client_id' => $this->client_id]);
        }
        if ($this->agency_id) {
            $query->andFilterWhere(['flat.agency_id' => $this->agency_id]);
        }
        if ($this->searchStatus) {
            $query->andFilterWhere(['in', 'flat.status', $this->searchStatus]);
        }

        if ($this->searchSquareFrom) {
            $this->searchSquareFrom = str_replace('.', ',', $this->searchSquareFrom);
        }
        if ($this->searchSquareTo) {
            $this->searchSquareTo = str_replace('.', ',', $this->searchSquareTo);
        }
        if ($this->searchPriceSellFrom) {
            $this->searchPriceSellFrom = str_replace('.', ',', $this->searchPriceSellFrom);
        }
        if ($this->searchPriceSellTo) {
            $this->searchPriceSellTo = str_replace('.', ',', $this->searchPriceSellTo);
        }

        if (isset($params['FlatSearch']['house_name'])) {
            $query->andFilterWhere(['house.name' => $params['FlatSearch']['house_name']]);
        }

        if (isset($params['FlatSearch']['sections'])) {
            $query->andFilterWhere(['in', 'house.section', $params['FlatSearch']['sections']]);
        }

        // grid filtering conditions
        $query->andFilterWhere(['number' => $this->number]);
        $query->andFilterWhere(['number_index' => $this->number_index]);
        $query->andFilterWhere(['unit_type' => $this->unit_type]);
        $query->andFilterWhere(['house_id' => $this->house_id]);
        $query->andFilterWhere(['flat.status' => $this->status]);
        $query->andFilterWhere(['square' => $this->square]);
        $query->andFilterWhere(['>=', 'square', $this->searchSquareFrom]);
        $query->andFilterWhere(['<=', 'square', $this->searchSquareTo]);
        $query->andFilterWhere(['>=', new \yii\db\Expression('`price_sell_m` * `square`'), $this->searchPriceSellFrom]);
        $query->andFilterWhere(['<=', new \yii\db\Expression('`price_sell_m` * `square`'), $this->searchPriceSellTo]);

        if ($this->searchNumberWithIndex) {
            $this->searchNumberWithIndex = str_replace(["\\", '.', ',', '|', '-'], '/', $this->searchNumberWithIndex);
            $numberParts = explode('/', $this->searchNumberWithIndex);
            $query->andFilterWhere(['number' => $numberParts[0]]);
            if (isset($numberParts[1])) {
                $query->andFilterWhere(['number_index' => $numberParts[1]]);
            }
        }

        if ($this->searchAgency) {
            $query->andFilterWhere(['or',
                ['like', 'agency.name', $this->searchAgency],
                ['like', 'client.agency.name', $this->searchAgency]
            ]);
        }

        if ($this->searchClient) {
            $query->andFilterWhere(['or',
                ['like', 'client.firstname', $this->searchClient],
                ['like', 'client.middlename', $this->searchClient],
                ['like', 'client.lastname', $this->searchClient],
            ]);
        }

        if ($this->searchClientPhone) {
            $query->andFilterWhere(['like', 'client.phone', $this->searchClientPhone]);
        }


        $dataProvider->pagination->pageSize = 100;

        return $dataProvider;
    }

}
