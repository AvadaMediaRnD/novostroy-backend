<?php

use yii\db\Migration;

/**
 * Class m190201_141007_replace_view_total_plan_fact
 */
class m190201_141007_replace_view_total_plan_fact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // delete wrong migration record
        $this->delete('migration', ['version' => 'm181205_122217_replace_view_total_plan_fact']);
        
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- View `view_total_plan_fact`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `view_total_plan_fact` AS 
SELECT
	f.house_id,
    YEAR(p.pay_date) AS `year`,
    MONTH(p.pay_date) AS `month`,
    DATE_FORMAT(p.pay_date, '%Y-%m') AS `year_month`,
    SUM(p.price_plan) AS `price_plan_total`,
    SUM(p.price_fact) AS `price_fact_total`,
    SUM(p.price_saldo) AS `price_saldo_total`,
    -- IF(DATE_FORMAT(p.pay_date, '%Y-%m') <= DATE_FORMAT(NOW(), '%Y-%m'), (SUM(p.price_plan) - SUM(p.price_fact)), 0) AS `price_debt_total`
    (SUM(p.price_plan) - SUM(p.price_fact)) AS `price_debt_total`
FROM `flat` f
LEFT JOIN `payment` p ON p.flat_id = f.id
WHERE p.id IS NOT NULL
GROUP BY f.house_id, `year_month`;

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
        echo "m190201_141007_replace_view_total_plan_fact Was not reverted. Its OK.\n";
    }
}
