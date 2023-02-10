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
