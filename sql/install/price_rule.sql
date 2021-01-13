CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule` (
  `id_dpd_price_rule` INT(11) UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `order_price_from` DECIMAL(20,6) NOT NULL,
  `order_price_to` DECIMAL(20,6) NOT NULL,
  `weight_from` DECIMAL(20,6) NOT NULL,
  `weight_to` DECIMAL(20,6) NOT NULL,
  `price` DECIMAL(20,6) NOT NULL,
  `position` INT(11) NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `customer_type` ENUM('all', 'individual', 'company') NOT NULL default 'all',
  PRIMARY KEY (`id_dpd_price_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_shop` (
  `id_dpd_price_rule` INT(11) UNSIGNED NOT NULL,
  `id_shop` INT(11) NOT NULL,
  `all_shops` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_dpd_price_rule`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_carrier` (
  `id_dpd_price_rule` INT(11) UNSIGNED AUTO_INCREMENT,
  `id_reference` INT(11) UNSIGNED NOT NULL,
  `all_carriers` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_dpd_price_rule`, `id_reference`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_zone` (
  `id_dpd_price_rule` INT(11) UNSIGNED AUTO_INCREMENT,
  `id_dpd_zone` INT(11) UNSIGNED NOT NULL,
  `all_zones` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_dpd_price_rule`, `id_dpd_zone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_payment` (
  `id_dpd_price_rule` INT(11) UNSIGNED AUTO_INCREMENT,
  `id_module` INT(11) NOT NULL,
  `all_payments` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_dpd_price_rule`, `id_module`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
