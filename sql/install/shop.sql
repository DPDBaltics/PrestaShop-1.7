CREATE TABLE IF NOT EXISTS `PREFIX_dpd_shop` (
  `id_dpd_shop` INT(11) UNSIGNED AUTO_INCREMENT,
  `parcel_shop_id` varchar(64) NOT NULL,
  `company` varchar(64) NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(64) NOT NULL,
  `p_code` VARCHAR(12) NOT NULL,
  `street` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `longitude` decimal(20,6) NOT NULL,
  `latitude` decimal(20,6) NOT NULL,
  PRIMARY KEY (`id_dpd_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_dpd_shop_work_hours` (
  `id_dpd_shop_work_hours` INT(11) UNSIGNED AUTO_INCREMENT,
  `parcel_shop_id` varchar(64) NOT NULL,
  `week_day` varchar(64) NOT NULL,
  `open_morning` varchar(64) NOT NULL,
  `close_morning` varchar(64) NOT NULL,
  `open_afternoon` varchar(64) NOT NULL,
  `close_afternoon` varchar(64) NOT NULL,
  PRIMARY KEY (`id_dpd_shop_work_hours`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

