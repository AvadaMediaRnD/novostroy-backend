<?php

use yii\db\Migration;

/**
 * Class m190814_104230_replace_view_house_total
 */
class m190814_104230_replace_view_house_total extends Migration
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
-- View `view_house_total`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `view_house_total` AS
SELECT 
	h.id,
        h.name,
        h.section,
    COUNT(f.id) AS `flats_total`,
    SUM(case when f.status IN (10,8,7,5) then 1 else 0 end) as `flats_available`,
    SUM(case when f.status IN (2,1,0) then 1 else 0 end) as `flats_sold`,
    
    SUM(f.square) AS `square_total`,
    SUM(case when f.status IN (10,8,7,5) then f.square else 0 end) AS `square_available`,
    SUM(case when f.status IN (2,1,0) then f.square else 0 end) AS `square_sold`,
    
    SUM(f.price_sell_m * f.square) AS `price_total`,
    SUM(case when f.status IN (10,8,7,5) then (f.price_sell_m * f.square) else 0 end) AS `price_available`,
    SUM(case when f.status IN (2,1,0) then (f.price_sell_m * f.square) else 0 end) AS `price_sold`
FROM `house` h 
LEFT JOIN `flat` f ON f.house_id = h.id 
WHERE f.unit_type NOT IN ('parking', 'car_place')
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
        echo "m190814_104230_replace_view_house_total Was not reverted. Its OK.\n";
    }
}
