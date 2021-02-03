<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;

/**
 * ClientSearch represents the model behind the search form of `common\models\Client`.
 */
class ClientSearch extends Client
{
    public $searchFullname;
    public $searchSellsTotal;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'agency_id', 'user_id'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'address', 'inn', 'passport_series', 'passport_number', 'passport_from', 'phone', 'email', 'description'], 'safe'],
            [['searchFullname', 'searchSellsTotal'], 'safe'],
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
        $query = Client::find()->joinWith(['agency', 'user', 'agreements'])
            ->select(['client.*', new \yii\db\Expression('COUNT(`agreement`.`id`) as `n_agreements`')])
            ->groupBy('client.id')
            ->distinct();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $dataProvider->sort->attributes['agency_id'] = [
            'asc' => ['agency.name' => SORT_ASC],
            'desc' => ['agency.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['user_id'] = [
            'asc' => ['user.lastname' => SORT_ASC, 'user.firstname' => SORT_ASC],
            'desc' => ['user.lastname' => SORT_DESC, 'user.firstname' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['searchFullname'] = [
            'asc' => ['client.lastname' => SORT_ASC, 'client.firstname' => SORT_ASC],
            'desc' => ['client.lastname' => SORT_DESC, 'client.firstname' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['searchSellsTotal'] = [
            'asc' => ['n_agreements' => SORT_ASC],
            'desc' => ['n_agreements' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['client.id' => $this->id]);
        $query->andFilterWhere(['like', 'client.phone', $this->phone]);
        $query->andFilterWhere(['like', 'client.email', $this->email]);
        $query->andFilterWhere(['client.agency_id' => $this->agency_id]);
        $query->andFilterWhere(['client.user_id' => $this->user_id]);
        $query->andFilterWhere(['or',
            ['like', 'client.firstname', $this->searchFullname],
            ['like', 'client.middlename', $this->searchFullname],
            ['like', 'client.lastname', $this->searchFullname],
        ]);

        return $dataProvider;
    }
}
