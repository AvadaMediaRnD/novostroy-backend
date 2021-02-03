<?php

use yii\db\Migration;

/**
 * Class m180806_125939_create_basic_tables
 */
class m180806_125939_create_basic_tables extends Migration
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
-- Table `house`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `house` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `section` VARCHAR(255) NULL,
  `address` VARCHAR(255) NULL,
  `n_floors` INT(11) NULL,
  `commission_agency` FLOAT NULL,
  `commission_manager` FLOAT NULL,
  `status` SMALLINT(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `agency`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agency` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `phone` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `status` SMALLINT(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `client`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `client` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(255) NULL,
  `middlename` VARCHAR(255) NULL,
  `lastname` VARCHAR(255) NULL,
  `address` VARCHAR(255) NULL,
  `inn` VARCHAR(255) NULL,
  `passport_series` VARCHAR(255) NULL,
  `passport_number` VARCHAR(255) NULL,
  `passport_from` VARCHAR(255) NULL,
  `phone` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `description` VARCHAR(255) NULL,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `agency_id` INT(11) NULL,
  `user_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_client_agency1_idx` (`agency_id` ASC),
  INDEX `fk_client_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_client_agency1`
    FOREIGN KEY (`agency_id`)
    REFERENCES `agency` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_client_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `flat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `flat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `number` INT(11) NULL,
  `n_rooms` INT(11) NULL,
  `floor` INT(11) NULL,
  `square` FLOAT NULL,
  `price_m` DECIMAL(14,4) NULL,
  `price_sell_m` DECIMAL(14,4) NULL,
  `price_discount_m` DECIMAL(14,4) NULL,
  `commission_agency` FLOAT NULL,
  `commission_manager` FLOAT NULL,
  `description` TEXT NULL,
  `status` SMALLINT(6) NOT NULL DEFAULT 0,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `house_id` INT(11) NOT NULL,
  `client_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_flat_house1_idx` (`house_id` ASC),
  INDEX `fk_flat_client1_idx` (`client_id` ASC),
  CONSTRAINT `fk_flat_house1`
    FOREIGN KEY (`house_id`)
    REFERENCES `house` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_flat_client1`
    FOREIGN KEY (`client_id`)
    REFERENCES `client` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `rieltor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rieltor` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(255) NULL,
  `phone` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `is_director` TINYINT(1) NULL DEFAULT 0,
  `agency_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_rieltor_agency1_idx` (`agency_id` ASC),
  CONSTRAINT `fk_rieltor_agency1`
    FOREIGN KEY (`agency_id`)
    REFERENCES `agency` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `agreement_template`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agreement_template` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `file` VARCHAR(255) NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `agreement`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agreement` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(255) NULL,
  `uid_date` DATE NULL,
  `file` VARCHAR(255) NULL,
  `scan_file` VARCHAR(255) NULL,
  `status` SMALLINT(6) NOT NULL DEFAULT 0,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `flat_id` INT(11) NULL,
  `agency_id` INT(11) NULL,
  `client_id` INT(11) NULL,
  `user_id` INT(11) NULL COMMENT 'не нужен?',
  `agreement_template_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_agreement_flat1_idx` (`flat_id` ASC),
  INDEX `fk_agreement_agency1_idx` (`agency_id` ASC),
  INDEX `fk_agreement_client1_idx` (`client_id` ASC),
  INDEX `fk_agreement_user1_idx` (`user_id` ASC),
  INDEX `fk_agreement_agreement_template1_idx` (`agreement_template_id` ASC),
  CONSTRAINT `fk_agreement_flat1`
    FOREIGN KEY (`flat_id`)
    REFERENCES `flat` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_agreement_agency1`
    FOREIGN KEY (`agency_id`)
    REFERENCES `agency` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_agreement_client1`
    FOREIGN KEY (`client_id`)
    REFERENCES `client` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_agreement_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_agreement_agreement_template1`
    FOREIGN KEY (`agreement_template_id`)
    REFERENCES `agreement_template` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `payment`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `payment` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `pay_number` INT(11) NULL,
  `pay_date` DATE NULL,
  `price_plan` DECIMAL(14,4) NULL,
  `price_fact` DECIMAL(14,4) NULL,
  `price_saldo` DECIMAL(14,4) NULL,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `flat_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_payment_flat1_idx` (`flat_id` ASC),
  CONSTRAINT `fk_payment_flat1`
    FOREIGN KEY (`flat_id`)
    REFERENCES `flat` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `cashbox`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `cashbox` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `currency` VARCHAR(255) NULL,
  `rate` DECIMAL(8,4) NULL DEFAULT 1,
  `is_default` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `article`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `article` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `type` ENUM('income', 'outcome') NULL,
  `name` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `counterparty`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `counterparty` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `table_name` VARCHAR(255) NULL,
  `table_id` INT(11) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `invoice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(255) NULL,
  `uid_date` DATE NULL,
  `price` DECIMAL(14,4) NULL,
  `rate` DECIMAL(8,4) NULL,
  `description` VARCHAR(255) NULL,
  `status` SMALLINT(6) NOT NULL DEFAULT 0,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `article_id` INT(11) NULL,
  `cashbox_id` INT(11) NULL,
  `flat_id` INT(11) NULL,
  `payment_id` INT(11) NULL,
  `counterparty_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_invoice_cashbox_idx` (`cashbox_id` ASC),
  INDEX `fk_invoice_article1_idx` (`article_id` ASC),
  INDEX `fk_invoice_payment1_idx` (`payment_id` ASC),
  INDEX `fk_invoice_flat1_idx` (`flat_id` ASC),
  INDEX `fk_invoice_counterparty1_idx` (`counterparty_id` ASC),
  CONSTRAINT `fk_invoice_cashbox`
    FOREIGN KEY (`cashbox_id`)
    REFERENCES `cashbox` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_invoice_article1`
    FOREIGN KEY (`article_id`)
    REFERENCES `article` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_invoice_payment1`
    FOREIGN KEY (`payment_id`)
    REFERENCES `payment` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_invoice_flat1`
    FOREIGN KEY (`flat_id`)
    REFERENCES `flat` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_invoice_counterparty1`
    FOREIGN KEY (`counterparty_id`)
    REFERENCES `counterparty` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `agreement_client`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agreement_client` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(255) NULL,
  `middlename` VARCHAR(255) NULL,
  `lastname` VARCHAR(255) NULL,
  `address` VARCHAR(255) NULL,
  `inn` VARCHAR(255) NULL,
  `passport_series` VARCHAR(255) NULL,
  `passport_number` VARCHAR(255) NULL,
  `passport_from` VARCHAR(255) NULL,
  `phone` VARCHAR(255) NULL,
  `email` VARCHAR(255) NULL,
  `description` VARCHAR(255) NULL,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `agreement_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_agreement_client_agreement1_idx` (`agreement_id` ASC),
  CONSTRAINT `fk_agreement_client_agreement1`
    FOREIGN KEY (`agreement_id`)
    REFERENCES `agreement` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `agreement_flat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `agreement_flat` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `number` INT(11) NULL,
  `n_rooms` INT(11) NULL,
  `floor` INT(11) NULL,
  `square` FLOAT NULL,
  `address` VARCHAR(255) NULL,
  `price` DECIMAL(14,4) NULL,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `agreement_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_agreement_flat_agreement1_idx` (`agreement_id` ASC),
  CONSTRAINT `fk_agreement_flat_agreement1`
    FOREIGN KEY (`agreement_id`)
    REFERENCES `agreement` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `system_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `system_config` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `type` ENUM('param', 'variable') NULL,
  `key` VARCHAR(255) NULL,
  `value_raw` TEXT NULL,
  `description` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `event` VARCHAR(255) NULL,
  `object_class` VARCHAR(255) NULL,
  `object_id` INT(11) NULL,
  `old_attributes` MEDIUMTEXT NULL,
  `message` TEXT NULL,
  `created_at` INT(11) NOT NULL,
  `updated_at` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_log_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_log_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

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

DROP TABLE IF EXISTS `house`;
DROP TABLE IF EXISTS `agency`;
DROP TABLE IF EXISTS `client`;
DROP TABLE IF EXISTS `flat`;
DROP TABLE IF EXISTS `rieltor`;
DROP TABLE IF EXISTS `agreement_template`;
DROP TABLE IF EXISTS `agreement_client`;
DROP TABLE IF EXISTS `agreement_flat`;
DROP TABLE IF EXISTS `agreement`;
DROP TABLE IF EXISTS `payment`;
DROP TABLE IF EXISTS `cashbox`;
DROP TABLE IF EXISTS `article`;
DROP TABLE IF EXISTS `counterparty`;
DROP TABLE IF EXISTS `invoice`;
DROP TABLE IF EXISTS `system_config`;
DROP TABLE IF EXISTS `user_log`;

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
        echo "m180806_125939_create_basic_tables cannot be reverted.\n";

        return false;
    }
    */
}
