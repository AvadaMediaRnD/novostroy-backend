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
use yii\web\UploadedFile;
use yii\helpers\Html;

/**
 * AgreementTemplate form
 */
class AgreementTemplateForm extends Model
{
    public $id;
    public $name;
    public $is_default;
    public $content;
    
    public $file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'is_default'], 'integer'],
            ['name', 'string', 'max' => 255],
            ['content', 'string'],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => ['docx', 'ods', 'doc'], 'maxSize' => 10*1024*1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'name' => Yii::t('model', 'Название'),
            'is_default' => Yii::t('model', 'По-умолчанию'),
            'content' => Yii::t('model', 'Содержимое'),
            'file' => Yii::t('model', 'Файл'),
        ];
    }

    /**
     * Save AgreementTemplate
     * @return AgreementTemplate|null
     */
    public function save()
    {
        if ($this->validate()) {
            $model = AgreementTemplate::findOne($this->id);
            if (!$model) {
                $model = new AgreementTemplate();
            }
            
            $model->name = $this->name;
            $model->is_default = $this->is_default;

            if ($model->save()) {
                $file = UploadedFile::getInstance($this, 'file');
                if ($file) {
                    // uploaded file
                    $path = '/upload/AgreementTemplate/tpl-' . $model->id . '.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $model->file = $path;
                        $model->save(false);
                    }
                } else {
                    // save from content
                    $path = '/upload/AgreementTemplate/tpl-' . $model->id . '.docx'; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $model->file = $path;
                    $model->writeFileContent(Html::decode($this->content));
                    $model->save(false);
                }
                
                return $model;
            }
        }
        
        return null;
    }
}
