CREATE TABLE IF NOT EXISTS `PREFIX_dpd_collection_request` (
  `id_dpd_collection_request` INT(11) UNSIGNED AUTO_INCREMENT,
  `shipment_date` DATETIME NOT NULL,
  `pickup_address_full_name` VARCHAR(35) NOT NULL DEFAULT '',
  `pickup_address_mobile_phone` VARCHAR(255) NOT NULL,
  `pickup_address_mobile_phone_code` VARCHAR(255) NOT NULL,
  `pickup_address_id_ws_country` INT(11) UNSIGNED NOT NULL,
  `pickup_address_email` VARCHAR(255) NOT NULL,
  `pickup_address_zip_code` VARCHAR(255) NOT NULL,
  `pickup_address_city` VARCHAR(255) NOT NULL,
  `pickup_address_address` VARCHAR(255) NOT NULL,
  `receiver_address_full_name` VARCHAR(255) NOT NULL DEFAULT '',
  `receiver_address_mobile_phone` VARCHAR(255) NOT NULL,
  `receiver_address_mobile_phone_code` VARCHAR(11) NOT NULL,
  `receiver_address_id_ws_country` INT(11) UNSIGNED NOT NULL,
  `receiver_address_email` VARCHAR(255) NOT NULL,
  `receiver_address_zip_code` VARCHAR(255) NOT NULL,
  `receiver_address_city` VARCHAR(255) NOT NULL,
  `receiver_address_address` VARCHAR(255) NOT NULL,
  `info1` VARCHAR(255) NOT NULL,
  `info2` VARCHAR(255) NOT NULL,
  `id_shop` INT(11) UNSIGNED NOT NULL,
  `date_add` DATETIME,
  `date_upd` DATETIME,
  PRIMARY KEY (`id_dpd_collection_request`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;