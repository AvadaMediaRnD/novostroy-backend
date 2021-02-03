<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Agency;

/**
 * AgencySearch represents the model behind the search form of `common\models\Agency`.
 */
class AgencySearch extends Agency
{
    public $searchFullname;
    public $searchSellsTotal;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'phone', 'email', 'description'], 'safe'],
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
        $query = Agency::find()->joinWith(['agreements', 'rieltorDirector'])
            ->select(['agency.*', new \yii\db\Expression('COUNT(`agreement`.`id`) as `n_agreements`')])
            ->groupBy('agency.id')
            ->distinct();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
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
        $query->andFilterWhere(['agency.id' => $this->id]);
        $query->andFilterWhere(['like', 'agency.phone', $this->phone]);
        $query->andFilterWhere(['like', 'agency.email', $this->email]);
        $query->andFilterWhere(['like', 'agency.name', $this->name]);
        $query->andFilterWhere(['agency.status' => $this->status]);
        $query->andFilterWhere(['or',
            ['like', 'rieltor.firstname', $this->searchFullname],
            ['like', 'rieltor.middlename', $this->searchFullname],
            ['like', 'rieltor.lastname', $this->searchFullname],
        ]);

        return $dataProvider;
    }
}
