<?php

namespace backend\models;

use Yii,
    yii\base\Model,
    yii\data\ActiveDataProvider;
use common\models\House;

/**
 * HouseSearch represents the model behind the search form of `common\models\House`.
 */
class HouseSearch extends House {

    public $sectionsCount;
    public $flatsCount;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'n_floors', 'status'], 'integer'],
            [['commission_agency', 'commission_manager'], 'number'],
            [['name', 'section', 'address'], 'safe'],
            [['sectionsCount', 'flatsCount'], 'safe'],
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
        $query = static::find();
        $query->select(['*', 'CONVERT(section, UNSIGNED INTEGER) AS section_num']);

        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->andFilterWhere(['in', 'id', $houseIds]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['attributes' => ['section_num', 'name'], 'defaultOrder' => ['name' => SORT_ASC, 'section_num' => SORT_ASC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['commission_agency' => $this->commission_agency]);
        $query->andFilterWhere(['commission_manager' => $this->commission_manager]);
        $query->andFilterWhere(['name' => $this->name]);
        $query->andFilterWhere(['like', 'section', $this->section]);
        $query->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }

    public function searchComplex($params) {
        $query = static::find()->select(['house.*', 'COUNT(house.id) as `sectionsCount`']);

        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->andFilterWhere(['in', 'house.id', $houseIds]);

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

        $query->groupBy(['house.name']);

        // grid filtering conditions
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['commission_agency' => $this->commission_agency]);
        $query->andFilterWhere(['commission_manager' => $this->commission_manager]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'section', $this->section]);
        $query->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }

}
