CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_shop` (
    `id_dpd_price_rule` INT(11) UNSIGNED NOT NULL,
    `id_shop` INT(11) NOT NULL,
    `all_shops` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id_dpd_price_rule`, `id_shop`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;