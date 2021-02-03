<?php
namespace backend\models;

use common\models\Flat;
use common\models\House;
use common\models\Client;
use common\models\Agency;
use common\models\User;
use common\models\Rieltor;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Agency import form
 */
class AgencyImportForm extends Model
{
    public $format;
    public $is_only_new;
    public $file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['format'], 'string', 'max' => 255],
            ['is_only_new', 'safe'],
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
            'is_only_new' => Yii::t('model', 'Только новые'),
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

                $createdCount = 0;
                $updatedCount = 0;
                foreach ($sheetData as $k => $row) {
                    if ($k == 1) {
                        // header
                    } else {
                        // row data
                        $columnsMap = $this->getColumnsMap($row);
                        
                        $agencyQuery = Agency::find();
                        $agencyQuery->where(['id' => $columnsMap['Agency']['id']])
                            ->orWhere(['name' => $columnsMap['Agency']['name']]);
                        $agencyModel = $agencyQuery->one();
                        
                        if ($agencyModel && $this->is_only_new) {
                            // if we only add
                            continue;
                        }
                        
                        if (!$agencyModel) {
                            if (!$columnsMap['Agency']['name']) {
                                // do not have new agency name
                                continue;
                            }
                            $agencyModel = new Agency();
                        }
                        $agencyModel->load($columnsMap);
                        
                        if ($agencyModel->isNewRecord) {
                            $createdCount++;
                        } else {
                            $updatedCount++;
                        }
                        
                        if ($agencyModel->save()) {
                            foreach ($columnsMap['Rieltors'] as $k => $rieltor) {
                                if (array_filter($rieltor)) {
                                    $rieltorQuery = Rieltor::find()->where(['agency_id' => $agencyModel->id]);
                                    $rieltorQuery->andFilterWhere(['firstname' => $rieltor['firstname']]);
                                    $rieltorQuery->andFilterWhere(['middlename' => $rieltor['middlename']]);
                                    $rieltorQuery->andFilterWhere(['lastname' => $rieltor['lastname']]);
                                    $rieltorModel = $rieltorQuery->one();
                                    if (!$rieltorModel) {
                                        $rieltorModel = new Rieltor();
                                        $rieltorModel->agency_id = $agencyModel->id;
                                        $rieltorModel->firstname = $rieltor['firstname'];
                                        $rieltorModel->middlename = $rieltor['middlename'];
                                        $rieltorModel->lastname = $rieltor['lastname'];
                                        $rieltorModel->is_director = ($k == 0 ? 1 : 0);
                                        $rieltorModel->save();
                                    }
                                }
                            }
                        }
                    }
                }
                
                $resultLabels = [];
                if ($createdCount) {
                    $resultLabels[] = "добавлено: {$createdCount}";
                }
                if ($updatedCount) {
                    $resultLabels[] = "обновлено: {$updatedCount}";
                }
                if ($resultLabels) {
                    $resultLabel = implode(', ', $resultLabels);
                    Yii::$app->session->addFlash('success', $resultLabel);
                }
                
                return true;
            }
        }
        
        return false;
    }
    
    private function getColumnsMap($row)
    {
        $rieltorsString = str_replace(['.', ';', '/'], ',', $rieltorsString);
        $rieltorsString = str_replace([' , ', ' ,', ', '], ',', $row['G']);
        $rieltorsString = trim(preg_replace('/\s+/', ' ', $rieltorsString));
        $rieltors = [];
        if ($rieltorsString) {
            // parse rieltors string
            $rieltorNames = explode(',', $rieltorsString);
            foreach ($rieltorNames as $name) {
                if ($name) {
                    $nameParts = explode(' ', $name);
                    $rieltors[] = [
                        'lastname' => $nameParts[0],
                        'firstname' => $nameParts[1],
                        'middlename' => $nameParts[2],
                    ];
                }
            }
        }
        return [
            'Agency' => [
                'id' => $row['A'],
                'name' => $row['B'],
                'phone' => $row['C'] . '',
                'email' => $row['D'],
                'status' => Agency::getStatusByLabel($row['E']),
                'description' => $row['F'] . '',
            ],
            'Rieltors' => $rieltors,
        ];
    }
}
