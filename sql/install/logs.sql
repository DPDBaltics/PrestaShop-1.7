CREATE TABLE IF NOT EXISTS `PREFIX_dpd_log` (
  `id_dpd_log` INT(11) UNSIGNED AUTO_INCREMENT,
  `request` TEXT,
  `response` TEXT,
  `status` TEXT,
  `date_add` DATETIME,
  PRIMARY KEY (`id_dpd_log`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;