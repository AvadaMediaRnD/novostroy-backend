<?php

namespace backend\models;

use Yii,
    yii\base\Model,
    yii\web\UploadedFile;
use common\models\Agreement,
    common\models\Flat,
    common\models\House,
    common\models\Client,
    common\models\Agency,
    common\models\User,
    common\models\Payment;

/**
 * Flat import form
 */
class FlatImportForm extends Model {

    public $format;
    public $is_only_new;
    public $file;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['format'], 'string', 'max' => 255],
            ['is_only_new', 'safe'],
            ['file', 'file', 'extensions' => 'xls, xlsx, ods, csv', 'wrongExtension' => 'Некорректный формат файла.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'format' => Yii::t('model', 'Формат'),
            'is_only_new' => Yii::t('model', 'Только новые'),
            'file' => Yii::t('model', 'Файл'),
        ];
    }

    /**
     * @return boolean
     */
    public function import() {
        if ($this->validate()) {
            $file = UploadedFile::getInstance($this, 'file');
            if ($file) {
                $objPHPExcel = \PHPExcel_IOFactory::load($file->tempName);
                $col = $objPHPExcel->getActiveSheet()->getHighestColumn(1);
                $row = $objPHPExcel->getActiveSheet()->getHighestRow('B');
                $sheetData = $objPHPExcel->getActiveSheet()->rangeToArray('A1:' . $col . $row, null, true, false, true);

                $createdCount = 0;
                $updatedCount = 0;
                foreach ($sheetData as $k => $row) {
                    // trim spaces in key columns
                    $row['A'] = trim($row['A']);
                    $row['B'] = trim($row['B']);
                    $row['E'] = trim($row['E']);
                    $row['F'] = trim($row['F']);

                    if ($k == 1) {
                        // header
                        continue;
                    } elseif (!isset($row['A']) && !isset($row['B']) && !isset($row['E']) && !isset($row['F'])) {
                        // no data row
                        continue;
                    } else {
                        // row data
                        $columnsMap = $this->getColumnsMap($row);

                        // clear models
                        $houseModel = null;
                        $flatModel = null;
                        $agencyModel = null;
                        $userModel = null;
                        $clientModel = null;

                        // house
                        $houseQuery = House::find();
                        if (isset($columnsMap['House']['name'])) {
                            $houseQuery->andWhere(['name' => $columnsMap['House']['name']]);
                        } else {
                            $houseQuery->andWhere(['or', ['is', 'name', null], ['name' => '']]);
                        }
                        if (isset($columnsMap['House']['section'])) {
                            $houseQuery->andWhere(['section' => $columnsMap['House']['section']]);
                        } else {
                            $houseQuery->andWhere(['or', ['is', 'section', null], ['section' => '']]);
                        }
                        
                        
                        $houseModel = $houseQuery->one();
                        if ((!isset($houseModel) || $houseModel === null) && (isset($columnsMap['House']['name']) && !empty(trim($columnsMap['House']['name'])))) {
                            // we are sure the house name is set
                            $houseModel = new House();
                            $houseModel->name = $columnsMap['House']['name'];
                            $houseModel->section = $columnsMap['House']['section'];
                            $houseModel->status = House::STATUS_AVAILABLE;
                            $houseModel->save();
                        }

                        // flat
                        $flatQuery = Flat::find();
                        $flatQuery->where(['id' => $columnsMap['Flat']['id']]);
                        if (!$flatQuery->exists()) {
                            $flatQuery->orWhere(['and', ['number' => $columnsMap['Flat']['number']], ['number_index' => $columnsMap['Flat']['number_index']], ['house_id' => $houseModel->id], ['unit_type' => $columnsMap['Flat']['unit_type']]]);
                        }
                        $flatModel = $flatQuery->one();

                        if (isset($flatModel) && $this->is_only_new) {
                            // if we only add
                            continue;
                        }

                        if (!isset($flatModel)) {
                            if (!$columnsMap['Flat']['number'] || !$houseModel) {
                                // do not have new flat number and valid house
                                continue;
                            }
                            $flatModel = new Flat();
                            $flatModel->scenario = 'create';
                            $itsNew = true;
                        }

                        // agency
                        if (array_filter($columnsMap['Agency'])) {
                            $agencyQuery = Agency::find();
                            if (isset($columnsMap['Agency']['name'])) {
                                $agencyQuery->andWhere(['name' => $columnsMap['Agency']['name']]);
                            }
                            $agencyModel = $agencyQuery->one();
                            if (!isset($agencyModel)) {
                                $agencyModel = new Agency();
                                $agencyModel->name = $columnsMap['Agency']['name'];
                                $agencyModel->status = Agency::STATUS_ACTIVE;
                                $agencyModel->save();
                            }
                        }

                        // user
                        if (array_filter($columnsMap['User'])) {
                            $name = explode(' ', $columnsMap['User']['name']);
                            $userQuery = User::find();
                            if (isset($columnsMap['User']['name'])) {
                                $userQuery->orFilterWhere(['lastname' => $name[0]]);
                                $userQuery->orFilterWhere(['firstname' => $name[1] ?? '']);
                                $userQuery->orFilterWhere(['middlename' => $name[2] ?? '']);
                            }
                            $userModel = $userQuery->one();
                            if (!isset($userModel)) {
                                $userModel = new User();
                                $userModel->lastname = $name[0];
                                $userModel->firstname = $name[1] ?? '';
                                $userModel->middlename = $name[2] ?? '';
                                $userModel->generateEmail();
                                $userModel->generatePassword();
                                $userModel->generateAuthKey();
                                $userModel->role = User::ROLE_MANAGER;
                                $userModel->status = User::STATUS_ACTIVE;
                                $userModel->save();
                            }
                        }

                        // client
                        if (array_filter($columnsMap['Client'])) {
                            $clientQuery = Client::find();
                            if (isset($columnsMap['Client']['email'])) {
                                $clientQuery->andWhere(['email' => $columnsMap['Client']['email']]);
                            }
                            if (isset($columnsMap['Client']['phone'])) {
                                $clientQuery->andWhere(['phone' => $columnsMap['Client']['phone']]);
                            }
                            if (isset($columnsMap['Client']['firstname']) || isset($columnsMap['Client']['middlename']) || isset($columnsMap['Client']['lastname'])) {
                                $clientQuery->andFilterWhere(['firstname' => $columnsMap['Client']['firstname']]);
                                $clientQuery->andFilterWhere(['middlename' => $columnsMap['Client']['middlename']]);
                                $clientQuery->andFilterWhere(['lastname' => $columnsMap['Client']['lastname']]);
                            }
                            $clientModel = $clientQuery->one();
                            if (!isset($clientModel)) {
                                $clientModel = new Client();
                                $clientModel->email = $columnsMap['Client']['email'];
                                $clientModel->phone = $columnsMap['Client']['phone'];
                                $clientModel->firstname = $columnsMap['Client']['firstname'];
                                $clientModel->middlename = $columnsMap['Client']['middlename'];
                                $clientModel->lastname = $columnsMap['Client']['lastname'];
                                $clientModel->agency_id = (isset($agencyModel->id)) ? $agencyModel->id : $clientModel->agency_id;
                                $clientModel->user_id = (isset($userModel->id)) ? $userModel->id : $clientModel->user_id;
                                $clientModel->save();
                            }
                        }

                        // flat update
                        if (isset($flatModel->isNewRecord)) {
                            $createdCount++;
                        } else {
                            $updatedCount++;
                        }

                        // change house, client only if new models are valid
                        $flatModel->house_id = $houseModel->id ?? $flatModel->house_id;
                        $flatModel->client_id = (isset($clientModel->id)) ? $clientModel->id : $flatModel->client_id;
                        $flatModel->agency_id = (isset($agencyModel->id)) ? $agencyModel->id : $flatModel->agency_id;
                        $flatModel->load($columnsMap);
                        $flatModel->price_discount_m = $flatModel->price_m - $flatModel->price_sell_m;
                        $flatModel->scenario = 'import_update';
                        $flatModel->save();

                        //create agreement
                        /*
                        if (isset($clientModel) && isset($flatModel) && $flatModel->status == Flat::STATUS_SOLD) {
                            $agreementExists = Agreement::isExistsByFlatAndClientId($flatModel->id, $clientModel->id);
                            if (!isset($agreementExists)) {
                                $agreement = new AgreementForm();
                                $agreement->uid = Agreement::generateUid();
                                $agreement->uid_date = date('Y-m-d');
                                $agreement->firstname = $columnsMap['Client']['firstname'];
                                $agreement->middlename = $columnsMap['Client']['middlename'];
                                $agreement->lastname = $columnsMap['Client']['lastname'];
                                $agreement->phone = $columnsMap['Client']['phone'];
                                $agreement->email = $columnsMap['Client']['email'];
                                $agreement->description = $columnsMap['Flat']['description'];
                                $agreement->number = $columnsMap['Flat']['number'];
                                $agreement->number_index = $columnsMap['Flat']['number_index'];
                                $agreement->unit_type = $columnsMap['Flat']['unit_type'];
                                $agreement->square = $columnsMap['Flat']['square'];
                                $agreement->floor = $columnsMap['Flat']['floor'];
                                $agreement->n_rooms = $columnsMap['Flat']['n_rooms'];
                                $agreement->price = $flatModel->getPriceSell();
                                $agreement->status = 0;
                                $agreement->flat_id = $flatModel->id;
                                $agreement->agency_id = $flatModel->agency_id;
                                $agreement->client_id = $clientModel->id;
                                $agreement->save();
                            } else {
                                $agreement = Agreement::find()->where(['flat_id' => $flatModel->id, 'client_id' => $clientModel->id])->one();
                                if (isset($agreement->id)) {
                                    $agrementFlats = \common\models\AgreementFlat::findAll(['agreement_id' => $agreement->id]);
                                    foreach ($agrementFlats as $agrementFlat) {
                                        $agrementFlat->price = $flatModel->getPriceSell();
                                        $agrementFlat->save();
                                    }
                                }
                            }

                            // commissions
                            if (isset($userModel) && isset($itsNew) && $itsNew === true) {
                                $userModel->createCommissionForFlat($flatModel, null, true);
                            }
                            if (isset($agencyModel)&& isset($itsNew) && $itsNew === true) {
                                $agencyModel->createCommissionForFlat($flatModel, null, true);
                            }

                            // flat left payment
                            if ((isset($flatModel->price_paid_init) || $flatModel->getPriceSell() > 0) && isset($itsNew) && $itsNew === true) {
                                
                                $pricePayLeft = $flatModel->getPriceSell() - $flatModel->price_paid_init;
                                $paymentModel = Payment::find()
                                        ->where(['flat_id' => $flatModel->id])
                                        ->one();
                                if ($pricePayLeft > 0) {
                                    if (!isset($paymentModel)) {
                                        $paymentModel = new Payment();
                                    }
                                    if ($paymentModel->isNewRecord) {
                                        $paymentModel->pay_number = $flatModel->getPayments()->count() + 1;
                                        $paymentModel->is_price_left = 1;
                                        $paymentModel->pay_date = date('Y-m-d');
                                        $paymentModel->flat_id = $flatModel->id;
                                    }
                                    $paymentModel->price_plan = $pricePayLeft;
                                    $paymentModel->price_fact = 0;
                                    $paymentModel->price_saldo = 0;
                                    $paymentModel->save();
                                } elseif ($paymentModel) {
                                    $paymentModel->delete();
                                }

                            }
                        }
                        */
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

    private function getColumnsMap($row) {
        foreach ($row as &$item) {
            $item = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $item);
        }

        $unitType = Flat::getUnitTypeByLabel($row['C']) ?: Flat::TYPE_FLAT;

        $priceTotal = floatval($this->filterFloatValue($row['L']));
        $priceSellTotal = floatval($this->filterFloatValue($row['M']));
        $priceM = floatval($this->filterFloatValue($row['J']));
        $priceSellM = floatval($this->filterFloatValue($row['K']));
        $pricePaidInit = floatval($this->filterFloatValue($row['N']));
        $square = floatval($this->filterFloatValue($row['I']));
        if (!$square && $unitType == Flat::TYPE_CAR_PLACE || $unitType == Flat::TYPE_PARKING) {
            $square = 1;
        }
        $commissionAgency = floatval($this->filterFloatValue($row['O']));
        $commissionManager = floatval($this->filterFloatValue($row['Q']));

        $priceMFromTotal = $square ? ($priceTotal / $square) : 0;
        $priceSellMFromTotal = $square ? ($priceSellTotal / $square) : 0;
        $commissionAgencyType = ucfirst(trim($row['P'])) == 'Процент' ? Flat::COMMISSION_TYPE_PERCENT : Flat::COMMISSION_TYPE_VALUE;
        $commissionManagerType = ucfirst(trim($row['R'])) == 'Процент' ? Flat::COMMISSION_TYPE_PERCENT : Flat::COMMISSION_TYPE_VALUE;
        $numberParts = explode('/', str_replace(["\\", '.', ',', '|', '-'], '/', $row['B']));
        $number = $numberParts[0];
        $numberIndex = (isset($numberParts[1]) && mb_strlen($numberParts[1]) > 0) ? $numberParts[1] : null;

        $clientPhone = $row['W'] . '';
        if ($clientPhone) {
            if (mb_strlen($clientPhone) == 12 && mb_substr($clientPhone, 0, 2) == '38') {
                $clientPhone = '+' . $clientPhone;
            }
            if (mb_strlen($clientPhone) == 11 && mb_substr($clientPhone, 0, 2) == '80') {
                $clientPhone = '+3' . $clientPhone;
            }
        }

        return [
            'Flat' => [
                'id' => $row['A'],
                'number' => $number,
                'number_index' => $numberIndex,
                'unit_type' => $unitType,
                'status' => Flat::getStatusByLabel($row['D']) ?: Flat::STATUS_UNAVAILABLE,
                'floor' => (int) $row['G'],
                'n_rooms' => (int) $row['H'],
                'square' => $square,
                'price_m' => $priceM ?: $priceMFromTotal,
                'price_sell_m' => $priceSellM ?: $priceSellMFromTotal,
                'price_paid_init' => $pricePaidInit,
                'commission_agency' => $commissionAgency,
                'commission_agency_type' => $commissionAgencyType,
                'commission_manager' => $commissionManager,
                'commission_manager_type' => $commissionManagerType,
                'description' => $row['S'] . '',
            ],
            'House' => [
                'name' => $row['E'],
                'section' => $row['F'],
            ],
            'Client' => [
                'lastname' => $row['T'],
                'firstname' => $row['U'],
                'middlename' => $row['V'],
                'phone' => $clientPhone,
                'email' => $row['X'],
            ],
            'Agency' => [
                'name' => $row['Y'],
            ],
            'User' => [
                'name' => $row['Z'],
            ],
        ];
    }

    private function filterFloatValue($value) {
        return str_replace(["'", ','], ['', '.'], preg_replace('/[[:^print:]]/', '', preg_replace('/\s+/', '', $value)));
    }

}
