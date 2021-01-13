CREATE TABLE IF NOT EXISTS `PREFIX_dpd_order_delivery_time` (
  `id_dpd_order_delivery_time` INT(11) UNSIGNED AUTO_INCREMENT,
  `id_cart` INT(11) UNSIGNED,
  `delivery_time` VARCHAR(64),
  PRIMARY KEY (`id_dpd_order_delivery_time`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
