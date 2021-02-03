<?php

namespace console\controllers;

use yii\console\Controller;

class DevController extends Controller {

    /**
     * Cron task to update Cashbox rates
     */
    public function actionResetDatabaseViews() {
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
    SUM(case when f.status = 1 and f.square > 0 then (f.price_sell_m * f.square) else 0 end) AS `price_sold`,
    SUM(case when f.status = 1 and f.square <= 0 then (f.price_sell_m) else 0 end) AS `price_sold_park`
FROM `house` h 
LEFT JOIN `flat` f ON f.house_id = h.id
GROUP BY h.id;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- View `view_total_plan_fact`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `view_total_plan_fact` AS 
SELECT
	f.house_id,
    YEAR(p.pay_date) AS `year`,
    MONTH(p.pay_date) AS `month`,
    DATE_FORMAT(p.pay_date, '%Y-%m') AS `year_month`,
    SUM(case when f.status = 1 then (f.price_sell_m * f.square) else 0 end) AS `price_sold`, 
    SUM(p.price_plan) AS `price_plan_total`,
    SUM(case when inv.status = 1 then (p.price_fact) else 0 end) AS `price_fact_total`,
    SUM(p.price_saldo) AS `price_saldo_total`,
    IF(DATE_FORMAT(p.pay_date, '%Y-%m') <= DATE_FORMAT(NOW(), '%Y-%m'), (SUM(p.price_plan) - SUM(p.price_fact)), 0) AS `price_debt_total`
FROM `payment` p
LEFT JOIN `flat` f ON p.flat_id = f.id
LEFT JOIN `invoice` inv ON inv.payment_id = p.id
WHERE f.status = 1
GROUP BY f.house_id, `year_month`;

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
    SUM(p.price_plan) AS `price_plan`,
    SUM(p.price_fact) AS `price_fact`,
    (SUM(p.price_plan) - SUM(p.price_fact)) AS `price_left`,
    (0 - SUM(p.price_saldo)) AS `price_debt`,
    f.status as `sell_status`,
    c.firstname AS `client_firstname`,
    c.middlename AS `client_middlename`,
    c.lastname AS `client_lastname`,
    c.phone,
    c.email,
    u.firstname AS `user_firstname`,
    u.middlename AS `user_middlename`,
    u.lastname AS `user_lastname`,
    ag.name AS `agency_name`
    
FROM `flat` f 
LEFT JOIN `house` h ON f.house_id = h.id
LEFT JOIN `payment` p ON p.flat_id = f.id
LEFT JOIN `client` c ON f.client_id = c.id
LEFT JOIN `agency` ag ON f.agency_id = ag.id
LEFT JOIN `user` u ON c.user_id = u.id
LEFT JOIN `invoice` inv ON inv.payment_id = p.id
GROUP BY f.id;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
            ";
        \Yii::$app->db->createCommand($sql)->execute();

        print("\nViews updated");
    }

    /**
     * Cron task to update Cashbox rates
     */
    public function actionResetInformationViews() {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- View `view_notification`
-- -----------------------------------------------------
CREATE OR REPLACE VIEW `view_notification` AS
SELECT 
    f.id,
    f.number,
    f.unit_type,
    SUM(p.price_saldo) AS `price_saldo_total`
FROM `flat` f
LEFT JOIN `payment` p ON p.flat_id = f.id
GROUP BY f.id;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
            ";
        \Yii::$app->db->createCommand($sql)->execute();

        print("\nViews updated");
    }

}
