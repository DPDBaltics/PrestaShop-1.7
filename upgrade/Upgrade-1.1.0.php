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

use Invertus\dpdBaltics\Collection\DPDProductInstallCollection;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\dpdBaltics\Service\Carrier\CreateCarrierService;
use Invertus\dpdBaltics\Service\Product\ProductService;
use Invertus\psModuleTabs\Object\TabsCollection;
use Invertus\psModuleTabs\Service\TabsInitializer;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 *
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_1_1_0(DPDBaltics $module)
{
    $sql = [];

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_pudo_cart` ADD COLUMN `city` varchar(64) AFTER `country_code`';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_pudo_cart` ADD COLUMN `street` varchar(64) AFTER `city`';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_shipment` ADD COLUMN `is_document_return_enabled` tinyint(1) AFTER `return_pl_number`';

    $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'dpd_shipment` ADD COLUMN `document_return_number` varchar(255) AFTER `return_pl_number`';

    foreach ($sql as $query) {
        try {
            if (Db::getInstance()->execute($query) == false) {
                continue;
            }
        } catch (PrestaShopDatabaseException $e) {
            continue;
        }
    }
    $sql = [];

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dpd_courier_request` (
      `id_dpd_courier_request` INT(11) UNSIGNED AUTO_INCREMENT,
      `shipment_date` DATETIME NOT NULL,
      `sender_name` VARCHAR(255) NOT NULL,
      `sender_phone_code` VARCHAR(255) NOT NULL,
      `sender_phone` VARCHAR(255) NOT NULL,
      `sender_id_ws_country` INT(11) NOT NULL,
      `country` VARCHAR(255) NOT NULL,
      `sender_postal_code` VARCHAR(255) NOT NULL,
      `sender_city` VARCHAR(255) NOT NULL,
      `sender_address` VARCHAR(255) NOT NULL,
      `sender_additional_information` VARCHAR(255) NOT NULL,
      `order_nr` VARCHAR(255) NOT NULL DEFAULT \'\',
      `pick_up_time` VARCHAR(255) NOT NULL,
      `sender_work_until` VARCHAR(255) NOT NULL,
      `weight` FLOAT(11) UNSIGNED NOT NULL,
      `parcels_count` INT(11) NOT NULL,
      `pallets_count` INT(11) NOT NULL,
      `comment_for_courier` VARCHAR(255) NOT NULL,
      `id_shop` INT(11) UNSIGNED NOT NULL,
      `date_add` DATETIME,
      `date_upd` DATETIME,
      PRIMARY KEY (`id_dpd_courier_request`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dpd_shop` (
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
    ';

    $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dpd_shop_work_hours` (
      `id_dpd_shop_work_hours` INT(11) UNSIGNED AUTO_INCREMENT,
      `parcel_shop_id` varchar(64) NOT NULL,
      `week_day` varchar(64) NOT NULL,
      `open_morning` varchar(64) NOT NULL,
      `close_morning` varchar(64) NOT NULL,
      `open_afternoon` varchar(64) NOT NULL,
      `close_afternoon` varchar(64) NOT NULL,
      PRIMARY KEY (`id_dpd_shop_work_hours`)
    ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
    ';

    foreach ($sql as $query) {
        if (Db::getInstance()->execute($query) == false) {
            return false;
        }
    }

    Configuration::updateValue(
        Config::PARCEL_SHOP_DISPLAY,
        Config::PARCEL_SHOP_DISPLAY_LIST
    );

    /** @var CreateCarrierService $carrierCreateService */
    /** @var ProductService $productService */
    $carrierCreateService = $module->getModuleContainer()->get('invertus.dpdbaltics.service.carrier.create_carrier_service');
    $productService = $module->getModuleContainer()->get('invertus.dpdbaltics.service.product.product_service');
    /** @var CurrentCountryProvider $currentCountryProvider */
    $currentCountryProvider = $this->module->getModuleContainer('invertus.dpdbaltics.provider.current_country_provider');
    $countryCode = $currentCountryProvider->getCurrentCountryIsoCode();

    $productService->updateCarriersOnCountryChange($countryCode);

    $collection = new DPDProductInstallCollection();
    $product = Config::getProductByReference(Config::PRODUCT_TYPE_SATURDAY_DELIVERY);
    $collection->add($product);

    $product = Config::getProductByReference(Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD);
    $collection->add($product);

    try {
        $result = $carrierCreateService->createCarriers($collection);
    } catch (Exception $e) {

        $result = false;
    }

    return $result;
}
