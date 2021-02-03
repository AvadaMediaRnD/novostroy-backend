<?php

use yii\db\Migration;

/**
 * Class m181112_144757_add_commission_types
 */
class m181112_144757_add_commission_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('house', 'commission_manager_type', $this->tinyInteger(1)->defaultValue(0)->notNull()->after('commission_manager'));
        $this->addColumn('house', 'commission_agency_type', $this->tinyInteger(1)->defaultValue(0)->notNull()->after('commission_manager'));
        $this->addColumn('flat', 'commission_manager_type', $this->tinyInteger(1)->defaultValue(0)->notNull()->after('commission_manager'));
        $this->addColumn('flat', 'commission_agency_type', $this->tinyInteger(1)->defaultValue(0)->notNull()->after('commission_manager'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('house', 'commission_agency_type');
        $this->dropColumn('house', 'commission_manager_type');
        $this->dropColumn('flat', 'commission_agency_type');
        $this->dropColumn('flat', 'commission_manager_type');
    }
}
