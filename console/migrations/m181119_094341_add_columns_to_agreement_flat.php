<?php

use yii\db\Migration;

/**
 * Class m181119_094341_add_columns_to_agreement_flat
 */
class m181119_094341_add_columns_to_agreement_flat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agreement_flat', 'unit_type', $this->string(255)->null()->defaultValue(common\models\Flat::TYPE_FLAT)->after('number'));
        $this->addColumn('agreement_flat', 'number_index', $this->string(255)->null()->after('number'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('agreement_flat', 'number_index');
        $this->dropColumn('agreement_flat', 'unit_type');
    }
}
