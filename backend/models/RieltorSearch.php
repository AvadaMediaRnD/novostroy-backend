<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Rieltor;

/**
 * RieltorSearch represents the model behind the search form of `common\models\Rieltor`.
 */
class RieltorSearch extends Rieltor
{
    public $searchFullname;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_director', 'agency_id'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'phone', 'email'], 'safe'],
            [['searchFullname'], 'safe'],
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
        $query = Rieltor::find();

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

        // grid filtering conditions
        $query->andFilterWhere(['agency_id' => $this->agency_id]);
        
        return $dataProvider;
    }
}
