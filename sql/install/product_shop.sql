CREATE TABLE IF NOT EXISTS `PREFIX_dpd_product_shop` (
    `id_dpd_product` INT(11) UNSIGNED AUTO_INCREMENT,
    `id_shop` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_dpd_product`, `id_shop`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;