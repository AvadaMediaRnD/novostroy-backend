<?php

use yii\db\Migration;

/**
 * Class m180806_145935_create_view_tables
 */
class m180806_145935_create_view_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- View `view_cashbox`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `view_cashbox` AS
SELECT 
	c.*,
    (SELECT SUM(i.price) FROM `invoice` i LEFT JOIN `article` a ON i.article_id = a.id WHERE i.cashbox_id = c.id AND a.type = 'income') AS `price_income`, 
    (SELECT SUM(i.price) FROM `invoice` i LEFT JOIN `article` a ON i.article_id = a.id WHERE i.cashbox_id = c.id AND a.type = 'outcome') AS `price_outcome`,
    ((SELECT SUM(i.price) FROM `invoice` i LEFT JOIN `article` a ON i.article_id = a.id WHERE i.cashbox_id = c.id AND a.type = 'income') - (SELECT SUM(i.price) FROM `invoice` i LEFT JOIN `article` a ON i.article_id = a.id WHERE i.cashbox_id = c.id AND a.type = 'outcome')) AS `price`
FROM `cashbox` c;

-- -----------------------------------------------------
-- View `view_house_total`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `view_house_total` AS
SELECT 
	h.id,
        h.name,
        h.section,
    COUNT(f.id) AS `flats_total`,
    SUM(case when f.status = 10 then 1 else 0 end) as `flats_available`,
    SUM(case when f.status = 1 then 1 else 0 end) as `flats_sold`,
    
    SUM(f.square) AS `square_total`,
    SUM(case when f.status = 10 then f.square else 0 end) AS `square_available`,
    SUM(case when f.status = 1 then f.square else 0 end) AS `square_sold`,
    
    SUM(f.price_sell_m * f.square) AS `price_total`,
    SUM(case when f.status = 10 then (f.price_sell_m * f.square) else 0 end) AS `price_available`,
    SUM(case when f.status = 1 then (f.price_sell_m * f.square) else 0 end) AS `price_sold`
FROM `house` h 
LEFT JOIN `flat` f ON f.house_id = h.id
GROUP BY h.id;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
            ";
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP VIEW IF EXISTS `view_cashbox`;
DROP VIEW IF EXISTS `view_house_total`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
            ";
        $this->execute($sql);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180813_075935_create_view_tables cannot be reverted.\n";

        return false;
    }
    */
}
