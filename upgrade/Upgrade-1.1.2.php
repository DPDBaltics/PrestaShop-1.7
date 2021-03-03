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

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Factory\TabFactory;
use Invertus\dpdBaltics\Install\Installer;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Service\Product\ProductService;
use Invertus\psModuleTabs\Object\Tab;
use Invertus\psModuleTabs\Object\TabsCollection;
use Invertus\psModuleTabs\Service\TabsInitializer;
use Invertus\psModuleTabs\Service\TabsInstaller;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @return bool
 *
 * @throws PrestaShopDatabaseException
 * @throws PrestaShopException
 */
function upgrade_module_1_1_2(DPDBaltics $module)
{
    /** @var CurrentCountryProvider $currentCountryProvider */
    $currentCountryProvider = $this->module->getModuleContainer(CurrentCountryProvider::class);
    $newCountryIsoCode = $currentCountryProvider->getCurrentCountryIsoCode();

    /** @var ProductRepository $productRepository */
    $productRepository = $module->getModuleContainer(ProductRepository::class);
    /** @var ProductService $productService */
    $productService = $module->getModuleContainer(ProductService::class);
    $productId = $productRepository->getProductIdByProductReference(Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD);
    if ($newCountryIsoCode === Config::LATVIA_ISO_CODE) {
        $productService->deleteProduct(Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD);
    } elseif (!$productId) {
        $productService->addProduct(Config::PRODUCT_TYPE_SATURDAY_DELIVERY_COD);
    }

    return true;
}
