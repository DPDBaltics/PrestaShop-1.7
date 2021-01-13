CREATE TABLE IF NOT EXISTS `PREFIX_dpd_address_template` (
  `id_dpd_address_template` INT(11) UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL DEFAULT '',
  `mobile_phone` VARCHAR(255) NOT NULL,
  `mobile_phone_code` VARCHAR(255) NOT NULL,
  `dpd_country_id` INT(11) NOT NULL DEFAULT 0,
  `email` VARCHAR(255) NOT NULL DEFAULT '',
  `zip_code` VARCHAR(255) NOT NULL DEFAULT '',
  `dpd_city_name` VARCHAR(255) NOT NULL DEFAULT '',
  `address` VARCHAR(255) NOT NULL,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_dpd_address_template`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_dpd_address_template_shop` (
  `id_dpd_address_template` INT(11) UNSIGNED NOT NULL,
  `id_shop` INT(11) NOT NULL,
   `all_shops` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_dpd_address_template`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;