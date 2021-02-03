<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ViewTotalFlat;
use yii\db\Expression;

/**
 * ViewTotalFlatSearch represents the model behind the search form of `common\models\ViewTotalFlat`.
 */
class ViewTotalFlatSearch extends ViewTotalFlat
{
    public $searchClientFullname;
    public $searchUserFullname;
    public $searchSquareFrom;
    public $searchSquareTo;
    public $searchPriceFrom;
    public $searchPriceTo;
    public $searchPriceMFrom;
    public $searchPriceMTo;
    public $searchPricePlanFrom;
    public $searchPricePlanTo;
    public $searchPriceSellMFrom;
    public $searchPriceSellMTo;
    public $searchPriceFactFrom;
    public $searchPriceFactTo;
    public $searchPriceLeftFrom;
    public $searchPriceLeftTo;
    public $searchPriceDebtFrom;
    public $searchPriceDebtTo;
    public $searchPriceDiscountFrom;
    public $searchPriceDiscountTo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'n_rooms', 'floor', 'status', 'sell_status', 'created_at', 'updated_at', 'house_id', 'client_id', 'agency_id'], 'integer'],
            [[
                'square', 'price_m', 'price_sell_m', 'price_discount_m', 
                'commission_agency', 'commission_manager', 'price', 'price_sell', 'price_discount', 'price_plan', 
                'price_fact', 'price_left', 'price_debt', 'description', 
                'name', 'section', 'client_firstname', 
                'client_middlename', 'client_lastname', 'phone', 'email', 
                'user_firstname', 'user_middlename', 'user_lastname', 'agency_name'
            ], 'safe'],
            [[
                'searchClientFullname', 'searchUserFullname', 'searchPriceFrom', 'searchPriceTo',
                'searchPriceMFrom', 'searchPriceMTo', 'searchPricePlanFrom', 'searchPricePlanTo',
                'searchPriceSellMFrom', 'searchPriceSellMTo', 'searchPriceFactFrom', 'searchPriceFactTo',
                'searchPriceLeftFrom', 'searchPriceLeftTo', 'searchPriceDebtFrom', 'searchPriceDebtTo',
                'searchPriceDiscountFrom', 'searchPriceDiscountTo', 'searchSquareFrom', 'searchSquareTo',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = ViewTotalFlat::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
        
        $dataProvider->sort->attributes['searchClientFullname'] = [
            'asc' => ['client_lastname' => SORT_ASC],
            'desc' => ['client_lastname' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['searchUserFullname'] = [
            'asc' => ['user_lastname' => SORT_ASC],
            'desc' => ['user_lastname' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['number' => $this->number]);
        $query->andFilterWhere(['house_id' => $this->house_id]);
        $query->andFilterWhere(['sell_status' => $this->sell_status]);
        $query->andFilterWhere(['agency_id' => $this->agency_id]);
        $query->andFilterWhere([
            'or', 
            ['like', 'client_firstname', $this->searchClientFullname],
            ['like', 'client_middlename', $this->searchClientFullname],
            ['like', 'client_lastname', $this->searchClientFullname],
        ]);
        $query->andFilterWhere([
            'or', 
            ['like', 'user_firstname', $this->searchUserFullname],
            ['like', 'user_middlename', $this->searchUserFullname],
            ['like', 'user_lastname', $this->searchUserFullname],
        ]);
        $query->andFilterWhere(['like', 'phone', $this->phone]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['>=', 'square', $this->searchSquareFrom]);
        $query->andFilterWhere(['<=', 'square', $this->searchSquareTo]);
        $query->andFilterWhere(['>=', 'price', $this->searchPriceFrom]);
        $query->andFilterWhere(['<=', 'price', $this->searchPriceTo]);
        $query->andFilterWhere(['>=', 'price_m', $this->searchPriceMFrom]);
        $query->andFilterWhere(['<=', 'price_m', $this->searchPriceMTo]);
        $query->andFilterWhere(['>=', 'price_plan', $this->searchPricePlanFrom]);
        $query->andFilterWhere(['<=', 'price_plan', $this->searchPricePlanTo]);
        $query->andFilterWhere(['>=', 'price_sell_m', $this->searchPriceSellMFrom]);
        $query->andFilterWhere(['<=', 'price_sell_m', $this->searchPriceSellMTo]);
        $query->andFilterWhere(['>=', 'price_fact', $this->searchPriceFactFrom]);
        $query->andFilterWhere(['<=', 'price_fact', $this->searchPriceFactTo]);
        $query->andFilterWhere(['>=', 'price_left', $this->searchPriceLeftFrom]);
        $query->andFilterWhere(['<=', 'price_left', $this->searchPriceLeftTo]);
        $query->andFilterWhere(['>=', 'price_debt', $this->searchPriceDebtFrom]);
        $query->andFilterWhere(['<=', 'price_debt', $this->searchPriceDebtTo]);
        $query->andFilterWhere(['>=', 'price_discount', $this->searchPriceDiscountFrom]);
        $query->andFilterWhere(['<=', 'price_discount', $this->searchPriceDiscountTo]);
        
        // $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
