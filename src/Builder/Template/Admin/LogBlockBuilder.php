<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */


namespace Invertus\dpdBaltics\Builder\Template\Admin;

use Carrier;
use Configuration;
use DPDBaltics;
use DPDProduct;
use Invertus\dpdBaltics\Builder\Template\SearchBoxBuilder;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\ProductShopRepository;
use Invertus\dpdBaltics\Repository\ProductZoneRepository;
use Invertus\dpdBaltics\Service\LanguageService;
use Language;
use Shop;
use Smarty;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LogBlockBuilder
{
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductZoneRepository
     */
    private $productZoneRepository;

    /**
     * @var ProductShopRepository
     */
    private $productShopRepository;

    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * @var SearchBoxBuilder
     */
    private $searchBoxBuilder;

    public function __construct(
        DPDBaltics $module,
        Smarty $smarty,
        Language $language,
        ProductRepository $productRepository,
        ProductZoneRepository $productZoneRepository,
        ProductShopRepository $productShopRepository,
        LanguageService $languageService,
        SearchBoxBuilder $searchBoxBuilder
    ) {
        $this->module = $module;
        $this->smarty = $smarty;
        $this->language = $language;
        $this->productRepository = $productRepository;
        $this->productZoneRepository = $productZoneRepository;
        $this->productShopRepository = $productShopRepository;
        $this->languageService = $languageService;
        $this->searchBoxBuilder = $searchBoxBuilder;
    }

    public function renderProducts()
    {
        $languages = $this->languageService->getShopLanguagesIsoCodes();

        $products = $this->productRepository->getAllProducts();
        if (empty($products)) {
            return $this->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/product/no-products.tpl');
        }

        $isOddRow = false;
        $rows = [];

        $shops = Shop::getShops(true);
        $isMultiShop = (bool) Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && (count($shops) > 1);

        /** @var $product DPDProduct */
        foreach ($products as $product) {
            $isOddRow = !$isOddRow;

            $this->smarty->assign(
                [
                    'productName' => $product->getName(),
                    'idProduct' => $product->getIdDpdProduct(),
                    'isActive' => $product->getActive(),
                    'isMultiShop' => $isMultiShop,
                    'isOddRow' => $isOddRow
                ]
            );

            $carrier = Carrier::getCarrierByReference($product->id_reference);
            $delays = [];
            foreach ($carrier->delay as $key => $item) {
                $delays[Language::getIsoById($key)] = $item;
            }

            $this->smarty->assign(
                [
                    'carrierName' => $carrier->name,
                    'selectedLanguage' => $this->language->iso_code,
                    'zoneBlock' => $this->getProductSearchBoxZones($product),
                    'shopBlock' => $this->getProductSearchBoxShops($product),
                    'languages' => $languages,
                    'delays' => $delays,
                    'additionalProducts' => [],
                ]
            );
            $rows[] =
                $this->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/product/row.tpl');
        }

        $this->smarty->assign(
            [
                'rows' => $rows,
                'isMultiShop' => $isMultiShop
            ]
        );

        return $this->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/product/index.tpl');
    }

    /**
     * @param DPDProduct $product
     * @return string
     */
    private function getProductSearchBoxZones(DPDProduct $product)
    {
        $idProduct = $product->getIdDpdProduct();

        $allZonesSelected = $product->getIsAllZones();

        $zones = $this->productZoneRepository->getProductSelectedZones($idProduct);

        $searchBoxPlugin = $this->searchBoxBuilder->createSearchBox(
            $zones,
            $allZonesSelected,
            'zones_select[]',
            true
        );

        return $searchBoxPlugin->render();
    }

    /**
     * @param DPDProduct $product
     *
     * @return string
     */
    private function getProductSearchBoxShops(DPDProduct $product)
    {
        $idProduct = $product->getIdDpdProduct();

        $allShopsSelected = $product->getIsAllShops();

        $shops = $this->productShopRepository->getProductShops($idProduct);

        $searchBoxPlugin = $this->searchBoxBuilder->createSearchBox(
            $shops,
            $allShopsSelected,
            'shops_select[]',
            true
        );

        return $searchBoxPlugin->render();
    }
}
