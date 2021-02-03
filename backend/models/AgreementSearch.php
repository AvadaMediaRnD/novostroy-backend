<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Agreement;

/**
 * AgreementSearch represents the model behind the search form of `common\models\Agreement`.
 */
class AgreementSearch extends Agreement
{
    public $searchNumber;
    public $searchHouse;
    public $searchSquare;
    public $searchPrice;
    public $searchUidDateRange;
    public $searchClient;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'flat_id', 'agency_id', 'client_id', 'user_id', 'agreement_template_id'], 'integer'],
            [['uid', 'uid_date', 'description'], 'safe'],
            [['searchNumber', 'searchHouse', 'searchSquare', 'searchUidDateRange', 'searchClient'], 'safe'],
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
        $query = Agreement::find()
            ->joinWith(['flat.house', 'client']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $dataProvider->sort->attributes['searchNumber'] = [
            'asc' => ['flat.number' => SORT_ASC],
            'desc' => ['flat.number' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['searchHouse'] = [
            'asc' => ['house.name' => SORT_ASC],
            'desc' => ['house.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['searchClient'] = [
            'asc' => ['client.lastname' => SORT_ASC, 'client.firstname' => SORT_ASC],
            'desc' => ['client.lastname' => SORT_DESC, 'client.firstname' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere(['agreement.client_id' => $this->client_id]);
        $query->andFilterWhere(['agreement.agency_id' => $this->agency_id]);
        $query->andFilterWhere(['agreement.status' => $this->status]);
        $query->andFilterWhere(['like', 'agreement.uid', $this->uid]);
        
        if ($this->searchUidDateRange) {
            $dates = explode(' - ', $this->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $query->andFilterWhere(['>=', 'agreement.uid_date', date('Y-m-d', $tsFrom)]);
            $query->andFilterWhere(['<=', 'agreement.uid_date', date('Y-m-d', $tsTo)]);
        }
        
        $query->andFilterWhere(['flat.number' => $this->searchNumber]);
        $query->andFilterWhere(['like', 'house.name', $this->searchHouse]);
        $query->andFilterWhere(['or',
            ['like', 'client.firstname', $this->searchClient],
            ['like', 'client.middlename', $this->searchClient],
            ['like', 'client.lastname', $this->searchClient],
        ]);
        $query->andFilterWhere(['like', 'agreement.description', $this->description]);
        
        return $dataProvider;
    }
}
