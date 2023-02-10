CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_carrier` (
    `id_dpd_price_rule` INT(11) UNSIGNED AUTO_INCREMENT,
    `id_reference` INT(11) UNSIGNED NOT NULL,
    `all_carriers` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id_dpd_price_rule`, `id_reference`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;