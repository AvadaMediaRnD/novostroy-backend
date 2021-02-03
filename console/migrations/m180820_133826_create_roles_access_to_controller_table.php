<?php

use yii\db\Migration;

/**
 * Handles the creation of table `roles_access_to_controller`.
 * Has foreign keys to the tables:
 *
 * - `roles`
 */
class m180820_133826_create_roles_access_to_controller_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('roles_access_to_controller', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer()->notNull(),
            'controller_name' => $this->string(),
        ]);

        // creates index for column `role_id`
        $this->createIndex(
            'idx-roles_access_to_controller-role_id',
            'roles_access_to_controller',
            'role_id'
        );

        // add foreign key for table `roles`
        $this->addForeignKey(
            'fk-roles_access_to_controller-role_id',
            'roles_access_to_controller',
            'role_id',
            'roles',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `roles`
        $this->dropForeignKey(
            'fk-roles_access_to_controller-role_id',
            'roles_access_to_controller'
        );

        // drops index for column `role_id`
        $this->dropIndex(
            'idx-roles_access_to_controller-role_id',
            'roles_access_to_controller'
        );

        $this->dropTable('roles_access_to_controller');
    }
}
