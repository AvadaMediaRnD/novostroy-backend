<?php
namespace backend\models;

use common\models\Agreement;
use common\models\House;
use common\models\Client;
use common\models\Agency;
use common\models\User;
use common\models\Flat;
use common\models\AgreementTemplate;
use Yii;
use yii\base\Model;
use common\models\AgreementClient;
use common\models\AgreementFlat;
use yii\web\UploadedFile;
use yii\helpers\Html;

/**
 * Agreement form
 */
class AgreementForm extends Model
{
    public $id;
    public $uid;
    public $uid_date;
    public $firstname;
    public $middlename;
    public $lastname;
    public $address;
    public $birthdate;
    public $inn;
    public $phone;
    public $email;
    public $passport_series;
    public $passport_number;
    public $passport_from;
    public $description;
    public $number;
    public $number_index;
    public $unit_type;
    public $square;
    public $floor;
    public $n_rooms;
    public $flat_address;
    public $price;
    public $status;
    public $flat_id;
    public $agency_id;
    public $client_id;
    public $agreement_template_id;
    
    public $tpl_house_address;
    public $tpl_client_fullname;
    public $tpl_client_fullname_short;
    public $tpl_client_birthdate_text;
    public $tpl_client_passport_from;
    
    public $is_refresh;
    
    public $scan_file;
    public $plan_image;
    public $agreement_file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'n_rooms', 'is_refresh'], 'integer'],
            [['status', 'flat_id', 'agency_id', 'client_id', 'agreement_template_id', 'number', 'floor'], 'integer'],
            [['uid_date'], 'safe'],
            [[
                'uid', 'scan_file', 'firstname', 'middlename', 'lastname', 'address', 'inn',
                'phone', 'email', 'passport_series', 'passport_number', 'passport_from',
                'flat_address', 'number_index', 'unit_type', 'birthdate',
            ], 'string', 'max' => 255],
            ['description', 'string'],
            [['square', 'price'], 'number'],
            [['uid', 'uid_date', 'status'], 'required'],
            ['uid', function ($attribute, $params) {
                $query = Agreement::find()->where(['uid' => $this->$attribute])->andFilterWhere(['!=', 'id', $this->id]);
                if ($query->exists()) {
                    $this->addError($attribute, Yii::t('app', 'Этот номер уже используется.'));
                }
            }],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['agreement_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => AgreementTemplate::className(), 'targetAttribute' => ['agreement_template_id' => 'id']],
            ['scan_file', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, jpeg, pdf'],
            ['plan_image', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            ['agreement_file', 'file', 'skipOnEmpty' => true, 'extensions' => 'docx'],
            [[
                'tpl_house_address', 'tpl_client_fullname', 'tpl_client_fullname_short', 
                'tpl_client_birthdate_text', 'tpl_client_passport_from',
            ], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'firstname' => Yii::t('model', 'Имя'),
            'middlename' => Yii::t('model', 'Отечество'),
            'lastname' => Yii::t('model', 'Фамилия'),
            'address' => Yii::t('model', 'Адрес'),
            'birthdate' => Yii::t('model', 'Дата роджения'),
            'inn' => Yii::t('model', 'ИНН'),
            'phone' => Yii::t('model', 'Номер телефона'),
            'email' => Yii::t('model', 'Email'),
            'passport_series' => Yii::t('model', 'Серия'),
            'passport_number' => Yii::t('model', 'Номер'),
            'passport_from' => Yii::t('model', 'Когда и кем выдан'),
            'status' => Yii::t('model', 'Статус'),
            'number' => Yii::t('model', 'Номер'),
            'number_index' => Yii::t('model', 'Индекс'),
            'unit_type' => Yii::t('app', 'Тип помещения'),
            'square' => Yii::t('model', 'Площадь (м2)'),
            'n_rooms' => Yii::t('model', 'Кол-во комнат'),
            'floor' => Yii::t('model', 'Этаж'),
            'flat_address' => Yii::t('model', 'Адрес ЖК'),
            'price' => Yii::t('model', 'Сумма договора (USD)'),
            'description' => Yii::t('model', 'Примечание'),
            'flat_id' => Yii::t('model', '№ квартиры'),
            'client_id' => Yii::t('model', 'Покупатель'),
            'agency_id' => Yii::t('model', 'Агентство'),
            'agreement_template_id' => Yii::t('model', 'Шаблон договора'),
            'scan_file' => Yii::t('model', 'Скан договора'),
            'plan_image' => Yii::t('model', 'План квартиры'),
            'agreement_file' => Yii::t('model', 'Файл договора'),
            'is_refresh' => Yii::t('model', 'Обновить текст договора'),
            
            'tpl_house_address' => Yii::t('model', 'Адрес дома'),
            'tpl_client_fullname' => Yii::t('model', 'ФИО клиента'),
            'tpl_client_fullname_short' => Yii::t('model', 'ФИО клиента с инициалами'),
            'tpl_client_birthdate_text' => Yii::t('model', 'Дата рождения клиента'),
            'tpl_client_passport_from' => Yii::t('model', 'Паспорт кем выдан клиента'),
        ];
    }

    /**
     * Save Agreement
     * @return Agreement|null
     */
    public function save()
    {
        if ($this->validate()) {
            $model = Agreement::findOne($this->id);
            if (!$model) {
                $model = new Agreement();
            }
            
            $oldTemplateId = $model->agreement_template_id;
            
            $model->setAttributes($this->attributes);

            if ($model->save()) {
                $modelAgreementClient = $model->agreementClient ?: new AgreementClient();
                $modelAgreementClient->setAttributes($this->attributes);
                $modelAgreementClient->description = $model->client->description;
                if ($modelAgreementClient->isNewRecord) {
                    $modelAgreementClient->agreement_id = $model->id;
                }
                $modelAgreementClient->save();
                
                $modelAgreementFlat = $model->agreementFlat ?: new AgreementFlat();
                $modelAgreementFlat->setAttributes($this->attributes);
                $modelAgreementFlat->address = $this->flat_address;
                if ($modelAgreementFlat->isNewRecord) {
                    $modelAgreementFlat->agreement_id = $model->id;
                }
                $modelAgreementFlat->save();
                
                $file = UploadedFile::getInstance($this, 'scan_file');
                if ($file) {
                    $path = '/upload/Agreement/' . $model->id . '/scan_file'.$model->uid.'.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $model->scan_file = $path;
                        $model->save(false);
                    }
                }
                
                $planImage = UploadedFile::getInstance($this, 'plan_image');
                if ($planImage) {
                    $path = '/upload/Agreement/' . $model->id . '/plan_image'.$model->uid.'.' . 'jpg'; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($planImage->saveAs($pathFull)) {
                        $model->plan_image = $path;
                        $model->save(false);
                    }
                }
                
                $agreementFile = UploadedFile::getInstance($this, 'agreement_file');
                if ($agreementFile) {
                    $path = '/upload/Agreement/' . $model->id . '/' . 'agreement-' . $model->uid . '.docx';
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($agreementFile->saveAs($pathFull)) {
                        $model->file = $path;
                        $model->save(false);
                    }
                }
                
                // update file content if new record or changed template
                if ($model->isNewRecord || $oldTemplateId != $this->agreement_template_id || $this->is_refresh) {
                    $model->generateFile();
                }
                
                $this->id = $model->id;
                
                return $model;
            }
        }
        
        return null;
    }

}
