CREATE TABLE IF NOT EXISTS `PREFIX_dpd_zone` (
  `id_dpd_zone` INT(11) UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `is_custom` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_dpd_zone`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
