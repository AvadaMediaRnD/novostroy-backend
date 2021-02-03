<?php

use yii\db\Migration;

/**
 * Class m190124_111912_add_more_articles
 */
class m190124_111912_add_more_articles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('article', 
            ['id', 'type', 'name', 'is_custom'], 
            [
                [9, 'income', 'Продажа', 0],
                [10, 'income', 'Резерв', 0],
                [11, 'outcome', 'Доп. метры', 0],
                [12, 'outcome', 'Канцтовары', 0],
                [13, 'outcome', 'Химия', 0],
                [14, 'outcome', 'Зарплата', 0],
            ]
        );
        $this->update('article', ['name' => 'Ежемесячный платеж'], ['id' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('article', ['in', 'id', [9,10,11,12,13,14]]);
        $this->update('article', ['name' => 'Оплата за квартиру'], ['id' => 1]);
    }
}
