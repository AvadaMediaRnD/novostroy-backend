<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\helpers\PriceHelper;

/**
 * This is the model class for table "agreement".
 *
 * @property int $id
 * @property string $uid
 * @property string $uid_date
 * @property string $file
 * @property string $scan_file
 * @property string $description
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $flat_id
 * @property int $agency_id
 * @property int $client_id
 * @property int $user_id не нужен?
 * @property int $agreement_template_id
 * @property string $tpl_house_address
 * @property string $tpl_client_fullname
 * @property string $tpl_client_fullname_short
 * @property string $tpl_client_birthdate_text
 * @property string $tpl_client_passport_from
 *
 * @property Agency $agency
 * @property AgreementTemplate $agreementTemplate
 * @property Client $client
 * @property Flat $flat
 * @property User $user
 * @property AgreementClient $agreementClient
 * @property AgreementFlat $agreementFlat
 */
class Agreement extends \common\models\ZModel
{
    const STATUS_PENDING = 5;
    const STATUS_SIGNED = 10;
    const STATUS_DRAFT = 0;
    
    public $plan_image = '';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement';
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
            [['uid_date'], 'safe'],
            [['status', 'created_at', 'updated_at', 'flat_id', 'agency_id', 'client_id', 'user_id', 'agreement_template_id'], 'integer'],
            [['uid', 'file', 'scan_file'], 'string', 'max' => 255],
            ['description', 'string'],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['agreement_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => AgreementTemplate::className(), 'targetAttribute' => ['agreement_template_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [[
                'tpl_house_address', 'tpl_client_fullname', 'tpl_client_fullname_short', 
                'tpl_client_birthdate_text', 'tpl_client_passport_from',
            ], 'string', 'max' => 255],
            [['plan_image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', '№ договора'),
            'uid_date' => Yii::t('app', 'Дата продажи'),
            'file' => Yii::t('app', 'File'),
            'scan_file' => Yii::t('app', 'Scan File'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Статус'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'flat_id' => Yii::t('app', 'Flat ID'),
            'agency_id' => Yii::t('app', 'Агентство'),
            'client_id' => Yii::t('app', 'Покупатель'),
            'user_id' => Yii::t('app', 'Менеджер'),
            'agreement_template_id' => Yii::t('app', 'Agreement Template ID'),
            
            'tpl_house_address' => Yii::t('model', 'Адрес дома'),
            'tpl_client_fullname' => Yii::t('model', 'ФИО клиента'),
            'tpl_client_fullname_short' => Yii::t('model', 'ФИО клиента с инициалами'),
            'tpl_client_birthdate_text' => Yii::t('model', 'Дата рождения клиента'),
            'tpl_client_passport_from' => Yii::t('model', 'Паспорт кем выдан клиента'),
            
            'plan_image' => Yii::t('app', 'План квартиры'),
        ];
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
                Yii::error("Cannot delete file $file or $fileHtml", 'Agreement');
            }
        }
        if ($this->scan_file) {
            try {
                $scanFile = Yii::getAlias('@frontend/web' . $this->scan_file);
                if (file_exists($scanFile)) {
                    unlink($scanFile);
                }
            } catch (\yii\base\ErrorException $e) {
                Yii::error("Cannot delete file $scanFile", 'Agreement');
            }
        }
        return parent::delete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreementTemplate()
    {
        return $this->hasOne(AgreementTemplate::className(), ['id' => 'agreement_template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreementClient()
    {
        return $this->hasOne(AgreementClient::className(), ['agreement_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreementFlat()
    {
        return $this->hasOne(AgreementFlat::className(), ['agreement_id' => 'id']);
    }
    
    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_PENDING => Yii::t('model', 'В рассмотрении'),
            static::STATUS_SIGNED => Yii::t('model', 'Подписан'),
            static::STATUS_DRAFT => Yii::t('model', 'Черновик'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }
    
    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'text-default';
        if ($status == static::STATUS_PENDING) {
            $itemClass = 'text-orange';
        } elseif ($status == static::STATUS_SIGNED) {
            $itemClass = 'text-green';
        } elseif ($status == static::STATUS_DRAFT) {
            $itemClass = 'text-default';
        }
        return '<span class="'.$itemClass.'">'.$statusLabel.'</span>';
    }
    
    /**
     * 
     * @return string
     */
    public function getUidDate()
    {
        if (!$this->uid_date) {
            return $this->uid_date;
        }
        return Yii::$app->formatter->asDate($this->uid_date);
    }
    
    /**
     * Get short preview string for description
     * @return type
     */
    public function getDescriptionShort()
    {
        $description = trim(strip_tags($this->description));
        $short = mb_substr($description, 0, 48);
        if (mb_strlen($short) < mb_strlen($description)) {
            $short .= '...';
        }
        return $short;
    }
    
    public function getPlanImage($isFullPath = false)
    {
        $path = '/upload/Agreement/' . $this->id . '/plan_image'.$this->uid.'.' . 'jpg'; 
        $pathFull = Yii::getAlias('@frontend/web' . $path);
        if (file_exists($pathFull)) {
            return $isFullPath ? $pathFull : $path;
        }
        return null;
    }
    
    /**
     * @return string
     */
    public static function generateUid()
    {
        return date('ymd') . sprintf('%05d', static::find()->max('id') + 1);
    }
    
    /**
     * If exists Flat by FlatId and ClientId
     * @return true|false
     */
    public static function isExistsByFlatAndClientId($flat_id, $client_id)
    {
        return static::find()->where(['flat_id' => $flat_id, 'client_id' => $client_id ])->exists();
    }
    
    /**
     * Generate agreement file with content based on template
     */
    public function generateFile()
    {
        if ($this->agreementTemplate && $this->agreementFlat && $this->agreementClient) {
            $templateFilePathFull = Yii::getAlias('@frontend/web' . $this->agreementTemplate->file);
            if (file_exists($templateFilePathFull)) {
                $templateProcessor = new \common\override\PhpWord\TemplateProcessor($templateFilePathFull);
                
                // image
                if ($this->getPlanImage(true)) {
                    $templateProcessor->setImageValueRaw('image1.jpeg', $this->getPlanImage(true));
                }

                // plan
                $templateProcessor->cloneRow('ROW_N', $this->flat->getPayments()->count());
                
                $cashboxUsd = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);
                $paymentPricePlanTotal = 0;
                $paymentPricePlanUahTotal = 0;
                foreach ($this->flat->payments as $k => $payment) {
                    $paymentPricePlan = round($payment->price_plan, 2);
                    $paymentPricePlanUah = round($cashboxUsd->rate * $payment->price_plan, 2);
                    $paymentPricePlanTotal += $paymentPricePlan;
                    $paymentPricePlanUahTotal += $paymentPricePlanUah;
            
                    $r = $k + 1;
                    $templateProcessor->setValue('ROW_N#' . $r, $payment->pay_number);
                    $templateProcessor->setValue('ROW_USD#' . $r, $paymentPricePlan);
                    $templateProcessor->setValue('ROW_UAH#' . $r, $paymentPricePlanUah);
                    $templateProcessor->setValue('ROW_DATE#' . $r, $payment->getPayDate());
                }
                
                // text values
                $templateProcessor->setValue([
                    'HOUSE_ADDRESS',
                    'HOUSE_NAME',
                    'FLAT_NUMBER_INDEX',
                    'CLIENT_FULLNAME_SHORT',
                    'CLIENT_FULLNAME',
                    'AGREEMENT_DATE',
                    'AGREEMENT_NUMBER',
                    
                    'CLIENT_BIRTHDATE_TEXT',
                    'CLIENT_PASSPORT_SERIES',
                    'CLIENT_PASSPORT_NUMBER',
                    'CLIENT_PASSPORT_FROM',
                    'CLIENT_INN',
                    'CLIENT_PHONE',
                    'FLAT_SQUARE',
                    'FLAT_FLOOR',
                    'PRICE_UAH',
                    'PRICE_UAH_TEXT',
                    'PRICE_USD',
                    'PRICE_USD_TEXT',
                    'RATE_USD',
                    'RATE_USD_TEXT',
                    'HOUSE_SECTION',
                    'FLAT_BUILD_NUMBER',
                    'HOUSE_COMPANY_REG_INFO',
                    'PAYMENT_PRICE_TOTAL_USD',
                    'PAYMENT_PRICE_TOTAL_UAH',
                    //'FLAT_FLOOR_PLAN_IMG',
                    //'PAYMENT_PLAN',
                ], [
                    $this->tpl_house_address ?: $this->agreementFlat->address,
                    $this->flat->house->name,
                    $this->agreementFlat->number . ($this->agreementFlat->number_index ? ('/' . $this->agreementFlat->number_index) : ''),
                    $this->tpl_client_fullname_short ?: ($this->agreementClient->lastname 
                        . ($this->agreementClient->firstname ? ' ' . mb_substr($this->agreementClient->firstname, 0, 1) . '.' : '')
                        . ($this->agreementClient->middlename ? ' ' . mb_substr($this->agreementClient->middlename, 0, 1) . '.' : '')),
                    $this->tpl_client_fullname ?: ($this->agreementClient->lastname 
                        . ($this->agreementClient->firstname ? ' ' . $this->agreementClient->firstname : '')
                        . ($this->agreementClient->middlename ? ' ' . $this->agreementClient->middlename : '')),
                    $this->getUidDate(),
                    $this->uid,
                    
                    $this->tpl_client_birthdate_text ?: $this->agreementClient->birthdateText,
                    $this->agreementClient->passport_series,
                    $this->agreementClient->passport_number,
                    $this->tpl_client_passport_from ?: $this->agreementClient->passport_from,
                    $this->agreementClient->inn,
                    $this->agreementClient->phone,
                    $this->agreementFlat->square,
                    $this->agreementFlat->floor,
                    PriceHelper::format($this->agreementFlat->getPriceForCashbox(Cashbox::CURRENCY_UAH), false, false, '', '', ''),
                    $this->agreementFlat->getPriceTextForCashbox(Cashbox::CURRENCY_UAH),
                    PriceHelper::format($this->agreementFlat->getPriceForCashbox(Cashbox::CURRENCY_USD), false, false, '', '', ''),
                    $this->agreementFlat->getPriceTextForCashbox(Cashbox::CURRENCY_USD),
                    round($this->agreementFlat->rate, 2),
                    $this->agreementFlat->getRateText(),
                    $this->flat->house->section,
                    $this->flat->getBuildNumber(),
                    $this->flat->house->getCompanyRegInfo(),
                    $paymentPricePlanTotal,
                    $paymentPricePlanUahTotal
                    //$this->flat->getFloorFlatImg(true, false),
                    //'TMP', // $this->flat->getPaymentPlanForAgreement($this),
                ]);
                
                // save file
                $path = '/upload/Agreement/' . $this->id . '/agreement-'.$this->uid.'.docx';
                $pathFull = Yii::getAlias('@frontend/web' . $path);
                $dir = dirname($pathFull);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                
                $templateProcessor->saveAs($pathFull);
            }
        }
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
    
    /**
     * Get content of file
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
