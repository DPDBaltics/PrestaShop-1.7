CREATE TABLE IF NOT EXISTS `PREFIX_dpd_product_availability` (
  `id_dpd_product_availability` INT(11) UNSIGNED AUTO_INCREMENT,
  `product_reference` varchar(64) NOT NULL,
  `interval_start` varchar(64) NOT NULL,
  `interval_end` varchar(64) NOT NULL,
  `day` varchar(64) NOT NULL,
  PRIMARY KEY (`id_dpd_product_availability`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
