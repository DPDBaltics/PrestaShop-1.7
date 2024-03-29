CREATE TABLE IF NOT EXISTS `PREFIX_dpd_product` (
  `id_dpd_product` INT(11) UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `product_reference` VARCHAR(255) NOT NULL,
  `id_reference` INT(11) UNSIGNED NOT NULL,
  `active` TINYINT(1) NOT NULL,
  `is_pudo` TINYINT(1) NOT NULL,
  `is_cod` TINYINT(1) NOT NULL,
  `is_home_collection` TINYINT(1) NOT NULL,
  `all_zones` TINYINT(1) NOT NULL,
  `all_shops` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_dpd_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

