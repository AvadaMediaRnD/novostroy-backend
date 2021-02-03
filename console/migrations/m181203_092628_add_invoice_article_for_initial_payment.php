<?php

use yii\db\Migration;
use common\models\Article;

/**
 * Class m181203_092628_add_invoice_article_for_initial_payment
 */
class m181203_092628_add_invoice_article_for_initial_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('article', [
            'id' => 8,
            'type' => Article::TYPE_INCOME,
            'name' => 'Изначально внесено за квартиру'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('article', ['id' => 8]);
    }

}
