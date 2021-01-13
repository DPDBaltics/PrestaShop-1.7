CREATE TABLE IF NOT EXISTS `PREFIX_dpd_shipment_error` (
  `id_dpd_shipment_error` INT(11) UNSIGNED AUTO_INCREMENT,
  `id_shipment` INT(11),
  `error` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_dpd_shipment_error`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
