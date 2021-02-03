<?php

use yii\db\Migration;
use common\models\SystemConfig;

/**
 * Class m181225_093232_agreement_variables
 */
class m181225_093232_agreement_variables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('system_config', ['type', 'key', 'value_raw', 'description'], [
            [SystemConfig::TYPE_VARIABLE, '{{CLIENT_BIRTHDATE_TEXT}}', 'Дата рождения клиента', ''],
            [SystemConfig::TYPE_VARIABLE, '{{CLIENT_PASSPORT_SERIES}}', 'Паспорт клиента серия', ''],
            [SystemConfig::TYPE_VARIABLE, '{{CLIENT_PASSPORT_NUMBER}}', 'Паспорт клиента номер', ''],
            [SystemConfig::TYPE_VARIABLE, '{{CLIENT_PASSPORT_FROM}}', 'Паспорт клиента выдан', ''],
            [SystemConfig::TYPE_VARIABLE, '{{CLIENT_INN}}', 'ИНН клиента', ''],
            [SystemConfig::TYPE_VARIABLE, '{{CLIENT_PHONE}}', 'Телефон клиента', ''],
            [SystemConfig::TYPE_VARIABLE, '{{FLAT_SQUARE}}', 'Площадь квартиры', ''],
            [SystemConfig::TYPE_VARIABLE, '{{FLAT_FLOOR}}', 'Этаж квартиры', ''],
            [SystemConfig::TYPE_VARIABLE, '{{PRICE_UAH}}', 'Сумма по договору, грн', ''],
            [SystemConfig::TYPE_VARIABLE, '{{PRICE_UAH_TEXT}}', 'Сумма по договору числами и словами, грн', ''],
            [SystemConfig::TYPE_VARIABLE, '{{PRICE_USD}}', 'Сумма по договору, usd', ''],
            [SystemConfig::TYPE_VARIABLE, '{{PRICE_USD_TEXT}}', 'Сумма по договору числами и словами, usd', ''],
            [SystemConfig::TYPE_VARIABLE, '{{RATE_USD}}', 'Курс валют, usd', ''],
            [SystemConfig::TYPE_VARIABLE, '{{RATE_USD_TEXT}}', 'Курс валют числами и словами, usd', ''],
            [SystemConfig::TYPE_VARIABLE, '{{HOUSE_SECTION}}', 'Номер секции', ''],
            [SystemConfig::TYPE_VARIABLE, '{{FLAT_BUILD_NUMBER}}', 'Строительный номер помещения', ''],
            [SystemConfig::TYPE_VARIABLE, '{{HOUSE_COMPANY_REG_INFO}}', 'Реквизиты компании', ''],
            [SystemConfig::TYPE_VARIABLE, '{{FLAT_FLOOR_PLAN_IMG}}', 'План этажа (изображение)', ''],
            [SystemConfig::TYPE_VARIABLE, '{{PAYMENT_PLAN}}', 'График платежей в таблице', ''],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('system_config', ['in', 'key', [
            '{{CLIENT_BIRTHDATE_TEXT}}',
            '{{CLIENT_PASSPORT_SERIES}}',
            '{{CLIENT_PASSPORT_NUMBER}}',
            '{{CLIENT_PASSPORT_FROM}}',
            '{{CLIENT_INN}}',
            '{{CLIENT_PHONE}}',
            '{{FLAT_SQUARE}}',
            '{{FLAT_FLOOR}}',
            '{{PRICE_UAH}}',
            '{{PRICE_UAH_TEXT}}',
            '{{PRICE_USD}}',
            '{{PRICE_USD_TEXT}}',
            '{{RATE_USD}}',
            '{{RATE_USD_TEXT}}',
            '{{HOUSE_SECTION}}',
            '{{FLAT_BUILD_NUMBER}}',
            '{{HOUSE_COMPANY_REG_INFO}}',
            '{{PAYMENT_PLAN}}',
        ]]);
    }

}
