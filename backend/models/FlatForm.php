<?php

namespace backend\models;

use Yii,
    yii\base\Model,
    yii\validators\UniqueValidator;
use common\models\Flat,
    common\models\House,
    common\models\Client,
    common\models\Agency,
    common\models\User;

/**
 * Flat form
 */
class FlatForm extends Model {

    public $id;
    public $unit_type;
    public $number;
    public $number_index;
    public $n_rooms;
    public $floor;
    public $square;
    public $price_m;
    public $price_sell_m;
    public $price_discount_m;
    public $price_paid_init;
    public $price_paid_out;
    public $commission_agency;
    public $commission_manager;
    public $commission_agency_type;
    public $commission_manager_type;
    public $description;
    public $status;
    public $house_id;
    public $client_id;
    public $agency_id;
    public $user_id;
    public $price_total;
    public $price_sell_total;
    public $price_discount_total;
    public $tab_id;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'number', 'n_rooms', 'floor', 'status', 'house_id', 'commission_agency_type', 'commission_manager_type'], 'integer'],
            [['description'], 'string'],
            [['tab_id'], 'string', 'max' => 6],
            [['unit_type', 'number_index'], 'string', 'max' => 255],
            [['square', 'price_m', 'price_sell_m', 'price_discount_m', 'price_paid_init', 'commission_agency', 'commission_manager', 'price_paid_out'], 'number'],
            [['house_id'], 'exist', 'skipOnError' => true, 'targetClass' => House::className(), 'targetAttribute' => ['house_id' => 'id']],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['client_id', 'safe'],
            [['house_id', 'number', 'status'], 'required'],
            [['price_total', 'price_sell_total', 'price_discount_total'], 'safe'],
            ['number', 'validateUnique', 'on' => 'create'],
            ['number', 'validateUniqueUpdate', 'on' => 'update'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'unit_type' => Yii::t('model', 'Тип помещения'),
            'number' => Yii::t('model', '№'),
            'number_index' => Yii::t('model', 'Индекс'),
            'n_rooms' => Yii::t('model', 'Кол-во комнат'),
            'floor' => Yii::t('model', '№ этажа'),
            'square' => Yii::t('model', 'Площадь (м2)'),
            'price_m' => Yii::t('model', 'Цена за м2 (USD)'),
            'price_sell_m' => Yii::t('model', 'Цена продажи за м2 (USD)'),
            'price_discount_m' => Yii::t('model', 'Скидка за м2 (USD)'),
            'price_paid_init' => Yii::t('model', 'Изначально уплаченная сумма (USD)'),
            'price_paid_out' => Yii::t('model', 'Остаток суммы (USD)'),
            'commission_agency' => Yii::t('app', 'Комиссия агентства'),
            'commission_manager' => Yii::t('app', 'Комиссия менеджера'),
            'commission_agency_type' => Yii::t('app', 'Тип комиссии агентства'),
            'commission_manager_type' => Yii::t('app', 'Тип комиссии менеджера'),
            'description' => Yii::t('model', 'Описание'),
            'status' => Yii::t('model', 'Статус'),
            'house_id' => Yii::t('model', 'Дом (секция)'),
            'client_id' => Yii::t('model', 'Покупатель'),
            'agency_id' => Yii::t('model', 'Агентство'),
            'user_id' => Yii::t('model', 'Менеджер'),
            'price_total' => Yii::t('model', 'Цена (USD)'),
            'price_sell_total' => Yii::t('model', 'Цена продажи (USD)'),
            'price_discount_total' => Yii::t('model', 'Полная скидка (USD)'),
            'tab_id' => Yii::t('model', 'Tab'),
        ];
    }

    public function validateUnique($attribute) {
        $number = $this->$attribute;
        if (Flat::findOne(['number' => $number, 'number_index' => $this->number_index, 'house_id' => $this->house_id, 'unit_type' => $this->unit_type]) !== NULL) {
            $this->addError($attribute, 'Помещение этого типа с данным номером в этом доме уже существует');
        }
    }

    public function validateUniqueUpdate($attribute) {
        $number = $this->$attribute;
        if (($model = Flat::findOne(['number' => $number, 'number_index' => $this->number_index, 'house_id' => $this->house_id, 'unit_type' => $this->unit_type])) !== NULL) {
            if ($model->id !== $this->id) {
                $this->addError($attribute, 'Помещение этого типа с данным номером в этом доме уже существует');
            }
        }
    }

    /**
     * Save Flat
     * @return Flat|null
     */
    public function save() {
        if ($this->validate()) {

            if ($this->unit_type == Flat::TYPE_CAR_PLACE || $this->unit_type == Flat::TYPE_PARKING) {
                $this->n_rooms = 0;
                $this->floor = 0;
                $this->square = 0;
                $this->price_m = $this->price_total;
                $this->price_sell_m = $this->price_sell_total;
                $this->price_discount_m = $this->price_discount_total;
            }

            $model = Flat::findOne($this->id);
            if (!$model) {
                $model = new Flat();
                $model->scenario = 'create';
            }

            if ($this->client_id && (int) $this->client_id == 0) {
                $names = explode(' ', $this->client_id);
                if (count($names) && mb_strlen($names[0])) {
                    $client = new Client();
                    $client->lastname = isset($names[0]) ? $names[0] : null;
                    $client->firstname = isset($names[1]) ? $names[1] : null;
                    $client->middlename = isset($names[2]) ? $names[2] : null;
                    $client->agency_id = $this->agency_id;
                    $client->user_id = $this->user_id;
                    $client->save();

                    $this->client_id = $client->id;
                }
            }

            $model->setAttributes($this->attributes);

            if ($model->save()) {
                if ($client = $model->client) {
                    if ($this->user_id) {
                        $client->user_id = $this->user_id;
                    }
                    $client->save();
                }

                $this->id = $model->id;

                Yii::$app->session->set('tab_id', $this->tab_id);

                return $model;
            }
        }

        return null;
    }

}
