<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 *
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_3_2_22(DPDBaltics $module)
{
    $sql = [];
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_shop` ADD INDEX(`parcel_shop_id`)';
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_shop_work_hours` ADD INDEX(`parcel_shop_id`)';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_zone_range` ADD INDEX(`id_dpd_zone`)';
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_zone_range` ADD INDEX(`id_country`)';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_pudo_cart` ADD INDEX(`id_cart`)';
    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_pudo_cart` ADD INDEX(`id_carrier`)';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_order_phone` ADD INDEX(`id_cart`)';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) == false) {
            return false;
        }
    }

    return true;
}

