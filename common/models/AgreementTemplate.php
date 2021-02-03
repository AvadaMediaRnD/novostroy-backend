<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "agreement_template".
 *
 * @property int $id
 * @property string $name
 * @property string $file
 * @property int $is_default
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Agreement[] $agreements
 */
class AgreementTemplate extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement_template';
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_default', 'created_at', 'updated_at'], 'integer'],
            ['is_default', 'default', 'value' => 0],
            [['name', 'file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'file' => Yii::t('app', 'File'),
            'is_default' => Yii::t('app', 'Is Default'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    public function afterSave($insert, $changedAttributes) {
        if ($this->is_default) {
            static::updateAll(['is_default' => 0], ['!=', 'id', $this->id]);
        }
        if (!static::find()->where(['is_default' => 1])->exists()) {
            Yii::$app->db->createCommand('UPDATE `agreement_template` SET `is_default` = 1 ORDER BY `id` DESC LIMIT 1')->execute();
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function delete()
    {
        if ($this->file) {
            try {
                $file = Yii::getAlias('@frontend/web' . $this->file);
                $fileHtml = Yii::getAlias('@frontend/web' . $this->file . '.html');
                if (file_exists($file)) {
                    unlink($file);
                }
                if (file_exists($fileHtml)) {
                    unlink($fileHtml);
                }
            } catch (\yii\base\ErrorException $e) {
                Yii::error("Cannot delete file $file or $fileHtml", 'AgreementTemplate');
            }
        }
        return parent::delete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreements()
    {
        return $this->hasMany(Agreement::className(), ['agreement_template_id' => 'id']);
    }
    
    /**
     * Get content of tpl file
     * @return string
     */
    public function readFileContent()
    {
        if (!$this->file) {
            return null;
        }
        $pathFull = Yii::getAlias('@frontend/web' . $this->file);
        if (!file_exists($pathFull) || !is_readable($pathFull)) {
            return null;
        }
        
        $pathFullHtml = "$pathFull.html";
        if (file_exists($pathFullHtml)) {
            return file_get_contents($pathFullHtml);
        }
        
        $content = $this->readZippedXML($pathFull);
        return $content;
    }
    
    /**
     * Write content to docx file
     * @param string $content
     */
    public function writeFileContent($content)
    {
        $pathFull = Yii::getAlias('@frontend/web' . $this->file);
        libxml_use_internal_errors(true);
        if ($this->file) {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            // $section->addText($content);
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $content);
            
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($pathFull);
            
            // Saving the document as HTML file...
//            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
//            $objWriter->save($pathFull . '.html');
            
            // plain html save for reading in form
            file_put_contents("$pathFull.html", $content);
        }
    }
    
    private function readZippedXML($archiveFile) {
        // Create new ZIP archive
        $zip = new \ZipArchive;
        $dataFile = 'word/document.xml';
        // Open received archive file
        if (true === $zip->open($archiveFile)) {
            // If done, search for the data file in the archive
            if (($index = $zip->locateName($dataFile)) !== false) {
                // If found, read it to the string
                $data = $zip->getFromIndex($index);
                // Close archive file
                $zip->close();
                // Load XML from a string
                // Skip errors and warnings
                $xml = \DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                // Return data without XML formatting tags

                $contents = explode('\n',strip_tags($xml->saveXML()));
                $text = '';
                foreach($contents as $i => $content) {
                    $text .= $contents[$i];
                }
                return $text;
            }
            $zip->close();
        }
        // In case of failure return empty string
        return "";
    }
}
