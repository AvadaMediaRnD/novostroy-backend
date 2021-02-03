<?php

use yii\db\Migration;

/**
 * Class m180905_094056_replace_view_total_flat
 */
class m180905_094056_replace_view_total_flat extends Migration
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
-- View `view_total_flat`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `view_total_flat` AS 
SELECT f.*,
    h.name,
    h.section,
    (f.price_m * f.square) AS `price`,
    (f.price_sell_m * f.square) AS `price_sell`,
    (f.price_discount_m * f.square) AS `price_discount`,
    IF(a.uid IS NULL, NULL, SUM(p.price_plan)) AS `price_plan`,
    IF(a.uid IS NULL, NULL, SUM(p.price_fact)) AS `price_fact`,
    IF(a.uid IS NULL, NULL, (SUM(p.price_plan) - SUM(p.price_fact))) AS `price_left`,
    SUM(case when DATE_FORMAT(p.pay_date, '%Y-%m') <= DATE_FORMAT(NOW(), '%Y-%m') then (p.price_plan - p.price_fact) else 0 end) AS `price_debt`,
    
    IF(a.uid IS NULL, 4, 
        IF(SUM(p.price_plan) = SUM(p.price_fact), 10, 
            IF(a.uid AND SUM(p.price_fact) = 0, 6, 8)
        )
    ) as `sell_status`,

    a.uid,
    ac.firstname AS `client_firstname`,
    ac.middlename AS `client_middlename`,
    ac.lastname AS `client_lastname`,
    ac.phone,
    ac.email,
    u.firstname AS `user_firstname`,
    u.middlename AS `user_middlename`,
    u.lastname AS `user_lastname`,
    ag.id AS `agency_id`,
    ag.name AS `agency_name`
    
FROM `flat` f 
LEFT JOIN `house` h ON f.house_id = h.id
LEFT JOIN `payment` p ON p.flat_id = f.id
LEFT JOIN `agreement` a ON a.flat_id = f.id 
LEFT JOIN `agreement_client` ac ON ac.agreement_id = a.id 
LEFT JOIN `client` c ON a.client_id = c.id
LEFT JOIN `agency` ag ON c.agency_id = ag.id
LEFT JOIN `user` u ON c.user_id = u.id
GROUP BY f.id;

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
        echo "m180905_094056_replace_view_total_flat Was not reverted. Its OK.\n";
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180905_094056_replace_view_total_flat cannot be reverted.\n";

        return false;
    }
    */
}
