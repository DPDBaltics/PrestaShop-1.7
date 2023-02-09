CREATE TABLE IF NOT EXISTS `PREFIX_dpd_product_zone` (
    `id_dpd_product` INT(11) UNSIGNED AUTO_INCREMENT,
    `id_dpd_zone` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_dpd_product`, `id_dpd_zone`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;