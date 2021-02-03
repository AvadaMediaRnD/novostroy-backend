<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    public $searchFullname;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'role'], 'integer'],
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
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        
        $dataProvider->sort->attributes['searchFullname'] = [
            'asc' => ['user.lastname' => SORT_ASC, 'user.firstname' => SORT_ASC],
            'desc' => ['user.lastname' => SORT_DESC, 'user.firstname' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['role' => $this->role]);
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'phone', $this->phone]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['or', 
            ['like', 'firstname', $this->searchFullname],
            ['like', 'lastname', $this->searchFullname],
            ['like', 'middlename', $this->searchFullname],
        ]);

        return $dataProvider;
    }
}
