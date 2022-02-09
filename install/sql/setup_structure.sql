SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `melis_cms_scripts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `melis_cms_scripts` (
  `mcs_id` INT NOT NULL AUTO_INCREMENT,
  `mcs_site_id` int(11) DEFAULT NULL,
  `mcs_page_id` int(11) DEFAULT NULL,
  `mcs_head_top` text DEFAULT NULL,
  `mcs_head_bottom` text DEFAULT NULL,
  `mcs_body_bottom` text DEFAULT NULL,
  `mcs_date_edition` datetime NOT NULL,
  `mcs_user_id` int(11) NOT NULL,
  PRIMARY KEY (`mcs_id`))
ENGINE=InnoDB;


-- -----------------------------------------------------
-- Table `melis_cms_scripts_exceptions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `melis_cms_scripts_exceptions` (
  `mcse_id` int(11) NOT NULL AUTO_INCREMENT,
  `mcse_site_id` int(11) NOT NULL,
  `mcse_page_id` int(11) NOT NULL,
  `mcse_date_creation` datetime NOT NULL,
  `mcse_user_id` int(11) NOT NULL,
   PRIMARY KEY (`mcse_id`))
ENGINE=InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
