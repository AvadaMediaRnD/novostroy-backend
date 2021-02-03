<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "system_config".
 *
 * @property int $id
 * @property string $type
 * @property string $key
 * @property string $value_raw
 * @property string $description
 */
class SystemConfig extends \common\models\ZModel
{
    const TYPE_PARAM = 'param';
    const TYPE_VARIABLE = 'variable';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'value_raw'], 'string'],
            [['key', 'description'], 'string', 'max' => 255],
            ['type', 'default', 'value' => static::TYPE_VARIABLE],
            ['key', 'unique'],
            ['key', 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Тип'),
            'key' => Yii::t('app', 'Переменная'),
            'value_raw' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        if($this->type == static::TYPE_VARIABLE) {
            $this->fixVariableKey();
        }
        return parent::beforeSave($insert);
    }
    
    /**
     * Get available variables options for select list
     * @param string $type
     * @return array
     */
    public static function getOptions($type = null)
    {
        $query = static::find();
        if ($type) {
            $query->where(['type' => $type]);
        }
        $query->orderBy(['id' => SORT_ASC]);
        return ArrayHelper::map($query->all(), 'key', 'value_raw');
    }
    
    /**
     * @return array
     */
    public static function getTypeOptions()
    {
        return [
            static::TYPE_VARIABLE => Yii::t('model', 'Переменная'),
            static::TYPE_PARAM => Yii::t('model', 'Параметр'),
        ];
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabel($type = null)
    {
        $type = $type == null ? $this->type : $type;
        $options = static::getTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }

    /**
     * @param string $key
     * @param boolean $createIfNull
     * @return static
     */
    public static function getConfigModelByKey($key, $createIfNull){
        $model = self::find()->where(['key' => $key])->one();
        if ($createIfNull && $model == null) {
            $model = new SystemConfig();
            $model->type = 'param';
            $model->key = $key;
            $model->value_raw = '';
            $model->description = '';
            $model->save();
        }

        return $model;
    }
    
    /**
     * Fix format for key
     */
    private function fixVariableKey()
    {
        $this->key = trim(str_replace('-', ' ', $this->key));
        $this->key = preg_replace('/\s+/', ' ', $this->key);
        $this->key = mb_strtoupper(str_replace(' ', '_', $this->key));
        if (mb_substr($this->key, 0, 2) != '{{' || mb_substr($this->key, -2, 2) != '}}') {
            $key = preg_replace('/[^A-Za-z0-9\_\.]/', '', $this->key);
            $this->key = '{{' . $key . '}}';
        }
    }
}
