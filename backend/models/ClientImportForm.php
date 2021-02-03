<?php
namespace backend\models;

use common\models\Flat;
use common\models\House;
use common\models\Client;
use common\models\Agency;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Client import form
 */
class ClientImportForm extends Model
{
    public $format;
    public $file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['format'], 'string', 'max' => 255],
            ['file', 'file', 'extensions' => 'xls, xlsx, ods, csv', 'wrongExtension' => 'Некорректный формат файла.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'format' => Yii::t('model', 'Формат'),
            'file' => Yii::t('model', 'Файл'),
        ];
    }

    /**
     * @return boolean
     */
    public function import()
    {
        if ($this->validate()) {
            $file = UploadedFile::getInstance($this, 'file');
            if ($file) {
                $objPHPExcel = \PHPExcel_IOFactory::load($file->tempName);
                $col = $objPHPExcel->getActiveSheet()->getHighestColumn(1);
                $row = $objPHPExcel->getActiveSheet()->getHighestRow();
                $sheetData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:'.$col.$row, null, true, true, true);

                foreach ($sheetData as $k => $row) {
                    if ($k == 1) {
                        // header
                    } else {
                        // row data
                        $columnsMap = $this->getColumnsMap($row);
                        
                        $clientQuery = Client::find();
                        $clientQuery->where(['id' => $columnsMap['Client']['id']]);
                        $clientModel = $clientQuery->one();
                        if (!$clientModel) {
                            $clientModel = new Client();
                        }
                        $clientModel->load($columnsMap);
                        $clientModel->save();
                    }
                }
                
                return true;
            }
        }
        
        return false;
    }
    
    private function getColumnsMap($row)
    {
        return [
            'Client' => [
                'id' => $row['A'],
                'lastname' => $row['B'],
                'firstname' => $row['C'],
                'middlename' => $row['D'],
                'address' => $row['E'],
                'inn' => $row['F'],
                'birthdate' => date('Y-m-d', strtotime($row['G'])),
                'passport_series' => $row['H'],
                'passport_number' => $row['I'],
                'passport_from' => $row['J'],
                'phone' => $row['K'] . '',
                'email' => $row['L'],
                'description' => $row['M'] . '',
            ], 
        ];
    }
}
