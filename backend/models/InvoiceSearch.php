<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;

/**
 * InvoiceSearch represents the model behind the search form of `common\models\Invoice`.
 */
class InvoiceSearch extends Invoice
{
    public $searchHouse;
    public $searchUidDateRange;
    public $searchCounterparty;
    public $searchAgencyIdWithRieltors;
    public $searchFlat;
    public $searchUnitType;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'article_id', 'cashbox_id', 'flat_id', 'payment_id', 'client_id', 'agency_id', 'rieltor_id', 'user_id'], 'integer'],
            [['uid', 'uid_date', 'price', 'rate', 'description', 'company_name', 'type'], 'safe'],
            [['searchHouse', 'searchUidDateRange', 'searchCounterparty', 'searchAgencyIdWithRieltors', 'searchFlat', 'searchUnitType'], 'safe'],
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
        $query = Invoice::find()->joinWith(['flat.house', 'agency']);
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->andWhere(['or', ['in', 'flat.house_id', $houseIds], ['is', 'flat.house_id', null]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['searchUidDateRange' => SORT_DESC, 'id' => SORT_DESC]],
        ]);
        
        $dataProvider->sort->attributes['searchHouse'] = [
            'asc' => ['house.name' => SORT_ASC, 'house.section' => SORT_ASC],
            'desc' => ['house.name' => SORT_DESC, 'house.section' => SORT_DESC]
        ];
        
        $dataProvider->sort->attributes['searchUidDateRange'] = [
            'asc' => ['invoice.uid_date' => SORT_ASC],
            'desc' => ['invoice.uid_date' => SORT_DESC]
        ];
        
        $dataProvider->sort->attributes['searchCounterparty'] = [
            'asc' => ['agency.name' => SORT_ASC],
            'desc' => ['agency.name' => SORT_DESC]
        ];
        
        $dataProvider->sort->attributes['searchFlat'] = [
            'asc' => ['flat.number' => SORT_ASC, 'flat.number_index' => SORT_ASC],
            'desc' => ['flat.number' => SORT_DESC, 'flat.number_index' => SORT_DESC]
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['invoice.id' => $this->id]);
        $query->andFilterWhere(['flat.id' => $this->flat_id]);
        $query->andFilterWhere(['invoice.agency_id' => $this->agency_id]);
        $query->andFilterWhere(['rieltor_id' => $this->rieltor_id]);
        $query->andFilterWhere(['article_id' => $this->article_id]);
        $query->andFilterWhere(['cashbox_id' => $this->cashbox_id]);
        $query->andFilterWhere(['user_id' => $this->user_id]);
        $query->andFilterWhere(['type' => $this->type]);
        $query->andFilterWhere(['invoice.status' => $this->status]);
        
        if ($this->searchHouse) {
            $query->andFilterWhere(['house.id' => $this->searchHouse]);
        }
        
        if ($this->searchUidDateRange) {
            $dates = explode(' - ', $this->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $query->andFilterWhere(['>=', 'invoice.uid_date', date('Y-m-d', $tsFrom)]);
            $query->andFilterWhere(['<=', 'invoice.uid_date', date('Y-m-d', $tsTo)]);
        }
        
        if ($this->searchCounterparty) {
            $nameParts = explode(' ', $this->searchCounterparty);
            
            $query->joinWith(['client', 'rieltor', 'user']);
            $fullnameDb = new \yii\db\Expression("CONCAT(
                COALESCE(`client`.`firstname`, '') COLLATE utf8_general_ci, COALESCE(`client`.`middlename`, '') COLLATE utf8_general_ci, COALESCE(`client`.`lastname`, '') COLLATE utf8_general_ci,
                COALESCE(`rieltor`.`firstname`, '') COLLATE utf8_general_ci, COALESCE(`rieltor`.`middlename`, '') COLLATE utf8_general_ci, COALESCE(`rieltor`.`lastname`, '') COLLATE utf8_general_ci,
                COALESCE(`user`.`firstname`, '') COLLATE utf8_general_ci, COALESCE(`user`.`middlename`, '') COLLATE utf8_general_ci, COALESCE(`user`.`lastname`, '') COLLATE utf8_general_ci
            )");
            $query->andFilterWhere(['like', $fullnameDb, $nameParts[0]]);
            if (isset($nameParts[1])) {
                $query->andFilterWhere(['like', $fullnameDb, $nameParts[1]]);
            }
            if (isset($nameParts[2])) {
                $query->andFilterWhere(['like', $fullnameDb, $nameParts[2]]);
            }
            $query->orWhere(['and', 
                ['like', 'agency.name', $this->searchCounterparty],
                ['is', 'invoice.client_id', null]
            ]);
        }
        
        if ($this->searchAgencyIdWithRieltors) {
            $query->joinWith(['agency.rieltors']);
            $query->andFilterWhere(['or', 
                ['invoice.agency_id' => $this->searchAgencyIdWithRieltors],
                ['rieltor.agency_id' => $this->searchAgencyIdWithRieltors],
            ]);
        }
        
        if ($this->searchFlat) {
            $slashed = explode('/', $this->searchFlat);
            if (isset($slashed[1])) {
                $query->andFilterWhere(['and', 
                    ['flat.number' => $slashed[0]],
                    ['flat.number_index' => $slashed[1]],
                ]);
            } else {
                $query->andFilterWhere(['or', 
                    ['flat.number' => $this->searchFlat],
                    ['flat.number_index' => $this->searchFlat],
                ]);
            }
        }
        
        if ($this->searchUnitType) {
            $query->andFilterWhere(['flat.unit_type' => $this->searchUnitType]);
        }
        
        $query->groupBy('invoice.id');
        
        $dataProvider->pagination->pageSize = 100;

        return $dataProvider;
    }
}
