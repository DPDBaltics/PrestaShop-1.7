CREATE TABLE IF NOT EXISTS `PREFIX_dpd_price_rule_payment` (
    `id_dpd_price_rule` INT(11) UNSIGNED AUTO_INCREMENT,
    `id_module` INT(11) NOT NULL,
    `all_payments` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id_dpd_price_rule`, `id_module`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;