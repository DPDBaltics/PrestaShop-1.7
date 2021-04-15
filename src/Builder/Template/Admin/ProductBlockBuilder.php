<?php

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
use Invertus\dpdBaltics\Util\ProductUtility;
use Language;
use Shop;
use Smarty;

class ProductBlockBuilder
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
                    'hasAvailability' => ProductUtility::hasAvailability($product->getProductReference()),
                    'isActive' => $product->getActive(),
                    'isCod' => $product->getIsCod(),
                    'isMultiShop' => $isMultiShop,
                    'isOddRow' => $isOddRow
                ]
            );

            if ($product->getIsHomeCollection()) {
                $homeCollectionProducts[] = $product;

                continue;
            }

            $carrier = Carrier::getCarrierByReference($product->id_reference);

            if (!$carrier) {
                continue;
            }

            $delays = [];
            foreach ($carrier->delay as $key => $item) {
                $delays[Language::getIsoById($key)] = $item;
            }

            $this->smarty->assign(
                [
                    'carrierName' => $carrier->name,
                    'selectedLanguage' => $this->language->iso_code,
                    'zoneBlock' => $this->getProductSearchBoxZones($product),
                    'availabilityBlock' => $this->getProductSearchBoxZones($product),
                    'shopBlock' => $this->getProductSearchBoxShops($product),
                    'languages' => $languages,
                    'delays' => $delays,
                    'additionalProducts' => [],
                ]
            );
            $rows[] =
                $this->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/product/row.tpl');
        }

        if (!empty($homeCollectionProducts)) {
            $this->smarty->assign(
                [
                    'activeHomeColService' => true,
                    'homeCollectionProducts' => $homeCollectionProducts,
                ]
            );

            $rows[] = $this->smarty->fetch(
                $this->module->getLocalPath() . 'views/templates/admin/product/row-home-collection.tpl'
            );
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
