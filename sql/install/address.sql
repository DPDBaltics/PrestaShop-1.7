CREATE TABLE IF NOT EXISTS `PREFIX_dpd_receiver_address` (
  `id_dpd_receiver_address` INT(11) UNSIGNED AUTO_INCREMENT,
  `id_order` INT(10) UNSIGNED NOT NULL,
  `id_origin_address` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_dpd_receiver_address`),
  UNIQUE (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;