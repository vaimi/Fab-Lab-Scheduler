-- MySQL Script generated by MySQL Workbench
-- 10/11/15 22:51:25
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema fablab
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema fablab
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `fablab` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `fablab` ;

-- -----------------------------------------------------
-- Table `fablab`.`Machinegroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`Machinegroups` (
  `machinegroup_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(50) NULL,
  `Machinegroupscol` VARCHAR(45) NULL,
  PRIMARY KEY (`machinegroup_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fablab`.`Machines`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`Machines` (
  `machine_id` INT NOT NULL AUTO_INCREMENT,
  `Machinegroups_machinegroup_id` INT NOT NULL,
  `manufacturer` VARCHAR(50) NOT NULL,
  `model` VARCHAR(50) NOT NULL,
  `needs_supervision` TINYINT(1) NOT NULL,
  `description` VARCHAR(45) NULL,
  PRIMARY KEY (`machine_id`, `Machinegroups_machinegroup_id`),
  INDEX `fk_Machines_Machinegroups_idx` (`Machinegroups_machinegroup_id` ASC),
  CONSTRAINT `fk_Machines_Machinegroups`
    FOREIGN KEY (`Machinegroups_machinegroup_id`)
    REFERENCES `fablab`.`Machinegroups` (`machinegroup_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL,
  `pass` VARCHAR(64) CHARACTER SET 'utf8' NOT NULL,
  `name` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `banned` TINYINT(1) NULL DEFAULT '0',
  `last_login` DATETIME NULL DEFAULT NULL,
  `last_activity` DATETIME NULL DEFAULT NULL,
  `last_login_attempt` DATETIME NULL DEFAULT NULL,
  `forgot_exp` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `remember_time` DATETIME NULL DEFAULT NULL,
  `remember_exp` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `verification_code` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `totp_secret` VARCHAR(16) CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `ip_address` TEXT CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `login_attempts` INT(11) NULL DEFAULT '0',
  `surname` VARCHAR(512) NULL,
  `address_street` VARCHAR(512) NULL,
  `address_postal_code` VARCHAR(512) NULL,
  `phone_number` VARCHAR(30) NULL,
  `student_id` VARCHAR(20) NULL,
  `quota` DECIMAL NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`Userlevels`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`Userlevels` (
  `Machines_machine_id` INT NOT NULL,
  `Users_user_id` INT(11) UNSIGNED NOT NULL,
  `level` INT NOT NULL,
  PRIMARY KEY (`Machines_machine_id`, `Users_user_id`),
  INDEX `fk_Userlevels_Machines1_idx` (`Machines_machine_id` ASC),
  INDEX `fk_Userlevels_aauth_users1_idx` (`Users_user_id` ASC),
  CONSTRAINT `fk_Userlevels_Machines1`
    FOREIGN KEY (`Machines_machine_id`)
    REFERENCES `fablab`.`Machines` (`machine_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Userlevels_aauth_users1`
    FOREIGN KEY (`Users_user_id`)
    REFERENCES `fablab`.`aauth_users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fablab`.`Reservations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`Reservations` (
  `reservation_id` INT NOT NULL AUTO_INCREMENT,
  `Machines_machine_id` INT NOT NULL,
  `Users_user_id` INT(11) UNSIGNED NOT NULL,
  `start_time` INT NOT NULL,
  `end_time` INT NOT NULL,
  `qrcode` VARCHAR(256) NOT NULL,
  `passcode` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`reservation_id`, `Machines_machine_id`, `Users_user_id`),
  INDEX `fk_Reservations_Machines1_idx` (`Machines_machine_id` ASC),
  INDEX `fk_Reservations_aauth_users1_idx` (`Users_user_id` ASC),
  CONSTRAINT `fk_Reservations_Machines1`
    FOREIGN KEY (`Machines_machine_id`)
    REFERENCES `fablab`.`Machines` (`machine_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Reservations_aauth_users1`
    FOREIGN KEY (`Users_user_id`)
    REFERENCES `fablab`.`aauth_users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fablab`.`Supervisions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`Supervisions` (
  `supervision_id` INT NOT NULL AUTO_INCREMENT,
  `Users_user_id` INT(11) UNSIGNED NOT NULL,
  `start_time` VARCHAR(512) NOT NULL,
  `end_time` VARCHAR(512) NOT NULL,
  PRIMARY KEY (`supervision_id`, `Users_user_id`),
  INDEX `fk_Supervisions_aauth_users1_idx` (`Users_user_id` ASC),
  CONSTRAINT `fk_Supervisions_aauth_users1`
    FOREIGN KEY (`Users_user_id`)
    REFERENCES `fablab`.`aauth_users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fablab`.`Settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`Settings` (
  `setting_key` VARCHAR(20) NOT NULL,
  `setting_value` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`setting_key`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NULL DEFAULT NULL,
  `definition` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_perms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_perms` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NULL DEFAULT NULL,
  `definition` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_perm_to_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_perm_to_group` (
  `perm_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `group_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`perm_id`, `group_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_perm_to_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_perm_to_user` (
  `perm_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`perm_id`, `user_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_pms`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_pms` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` INT(11) UNSIGNED NOT NULL,
  `receiver_id` INT(11) UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NULL DEFAULT NULL,
  `date_sent` DATETIME NULL DEFAULT NULL,
  `date_read` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `full_index` (`id` ASC, `sender_id` ASC, `receiver_id` ASC, `date_read` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_system_variables`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_system_variables` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `data_key` VARCHAR(100) NOT NULL,
  `value` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_user_to_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_user_to_group` (
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`, `group_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `fablab`.`aauth_user_variables`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `fablab`.`aauth_user_variables` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `data_key` VARCHAR(100) NOT NULL,
  `value` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `user_id_index` (`user_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;