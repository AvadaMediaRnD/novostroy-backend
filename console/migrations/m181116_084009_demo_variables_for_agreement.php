<?php

use yii\db\Migration;
use common\models\SystemConfig;

/**
 * Class m181116_084009_demo_variables_for_agreement
 */
class m181116_084009_demo_variables_for_agreement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('system_config', ['id', 'type', 'key', 'value_raw', 'description'], [
            [1, SystemConfig::TYPE_VARIABLE, '{{AGREEMENT_NUMBER}}', 'Номер договора', 'Описания для переменной'],
            [2, SystemConfig::TYPE_VARIABLE, '{{AGREEMENT_DATE}}', 'Дата договора', 'Описания для переменной'],
            [3, SystemConfig::TYPE_VARIABLE, '{{CLIENT_FULLNAME}}', 'ФИО клиента', 'Описания для переменной'],
            [4, SystemConfig::TYPE_VARIABLE, '{{CLIENT_FULLNAME_SHORT}}', 'ФИО клиента с инициалами', 'Описания для переменной'],
            [5, SystemConfig::TYPE_VARIABLE, '{{FLAT_NUMBER_INDEX}}', 'Номер квартиры с индексом', 'Описания для переменной'],
            [6, SystemConfig::TYPE_VARIABLE, '{{HOUSE_NAME}}', 'Название объекта', 'Описания для переменной'],
            [7, SystemConfig::TYPE_VARIABLE, '{{HOUSE_ADDRESS}}', 'Адрес объекта', 'Описания для переменной'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('system_config');
    }
}
