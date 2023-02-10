CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_zone` (
    `id_dpd_price_rule` INT(11) UNSIGNED AUTO_INCREMENT,
    `id_dpd_zone` INT(11) UNSIGNED NOT NULL,
    `all_zones` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id_dpd_price_rule`, `id_dpd_zone`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;