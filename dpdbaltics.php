<?php

use Invertus\dpdBaltics\Grid\Row\PrintAccessibilityChecker;
use Invertus\dpdBaltics\Builder\Template\Front\CarrierOptionsBuilder;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\ConsoleCommand\UpdateParcelShopsCommand;
use Invertus\dpdBaltics\Controller\AbstractAdminController;
use Invertus\dpdBaltics\Grid\LinkRowActionCustom;
use Invertus\dpdBaltics\Grid\SubmitBulkActionCustom;
use Invertus\dpdBaltics\Install\Installer;
use Invertus\dpdBaltics\Logger\Logger;
use Invertus\dpdBaltics\OnBoard\Service\OnBoardService;
use Invertus\dpdBaltics\Repository\AddressTemplateRepository;
use Invertus\dpdBaltics\Repository\OrderRepository;
use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBaltics\Repository\PhonePrefixRepository;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\PudoRepository;
use Invertus\dpdBaltics\Repository\ReceiverAddressRepository;
use Invertus\dpdBaltics\Repository\ShipmentRepository;
use Invertus\dpdBaltics\Repository\ZoneRepository;
use Invertus\dpdBaltics\Service\API\LabelApiService;
use Invertus\dpdBaltics\Service\API\ParcelShopSearchApiService;
use Invertus\dpdBaltics\Service\CarrierPhoneService;
use Invertus\dpdBaltics\Service\Exception\ExceptionService;
use Invertus\dpdBaltics\Service\GoogleApiService;
use Invertus\dpdBaltics\Service\Label\LabelPositionService;
use Invertus\dpdBaltics\Service\OrderService;
use Invertus\dpdBaltics\Service\Parcel\ParcelShopService;
use Invertus\dpdBaltics\Service\Payment\PaymentService;
use Invertus\dpdBaltics\Service\PriceRuleService;
use Invertus\dpdBaltics\Service\PudoService;
use Invertus\dpdBaltics\Service\ShipmentService;
use Invertus\dpdBaltics\Service\TabService;
use Invertus\dpdBaltics\Service\TrackingService;
use Invertus\dpdBaltics\Util\CountryUtility;
use Invertus\dpdBaltics\Validate\Carrier\PudoValidate;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelPrintResponse;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelShopSearchResponse;
use Invertus\dpdBalticsApi\Exception\DPDBalticsAPIException;
use Invertus\dpdBalticsApi\Factory\SerializerFactory;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DPDBaltics extends CarrierModule
{
    /**
     * Controller for displaying dpd links in prestashop's menu
     */
    const ADMIN_DPDBALTICS_MODULE_CONTROLLER = 'AdminDPDBalticsModule';

    const ADMIN_ZONES_CONTROLLER = 'AdminDPDBalticsZones';
    const ADMIN_PRODUCT_AVAILABILITY_CONTROLLER = 'AdminDPDBalticsProductsAvailability';
    const ADMIN_PRODUCTS_CONTROLLER = 'AdminDPDBalticsProducts';
    const ADMIN_SETTINGS_CONTROLLER = 'AdminDPDBalticsSettings';
    const ADMIN_SHIPMENT_SETTINGS_CONTROLLER = 'AdminDPDBalticsShipmentSettings';
    const ADMIN_IMPORT_EXPORT_CONTROLLER = 'AdminDPDBalticsImportExport';
    const ADMIN_PRICE_RULES_CONTROLLER = 'AdminDPDBalticsPriceRules';
    const ADMIN_ADDRESS_TEMPLATE_CONTROLLER = 'AdminDPDBalticsAddressTemplate';
    const ADMIN_AJAX_CONTROLLER = 'AdminDPDBalticsAjax';
    const ADMIN_PUDO_AJAX_CONTROLLER = 'AdminDPDBalticsPudoAjax';
    const ADMIN_REQUEST_SUPPORT_CONTROLLER = 'AdminDPDBalticsRequestSupport';
    const ADMIN_AJAX_SHIPMENTS_CONTROLLER = 'AdminDPDBalticsAjaxShipments';
    const ADMIN_LOGS_CONTROLLER = 'AdminDPDBalticsLogs';
    const ADMIN_SHIPMENT_CONTROLLER = 'AdminDPDBalticsShipment';
    const ADMIN_ORDER_RETURN_CONTROLLER = 'AdminDPDBalticsOrderReturn';
    const ADMIN_COLLECTION_REQUEST_CONTROLLER = 'AdminDPDBalticsCollectionRequest';
    const ADMIN_COURIER_REQUEST_CONTROLLER = 'AdminDPDBalticsCourierRequest';
    const ADMIN_AJAX_ON_BOARD_CONTROLLER = 'AdminDPDAjaxOnBoard';

    const DISABLE_CACHE = true;

    /**
     * Symfony DI Container
     **/
    private $moduleContainer;

    /**
     * Prestashop fills this property automatically with selected carrier ID in FO checkout
     *
     * @var int $id_carrier
     */
    public $id_carrier;


    public function __construct()
    {
        $this->name = 'dpdbaltics';
        $this->displayName = $this->l('DPDBaltics');
        $this->author = 'Invertus';
        $this->tab = 'shipping_logistics';
        $this->version = '3.1.6';
        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];
        $this->need_instance = 0;
        parent::__construct();

        $this->autoLoad();
        $this->compile();
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        /** @var Installer $installer */
        $installer = $this->getModuleContainer()->get(Installer::class);
        if (!$installer->install()) {
            $this->_errors += $installer->getErrors();
            $this->uninstall();

            return false;
        }

        return true;
    }

    public function uninstall()
    {
        /** @var Installer $installer */
        $installer = $this->moduleContainer->get(Installer::class);
        if (!$installer->uninstall()) {
            $this->_errors += $installer->getErrors();
            return false;
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getTabs()
    {
        /** @var TabService $tabsService */
        $tabsService = $this->getModuleContainer()->get(TabService::class);

        return $tabsService->getTabs();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink(self::ADMIN_SETTINGS_CONTROLLER));
    }

    /**
     * @return mixed
     */
    public function getModuleContainer($id = false)
    {
        if ($id) {
            return $this->moduleContainer->get($id);
        }

        return $this->moduleContainer;
    }

    public function hookActionFrontControllerSetMedia()
    {
        $currentController = $this->context->controller->php_self !== null ?
            $this->context->controller->php_self :
            Tools::getValue('controller');

        if ('order' === $currentController) {
            $this->context->controller->addJS($this->getPathUri() . 'views/js/front/order.js');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/front/order-input.js');
            $this->context->controller->addCSS($this->getPathUri() . 'views/css/front/order-input.css');
            /** @var PaymentService $paymentService */
            $paymentService = $this->getModuleContainer(PaymentService::class);

            $cart = Context::getContext()->cart;
            $paymentService->filterPaymentMethods($cart);
            $paymentService->filterPaymentMethodsByCod($cart);
        }

        if ('product' === $currentController) {
            $this->context->controller->registerStylesheet(
                'dpdbaltics-product-carriers.',
                'modules/' . $this->name . '/views/css/front/product-carriers.css',
                [
                    'media' => 'all',
                    'position' => 150
                ]
            );
        }

        if (in_array($currentController, ['order', 'order-opc', 'ShipmentReturn'])) {
            /** @var ProductRepository $productRepo */
            $productRepo = $this->getModuleContainer(ProductRepository::class);
            Media::addJsDef([
                'pudoCarriers' => Tools::jsonEncode($productRepo->getPudoProducts()),
                'currentController' => $currentController,
                'id_language' => $this->context->language->id,
                'id_shop' => $this->context->shop->id,
                'dpdAjaxLoaderPath' => $this->getPathUri() . 'views/img/ajax-loader-big.gif',
                'dpdPickupMarkerPath' => $this->getPathUri() . 'views/img/dpd-pick-up.png',
                'dpdLockerMarkerPath' => $this->getPathUri() . 'views/img/locker.png',
                'dpdHookAjaxUrl' => $this->context->link->getModuleLink($this->name, 'Ajax'),
                'pudoSelectSuccess' => $this->l('Pick-up point selected'),
            ]);

            $this->context->controller->registerStylesheet(
                'dpdbaltics-pudo-shipment',
                'modules/' . $this->name . '/views/css/front/' . 'pudo-shipment.css',
                [
                    'media' => 'all',
                    'position' => 150
                ]
            );
            if (Configuration::get(\Invertus\dpdBaltics\Config\Config::PICKUP_MAP)) {
                /** @var GoogleApiService $googleApiService */
                $googleApiService = $this->getModuleContainer(GoogleApiService::class);
                $this->context->controller->registerJavascript(
                    'dpdbaltics-google-api',
                    $googleApiService->getFormattedGoogleMapsUrl(), [
                        'server' => 'remote'
                    ]
                );
            }
            $this->context->controller->registerJavascript(
                'dpdbaltics-pudo',
                'modules/' . $this->name . '/views/js/front/pudo.js',
                [
                    'position' => 'bottom',
                    'priority' => 130
                ]
            );
            $this->context->controller->registerJavascript(
                'dpdbaltics-pudo-search',
                'modules/' . $this->name . '/views/js/front/pudo-search.js',
                [
                    'position' => 'bottom',
                    'priority' => 130
                ]
            );
        }
    }

    public function hookActionValidateStepComplete(&$params)
    {
        if ('delivery' !== $params['step_name']) {
            return;
        }

        /** @var Cart $cart */
        $cart = $params['cart'];
        $carrier = new Carrier($cart->id_carrier);
        $idShop = $this->context->shop->id;

        /** @var Invertus\dpdBaltics\Repository\CarrierRepository $carrierRepo */
        /** @var Invertus\dpdBaltics\Repository\ProductRepository $productRepo */
        $carrierRepo = $this->getModuleContainer()->get(Invertus\dpdBaltics\Repository\CarrierRepository::class);
        $productRepo = $this->getModuleContainer()->get(Invertus\dpdBaltics\Repository\ProductRepository::class);

        $carrierReference = $carrier->id_reference;
        $dpdCarriers = $carrierRepo->getDpdCarriers($idShop);
        $isDpdCarrier = false;
        foreach ($dpdCarriers as $dpdCarrier) {
            if ($carrierReference == $dpdCarrier['id_reference']) {
                $isDpdCarrier = true;
                $productId = $productRepo->getProductIdByCarrierReference($carrier->id_reference);
                $product = new DPDProduct($productId);
                $isSameDayDelivery = $product->product_reference === Config::PRODUCT_TYPE_SAME_DAY_DELIVERY;
                break;
            }
        }
        if (!$isDpdCarrier) {
            return true;
        }

        if ($isSameDayDelivery) {
            /** @var PudoRepository $pudoRepo */
            $pudoRepo = $this->getModuleContainer(PudoRepository::class);
            $pudoId = $pudoRepo->getIdByCart($cart->id);
            $selectedPudo = new DPDPudo($pudoId);
            if ($selectedPudo->city !== Config::SAME_DAY_DELIVERY_CITY) {
                $this->context->controller->errors[] =
                    $this->l('This carrier can\'t deliver to your selected city');
                $params['completed'] = false;
                $selectedPudo->delete();

                return;
            }
        }
        if (!Tools::getValue('dpd-phone')) {
            $this->context->controller->errors[] =
                $this->l('In order to use DPD Carrier you need to enter phone number');
            $params['completed'] = false;

            return;
        }

        if (!Tools::getValue('dpd-phone-area')) {
            $this->context->controller->errors[] =
                $this->l('In order to use DPD Carrier you need to enter phone area');
            $params['completed'] = false;

            return;
        }

        /** @var CarrierPhoneService $carrierPhoneService */
        $carrierPhoneService = $this->getModuleContainer()->get(CarrierPhoneService::class);

        if (!$carrierPhoneService->saveCarrierPhone(
            $this->context->cart->id,
            Tools::getValue('dpd-phone'),
            Tools::getValue('dpd-phone-area')
        )
        ) {
            $this->context->controller->errors[] = $this->l('Phone data is not saved');
            $params['completed'] = false;
        };

        /** @var \Invertus\dpdBaltics\Service\OrderDeliveryTimeService $orderDeliveryService */
        $orderDeliveryService = $this->getModuleContainer()->get(\Invertus\dpdBaltics\Service\OrderDeliveryTimeService::class);

        $deliveryTime = Tools::getValue('dpd-delivery-time');
        if ($deliveryTime) {
            if (!$orderDeliveryService->saveDeliveryTime(
                $this->context->cart->id,
                $deliveryTime
            )) {
                $this->context->controller->errors[] = $this->l('Delivery time data is not saved');
                $params['completed'] = false;
            };
        }

        /** @var Cart $cart */
        $cart = $params['cart'];
        $carrier = new Carrier($cart->id_carrier);
        /** @var PudoValidate $pudoValidator */
        $pudoValidator = $this->getModuleContainer(PudoValidate::class);
        if (!$pudoValidator->validatePickupPoints($cart->id, $carrier->id)) {
            $carrier = new Carrier($cart->id_carrier, $this->context->language->id);
            $this->context->controller->errors[] =
                sprintf($this->l('Please select pickup point for carrier: %s.'), $carrier->name);

            $params['completed'] = false;
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if ($params['type'] == 'after_price') {
            /** @var CarrierOptionsBuilder $carrierOptionsBuilder */
            $carrierOptionsBuilder = $this->getModuleContainer()->get(CarrierOptionsBuilder::class);

            return $carrierOptionsBuilder->renderCarrierOptionsInProductPage();
        }
    }

    public function hookDisplayBackOfficeTop()
    {
        if ($this->context->controller instanceof AbstractAdminController &&
            Configuration::get(Config::ON_BOARD_TURNED_ON) &&
            Configuration::get(Config::ON_BOARD_STEP)
        ) {
            /** @var OnBoardService $onBoardService */
            $onBoardService = $this->getModuleContainer(OnBoardService::class);
            return $onBoardService->makeStepActionWithTemplateReturn();
        }
    }

    public function getOrderShippingCost($cart, $shippingCost)
    {
        return $this->getOrderShippingCostExternal($cart);
    }

    /**
     * @param $cart Cart
     * @return bool|float|string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function getOrderShippingCostExternal($cart)
    {
        // This method is still called when module is disabled so we need to do a manual check here
        if (!$this->active) {
            return false;
        }

        $carrier = new Carrier($this->id_carrier);
        if ($this->context->controller->ajax && Tools::getValue('id_address_delivery')) {
            $cart->id_address_delivery = (int)Tools::getValue('id_address_delivery');
        }

        $deliveryAddress = new Address($cart->id_address_delivery);

        /** @var ProductRepository $productRepo */
        /** @var ZoneRepository $zoneRepo */
        /** @var \Invertus\dpdBaltics\Service\Product\ProductAvailabilityService $productAvailabilityService */
        /** @var \Invertus\dpdBaltics\Validate\Weight\CartWeightValidator $cartWeightValidator */
        /** @var \Invertus\dpdBaltics\Provider\CurrentCountryProvider $currentCountryProvider */
        $productRepo = $this->getModuleContainer()->get(ProductRepository::class);
        $zoneRepo = $this->getModuleContainer()->get(ZoneRepository::class);
        $productAvailabilityService = $this->getModuleContainer(\Invertus\dpdBaltics\Service\Product\ProductAvailabilityService::class);
        $cartWeightValidator = $this->getModuleContainer(\Invertus\dpdBaltics\Validate\Weight\CartWeightValidator::class);
        $currentCountryProvider = $this->getModuleContainer(\Invertus\dpdBaltics\Provider\CurrentCountryProvider::class);
        $countryCode = $currentCountryProvider->getCurrentCountryIsoCode($cart);

        if (!$productAvailabilityService->checkIfCarrierIsAvailable($carrier->id_reference)) {
            return false;
        }

        try {
            $carrierZones = $zoneRepo->findZonesIdsByCarrierReference($carrier->id_reference);
            $isShopAvailable = $productRepo->checkIfCarrierIsAvailableInShop($carrier->id_reference, $this->context->shop->id);
            $serviceCarrier = $productRepo->findProductByCarrierReference($carrier->id_reference);
        } catch (Exception $e) {
            $tplVars = [
                'errorMessage' => $this->l('Something went wrong while collecting DPD carrier data'),
            ];
            $this->context->smarty->assign($tplVars);

            return $this->context->smarty->fetch(
                $this->getLocalPath() . 'views/templates/admin/dpd-shipment-fatal-error.tpl'
            );
        }

        $parcelDistribution = \Configuration::get(Config::PARCEL_DISTRIBUTION);
        $maxAllowedWeight = Config::getDefaultServiceWeights($countryCode, $serviceCarrier['product_reference']);

        if (!$cartWeightValidator->validate($cart, $parcelDistribution ,$maxAllowedWeight)) {
            return false;
        }

        if ((bool)$serviceCarrier['is_home_collection']) {
            return false;
        }

        if (!DPDZone::checkAddressInZones($deliveryAddress, $carrierZones)) {
            return false;
        }

        if (empty($isShopAvailable)) {
            return false;
        }

        if ($serviceCarrier['product_reference'] === Config::PRODUCT_TYPE_SAME_DAY_DELIVERY) {
            $isSameDayAvailable = \Invertus\dpdBaltics\Util\ProductUtility::validateSameDayDelivery(
                $countryCode,
                $deliveryAddress->city
            );

            if (!$isSameDayAvailable) {
                return false;
            }
        }

        /** @var PriceRuleRepository $priceRuleRepository */
        $priceRuleRepository = $this->getModuleContainer()->get(PriceRuleRepository::class);

        // Get all price rules for current carrier
        $priceRulesIds =
            $priceRuleRepository->getByCarrierReference(
                $deliveryAddress,
                $carrier->id_reference
            );
        /** @var PriceRuleService $priceRuleService */
        $priceRuleService = $this->getModuleContainer()->get(PriceRuleService::class);

        return $priceRuleService->applyPriceRuleForCarrier($cart, $priceRulesIds, $this->context->shop->id);
    }

    public function hookDisplayCarrierExtraContent($params)
    {
        /** @var Cart $cart */
        $cart = $params['cart'];
        $carrier = new Carrier($params['carrier']['id']);

        /** @var \Invertus\dpdBaltics\Provider\CurrentCountryProvider $currentCountryProvider */
        $currentCountryProvider = $this->getModuleContainer(\Invertus\dpdBaltics\Provider\CurrentCountryProvider::class);
        $countryCode = $currentCountryProvider->getCurrentCountryIsoCode($cart);

        $deliveryAddress = new Address($cart->id_address_delivery);

        /** @var CarrierPhoneService $carrierPhoneService */
        /** @var \Invertus\dpdBaltics\Presenter\DeliveryTimePresenter $deliveryTimePresenter */
        /** @var ProductRepository $productRepo */
        $carrierPhoneService = $this->getModuleContainer()->get(CarrierPhoneService::class);
        $deliveryTimePresenter = $this->getModuleContainer()->get(\Invertus\dpdBaltics\Presenter\DeliveryTimePresenter::class);
        $productRepo = $this->getModuleContainer()->get(ProductRepository::class);

        $productId = $productRepo->getProductIdByCarrierReference($carrier->id_reference);
        $dpdProduct = new DPDProduct($productId);
        $return = '';
        if ($dpdProduct->getProductReference() === Config::PRODUCT_TYPE_SAME_DAY_DELIVERY) {
            /** @var \Invertus\dpdBaltics\Presenter\SameDayDeliveryMessagePresenter $sameDayDeliveryPresenter */
            $sameDayDeliveryPresenter = $this->getModuleContainer()->get(\Invertus\dpdBaltics\Presenter\SameDayDeliveryMessagePresenter::class);
            $return .= $sameDayDeliveryPresenter->getSameDayDeliveryMessageTemplate();
        }
        $return .= $carrierPhoneService->getCarrierPhoneTemplate($this->context->cart->id);
        if ($dpdProduct->getProductReference() === Config::PRODUCT_TYPE_B2B ||
            $dpdProduct->getProductReference() === Config::PRODUCT_TYPE_B2B_COD
        ) {
            $return .= $deliveryTimePresenter->getDeliveryTimeTemplate($countryCode, $deliveryAddress->city);
        }

        /** @var ProductRepository $productRep */
        $productRep = $this->getModuleContainer(ProductRepository::class);
        $isPudo = $productRep->isProductPudo($carrier->id_reference);
        if ($isPudo) {
            /** @var PudoRepository $pudoRepo */
            /** @var ParcelShopRepository $parcelShopRepo */
            /** @var ProductRepository $productRepo */
            $pudoRepo = $this->getModuleContainer(PudoRepository::class);
            $parcelShopRepo = $this->getModuleContainer(ParcelShopRepository::class);
            $productRepo = $this->getModuleContainer(ProductRepository::class);
            $product = $productRepo->findProductByCarrierReference($carrier->id_reference);
            $isSameDayDelivery = ($product['product_reference'] === Config::PRODUCT_TYPE_SAME_DAY_DELIVERY);

            $pudoId = $pudoRepo->getIdByCart($cart->id);
            $selectedPudo = new DPDPudo($pudoId);

            /** @var ParcelShopService $parcelShopService */
            $parcelShopService= $this->getModuleContainer(ParcelShopService::class);

            $selectedCity = null;
            $selectedStreet = null;

            try {
                if (Validate::isLoadedObject($selectedPudo) && !$isSameDayDelivery) {
                    $selectedCity = $selectedPudo->city;
                    $selectedStreet = $selectedPudo->street;
                    $parcelShops = $parcelShopService->getParcelShopsByCountryAndCity($countryCode, $selectedCity);
                    $parcelShops = $parcelShopService->moveSelectedShopToFirst($parcelShops, $selectedStreet);
                } else {
                    $selectedCity = $deliveryAddress->city;
                    $selectedStreet = $deliveryAddress->address1;
                    $parcelShops = $parcelShopService->getParcelShopsByCountryAndCity($countryCode, $selectedCity);
                    $parcelShops = $parcelShopService->moveSelectedShopToFirst($parcelShops, $selectedStreet);
                    if (!$parcelShops) {
                        $selectedCity = null;
                    }
                }
            } catch (DPDBalticsAPIException $e) {
                /** @var ExceptionService $exceptionService */
                $exceptionService = $this->getModuleContainer(ExceptionService::class);
                $tplVars = [
                    'errorMessage' => $exceptionService->getErrorMessageForException(
                        $e,
                        $exceptionService->getAPIErrorMessages()
                    )
                ];
                $this->context->smarty->assign($tplVars);

                return $this->context->smarty->fetch(
                    $this->getLocalPath() . 'views/templates/admin/dpd-shipment-fatal-error.tpl'
                );
            } catch (Exception $e) {
                $tplVars = [
                    'errorMessage' => $this->l("Something went wrong. We couldn't find parcel shops."),
                ];
                $this->context->smarty->assign($tplVars);

                return $this->context->smarty->fetch(
                    $this->getLocalPath() . 'views/templates/admin/dpd-shipment-fatal-error.tpl'
                );
            }

            /** @var PudoService $pudoService */
            $pudoService = $this->getModuleContainer(PudoService::class);

            $pudoServices = $pudoService->setPudoServiceTypes($parcelShops);
            $pudoServices = $pudoService->formatPudoServicesWorkHours($pudoServices);

            if (isset($parcelShops[0])) {
                $coordinates = [
                    'lat' => $parcelShops[0]->getLatitude(),
                    'lng' => $parcelShops[0]->getLongitude(),
                ];
                $this->context->smarty->assign(
                    [
                        'coordinates' => $coordinates
                    ]
                );
            }

            if ($isSameDayDelivery) {
                $cityList['Rīga'] = 'Rīga';
            } else {
                $cityList = $parcelShopRepo->getAllCitiesByCountryCode($countryCode);
            }

            if (!in_array($selectedCity, $cityList) && isset($parcelShops[0])) {
                $selectedCity = $parcelShops[0]->getCity();
            }

            if (!$selectedCity) {
                $tplVars = [
                    'displayMessage' => true,
                    'messages' => [$this->l("Your delivery address city is not in a list of pickup cities, please select closest pickup point city below manually")],
                    'messageType_pudo' => 'danger'

                ];
                $this->context->smarty->assign($tplVars);
            }

            $streetList = $parcelShopRepo->getAllAddressesByCountryCodeAndCity($countryCode, $selectedCity);
            $this->context->smarty->assign(
                [
                    'carrierId' => $carrier->id,
                    'pickUpMap' => Configuration::get(Config::PICKUP_MAP),
                    'pudoId' => $pudoId,
                    'pudoServices' => $pudoServices,
                    'dpd_pickup_logo' => $this->getPathUri() . 'views/img/pickup.png',
                    'dpd_locker_logo' => $this->getPathUri() . 'views/img/locker.png',
                    'delivery_address' => $deliveryAddress,
                    'saved_pudo_id' => $selectedPudo->pudo_id,
                    'is_pudo' => (bool)$isPudo,
                    'city_list' => $cityList,
                    'selected_city' => $selectedCity,
                    'show_shop_list' => Configuration::get(Config::PARCEL_SHOP_DISPLAY),
                    'street_list' => $streetList,
                    'selected_street' => $selectedStreet,
                ]
            );

            $return .= $this->context->smarty->fetch(
                $this->getLocalPath() . '/views/templates/hook/front/pudo-points.tpl'
            );
        }

        return $return;
    }

    /**
     * Includes Vendor Autoload.
     */
    private function autoLoad()
    {
        require_once $this->getLocalPath() . 'vendor/autoload.php';
    }

    private function compile()
    {
        $containerCache = $this->getLocalPath() . 'var/cache/container.php';
        $containerConfigCache = new ConfigCache($containerCache, self::DISABLE_CACHE);
        $containerClass = get_class($this) . 'Container';
        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $locator = new FileLocator($this->getLocalPath() . 'config');
            $loader = new YamlFileLoader($containerBuilder, $locator);
            $loader->load('config.yml');
            $containerBuilder->compile();
            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(['class' => $containerClass]),
                $containerBuilder->getResources()
            );
        }
        require_once $containerCache;
        $this->moduleContainer = new $containerClass();
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $currentController = Tools::getValue('controller');

        if (Config::isPrestashopVersionBelow174()) {
            /** @var  $tabs TabService*/
           $tabs = $this->getModuleContainer()->get(TabService::class);
           $visibleClasses = $tabs->getTabsClassNames(false);

           if (in_array($currentController, $visibleClasses, true)) {
               Media::addJsDef(['visibleTabs' => $tabs->getTabsClassNames(true)]);
               $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/tabsHandlerBelowPs174.js');
           }
        }

        if ('AdminOrders' === $currentController) {
            $this->handleLabelPrintService();

            $this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/order/order-list.css');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/order_list.js');
            Media::addJsDef(
                [
                    'dpdHookAjaxShipmentController' => $this->context->link->getAdminLink(self::ADMIN_AJAX_SHIPMENTS_CONTROLLER),
                    'shipmentIsBeingPrintedMessage' => $this->context->smarty->fetch($this->getLocalPath() . 'views/templates/admin/partials/spinner.tpl') .
                        $this->l('Your labels are being saved please stay on the page'),
                    'noOrdersSelectedMessage' => $this->l('No orders were selected'),
                    'downloadSelectedLabelsButton' => $this->context->smarty->fetch($this->getLocalPath() . 'views/templates/admin/partials/download-selected-labels-button.tpl')
                ]
            );

            $orderId = Tools::getValue('id_order');
            $shipment = $this->getShipment($orderId);

            Media::addJsDef(
                [
                    'shipment' => $shipment,
                    'id_order' => $orderId
                ]
            );
        }

        if ('AdminOrders' === $currentController &&
            (Tools::isSubmit('vieworder') || Tools::getValue('action') === 'vieworder')
        ) {
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/order_expand_form.js');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/shipment.js');
            Media::addJsDef([
                'expandText' => $this->l('Expand'),
                'collapseText' => $this->l('Collapse'),
                'dpdAjaxShipmentsUrl' =>
                    $this->context->link->getAdminLink(self::ADMIN_AJAX_SHIPMENTS_CONTROLLER),
                'dpdMessages' => [
                    'invalidProductQuantity' => $this->l('Invalid product quantity entered'),
                    'invalidShipment' => $this->l('Invalid shipment selected'),
                    'parcelsLimitReached' => $this->l('Parcels limit reached in shipment'),
                    'successProductMove' => $this->l('Product moved successfully'),
                    'successCreation' => $this->l('Successful creation'),
                    'unexpectedError' => $this->l('Unexpected error appeared.'),
                    'invalidPrintoutFormat' => $this->l('Invalid printout format selected.'),
                    'cannotOpenWindow' => $this->l('Cannot print label, your browser may be blocking it.'),
                    'dpdRecipientAddressError' => $this->l('Please fill required fields')
                ],
                'id_language' => $this->context->language->id,
                'id_shop' => $this->context->shop->id,
                'id_cart' => $this->context->cart->id,
                'currentController' => $currentController,

            ]);

            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/carrier_phone.js');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/custom_select.js');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/label_position.js');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/pudo.js');
            $this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/customSelect/custom-select.css');
            $this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/order/admin-orders-controller.css');
            $this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/order/quantity-max-value-tip.css');
        }
        if ('AdminOrders' === $currentController &&
            (Tools::isSubmit('addorder') || Tools::getValue('action') === 'addorder')
            ) {
            /** @var ProductRepository $productRepo */
            $productRepo = $this->getModuleContainer(ProductRepository::class);

            Media::addJsDef([
                'dpdFrontController' => false,
                'pudoCarriers' => Tools::jsonEncode($productRepo->getPudoProducts()),
                'currentController' => $currentController,
                'dpdAjaxShipmentsUrl' =>
                    $this->context->link->getAdminLink(self::ADMIN_AJAX_SHIPMENTS_CONTROLLER),
                'ignoreAdminController' => true,
                'dpdAjaxPudoUrl' => $this->context->link->getAdminLink(self::ADMIN_PUDO_AJAX_CONTROLLER),
                'id_shop' => $this->context->shop->id,
            ]);

            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/pudo_list.js');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/carrier_phone.js');
            $this->context->controller->addJS($this->getPathUri() . 'views/js/admin/pudo.js');

            $this->context->controller->addCSS($this->getPathUri() . 'views/css/admin/order/admin-orders-controller.css');

            return;
        }
    }

    public function hookDisplayAdminOrder(array $params)
    {
        return Config::isPrestashopVersionAbove177() ? false : $this->displayInAdminOrderPage($params);
    }

    public function hookDisplayAdminOrderTabContent(array $params)
    {
        return !Config::isPrestashopVersionAbove177() ? false : $this->displayInAdminOrderPage($params);
    }

    private function displayInAdminOrderPage(array $params)
    {
        $order = new Order($params['id_order']);
        $cart = Cart::getCartByOrderId($params['id_order']);

        /** @var ProductRepository $productRepo */
        $productRepo = $this->getModuleContainer(ProductRepository::class);
        $carrier = new Carrier($order->id_carrier);
        if (!$productRepo->getProductIdByCarrierReference($carrier->id_reference)) {
            return;
        }

        $shipment = $this->getShipment($order->id);
        $dpdCodWarning = false;

        /** @var OrderService $orderService */
        $orderService = $this->getModuleContainer(OrderService::class);
        $orderDetails = $orderService->getOrderDetails($order, $cart->id_lang);

        $customAddresses = [];

        /** @var ReceiverAddressRepository $receiverAddressRepository */
        $receiverAddressRepository = $this->getModuleContainer(ReceiverAddressRepository::class);

        $customOrderAddressesIds = $receiverAddressRepository->getAddressIdByOrderId($order->id);
        foreach ($customOrderAddressesIds as $customOrderAddressId) {
            $customAddress = new Address($customOrderAddressId);

            $customAddresses[] = [
                'id_address' => $customAddress->id,
                'alias' => $customAddress->alias
            ];
        }
        $combinedCustomerAddresses = array_merge($orderDetails['customer']['addresses'], $customAddresses);

        /** @var PhonePrefixRepository $phonePrefixRepository */
        $phonePrefixRepository = $this->getModuleContainer(PhonePrefixRepository::class);

        $products = $cart->getProducts();

        /** @var ProductRepository $productRepository */
        $productRepository = $this->getModuleContainer(ProductRepository::class);
        $dpdProducts = $productRepository->getAllProducts();
        $dpdProducts->where('active', '=', 1);
        /** @var LabelPositionService $labelPositionService */
        $labelPositionService = $this->getModuleContainer(LabelPositionService::class);
        $labelPositionService->assignLabelPositions($shipment->id);
        $labelPositionService->assignLabelFormat($shipment->id);

        /** @var PaymentService $paymentService */
        $paymentService = $this->getModuleContainer(PaymentService::class);
        try {
            $isCodPayment = $paymentService->isOrderPaymentCod($order->module);
        } catch (Exception $e) {
            $tplVars = [
                'errorMessage' => $this->l('Something went wrong checking payment method'),
            ];
            $this->context->smarty->assign($tplVars);

            return $this->context->smarty->fetch(
                $this->getLocalPath() . 'views/templates/admin/dpd-shipment-fatal-error.tpl'
            );
        }

        if ($isCodPayment) {
            $dpdProducts->where('is_cod', '=', 1);
        } else {
            $dpdProducts->where('is_cod', '=', 0);
        }
        /** @var PudoRepository $pudoRepo */
        $pudoRepo = $this->getModuleContainer(PudoRepository::class);
        $selectedProduct = new DPDProduct($shipment->id_service);
        $isPudo = $selectedProduct->is_pudo;
        $pudoId = $pudoRepo->getIdByCart($order->id_cart);

        $selectedPudo = new DPDPudo($pudoId);

        /** @var ParcelShopService $parcelShopService */
        /** @var ParcelShopRepository $parcelShopRepo */
        $parcelShopService= $this->getModuleContainer(ParcelShopService::class);
        $parcelShopRepo = $this->getModuleContainer(ParcelShopRepository::class);

        /** @var \Invertus\dpdBaltics\Provider\CurrentCountryProvider $currentCountryProvider */
        $currentCountryProvider = $this->getModuleContainer(\Invertus\dpdBaltics\Provider\CurrentCountryProvider::class);
        $countryCode = $currentCountryProvider->getCurrentCountryIsoCode($cart);

        $selectedCity = null;
        try {
            if ($pudoId) {
                $selectedCity = $selectedPudo->city;
                $parcelShops = $parcelShopService->getParcelShopsByCountryAndCity(
                    $countryCode,
                    $selectedCity
                );
            } else {
                $parcelShops = $parcelShopService->getParcelShopsByCountryAndCity(
                    $countryCode,
                    $orderDetails['order_address']['city']
                );
            }
        } catch (Exception $e) {
            $tplVars = [
                'errorMessage' => $this->l('Fatal error while searching for parcel shops: ') . $e->getMessage(),
            ];
            $this->context->smarty->assign($tplVars);

            return $this->context->smarty->fetch(
                $this->getLocalPath() . 'views/templates/admin/dpd-shipment-fatal-error.tpl'
            );
        }

        /** @var null|\Invertus\dpdBalticsApi\Api\DTO\Object\ParcelShop $selectedPudoService */
        $selectedPudoService = null;
        $hasParcelShops = false;
        if ($parcelShops) {
            if ($selectedPudo->pudo_id) {
                $selectedPudoService = $parcelShopService->getParcelShopByShopId($selectedPudo->pudo_id)[0];
            } else {
                $selectedPudoService = $parcelShops[0];
            }
            $hasParcelShops = true;
        }

        if ($selectedPudoService) {
            /** @var PudoService $pudoService */
            $pudoService = $this->getModuleContainer(PudoService::class);

            $selectedPudoService->setOpeningHours(
                $pudoService->formatPudoServiceWorkHours($selectedPudoService->getOpeningHours())
            );
        }

        /** @var DPDProduct $dpdProduct */
        foreach ($dpdProducts as $dpdProduct) {
            if ($dpdProduct->is_pudo && !$hasParcelShops) {
                $dpdProduct->active = 0;
            }
        }
        $cityList = $parcelShopRepo->getAllCitiesByCountryCode($countryCode);

        if (\Invertus\dpdBaltics\Config\Config::productHasDeliveryTime($selectedProduct->product_reference)) {
            /** @var \Invertus\dpdBaltics\Repository\OrderDeliveryTimeRepository $orderDeliveryTimeRepo */
            $orderDeliveryTimeRepo = $this->getModuleContainer()->get(\Invertus\dpdBaltics\Repository\OrderDeliveryTimeRepository::class);
            $orderDeliveryTimeId = $orderDeliveryTimeRepo->getOrderDeliveryIdByCartId($cart->id);
            if ($orderDeliveryTimeId) {
                $orderDeliveryTime = new DPDOrderDeliveryTime($orderDeliveryTimeId);
                $this->context->smarty->assign([
                    'orderDeliveryTime' => $orderDeliveryTime->delivery_time,
                    'deliveryTimes' => \Invertus\dpdBaltics\Config\Config::getDeliveryTimes($countryCode)
                ]);
            }
        }

        $tplVars = [
            'dpdLogoUrl' => $this->getPathUri() . 'views/img/DPDLogo.gif',
            'shipment' => $shipment,
            'testOrder' => $shipment->is_test,
            'total_products' => 1,
            'contractPageLink' => $this->context->link->getAdminLink(self::ADMIN_PRODUCTS_CONTROLLER),
            'dpdCodWarning' => $dpdCodWarning,
            'testMode' => Configuration::get(Config::SHIPMENT_TEST_MODE),
            'printLabelOption' => Configuration::get(Config::LABEL_PRINT_OPTION),
            'defaultLabelFormat' => Configuration::get(Config::DEFAULT_LABEL_FORMAT),
            'combinedAddresses' => $combinedCustomerAddresses,
            'orderDetails' => $orderDetails,
            'mobilePhoneCodeList' => $phonePrefixRepository->getCallPrefixes(),
            'products' => $products,
            'dpdProducts' => $dpdProducts,
            'isCodPayment' => $isCodPayment,
            'is_pudo' => (bool)$isPudo,
            'selectedPudo' => $selectedPudoService,
            'city_list' => $cityList,
            'selected_city' => $selectedCity,
            'has_parcel_shops' => $hasParcelShops,
            'receiverAddressCountries' => Country::getCountries($this->context->language->id, true),
            'documentReturnEnabled' => Configuration::get(Config::DOCUMENT_RETURN),
        ];

        $this->context->smarty->assign($tplVars);

        return $this->context->smarty->fetch(
            $this->getLocalPath() . 'views/templates/hook/admin/admin-order.tpl'
        );
    }

    public function hookActionValidateOrder($params)
    {
        $carrier = new Carrier($params['order']->id_carrier);
        if ($carrier->external_module_name !== $this->name) {
            return;
        }

        $isAdminOrderPage = 'AdminOrders' === Tools::getValue('controller') || Config::isPrestashopVersionAbove177();
        $isAdminNewOrderForm = Tools::isSubmit('addorder') || Tools::isSubmit('cart_summary');

        if ($isAdminOrderPage && $isAdminNewOrderForm) {
            $dpdPhone = Tools::getValue('dpd-phone');
            $dpdPhoneArea = Tools::getValue('dpd-phone-area');

            /** @var \Invertus\dpdBaltics\Service\OrderDeliveryTimeService $orderDeliveryService */
            $carrierPhoneService = $this->getModuleContainer(CarrierPhoneService::class);

            if (!empty($dpdPhone) && !empty($dpdPhoneArea)) {
                if (!$carrierPhoneService->saveCarrierPhone($this->context->cart->id, $dpdPhone, $dpdPhoneArea)) {
                    $error = $this->l('Phone data is not saved');
                    die($error);
                }
            }

            /** @var CarrierPhoneService $carrierPhoneService */
            $orderDeliveryService = $this->getModuleContainer(\Invertus\dpdBaltics\Service\OrderDeliveryTimeService::class);
            $deliveryTime = Tools::getValue('dpd-delivery-time');
            if ($deliveryTime !== null) {
                if (!$orderDeliveryService->saveDeliveryTime(
                    $this->context->cart->id,
                    $deliveryTime
                )) {
                    $error = $this->l('Delivery time is not saved');
                    die($error);
                };
            }
        }

        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->getModuleContainer(ShipmentService::class);
        $shipmentService->createShipmentFromOrder($params['order']);
    }

    public function printLabel($idShipment)
    {
        /** @var LabelApiService $labelApiService */
        $labelApiService = $this->getModuleContainer(LabelApiService::class);

        $shipment = new DPDShipment($idShipment);
        $format = $shipment->label_format;
        $position = $shipment->label_position;

        try {
            /** @var ParcelPrintResponse $parcelPrintResponse */
            $parcelPrintResponse = $labelApiService->printLabel($shipment->pl_number, $format, $position);
        } catch (DPDBalticsAPIException $e) {
            /** @var ExceptionService $exceptionService */
            $exceptionService = $this->getModuleContainer(ExceptionService::class);
            Context::getContext()->controller->errors[] = $exceptionService->getErrorMessageForException(
                $e,
                $exceptionService->getAPIErrorMessages()
            );
            return;
        } catch (Exception $e) {
            Context::getContext()->controller->errors[] = $this->l('Failed to print label: ') . $e->getMessage();
            return;
        }

        if ($parcelPrintResponse->getStatus() === Config::API_SUCCESS_STATUS) {
            $this->updateOrderCarrier($idShipment);
            return;
        }

        Context::getContext()->controller->errors[] = $this->l($parcelPrintResponse->getErrLog());
    }

    public function printMultipleLabels($shipmentIds)
    {
        $plNumbers = [];
        foreach ($shipmentIds as $shipmentId) {
            $shipment = new DPDShipment($shipmentId);
            $plNumbers[] = $shipment->pl_number;
        }

        /** @var LabelApiService $labelApiService */
        $labelApiService = $this->getModuleContainer(LabelApiService::class);

        $position = Configuration::get(Config::DEFAULT_LABEL_POSITION);
        $format = Configuration::get(Config::DEFAULT_LABEL_FORMAT);

        try {
            /** @var ParcelPrintResponse $parcelPrintResponse */
            $parcelPrintResponse = $labelApiService->printLabel(implode('|', $plNumbers), $format, $position);
        } catch (DPDBalticsAPIException $e) {
            /** @var ExceptionService $exceptionService */
            $exceptionService = $this->getModuleContainer(ExceptionService::class);
            Context::getContext()->controller->errors[] = $exceptionService->getErrorMessageForException(
                $e,
                $exceptionService->getAPIErrorMessages()
            );
            return;
        } catch (Exception $e) {
            Context::getContext()->controller->errors[] = $this->l('Failed to print label: ') . $e->getMessage();
            return;
        }

        if ($parcelPrintResponse->getStatus() === Config::API_SUCCESS_STATUS) {
            foreach ($shipmentIds as $shipmentId) {
                $this->updateOrderCarrier($shipmentId);
            }
            return;
        }

        Context::getContext()->controller->errors[] = $this->l($parcelPrintResponse->getErrLog());
    }

    private function updateOrderCarrier($shipmentId)
    {
        $shipment = new DPDShipment($shipmentId);
        /** @var OrderRepository $orderRepo */
        /** @var TrackingService $trackingService */
        $orderRepo = $this->getModuleContainer(OrderRepository::class);
        $trackingService = $this->getModuleContainer(TrackingService::class);
        $orderCarrierId = $orderRepo->getOrderCarrierId($shipment->id_order);

        $orderCarrier = new OrderCarrier($orderCarrierId);
        $orderCarrier->tracking_number = $trackingService->getTrackingNumber($shipment->pl_number);

        try {
            $orderCarrier->update();
        } catch (Exception $e) {
            Context::getContext()->controller->errors[] =
                $this->l('Failed to save tracking number: ') . $e->getMessage();
            return;
        }

        $shipment->printed_label = 1;
        $shipment->date_print = date('Y-m-d H:i:s');
        $shipment->update();
    }

    public function hookDisplayOrderDetail($params)
    {
        $isReturnServiceEnabled = Configuration::get(Config::PARCEL_RETURN);
        if (!$isReturnServiceEnabled) {
            return;
        }
        if (CountryUtility::isEstonia()) {
            return;
        }

        /** @var ShipmentRepository $shipmentRepo */
        $shipmentRepo = $this->getModuleContainer(ShipmentRepository::class);
        $shipmentId = $shipmentRepo->getIdByOrderId($params['order']->id);

        $orderState = new OrderState($params['order']->current_state);
        if (!$orderState->delivery) {
            return;
        }
        /** @var AddressTemplateRepository $addressTemplateRepo */
        $addressTemplateRepo = $this->getModuleContainer(AddressTemplateRepository::class);
        $returnAddressTemplates = $addressTemplateRepo->getReturnServiceAddressTemplates();

        $shipment = new DPDShipment($shipmentId);

        if (!$returnAddressTemplates) {
            return;
        }

        $showTemplates = false;
        if (sizeof($returnAddressTemplates) > 1 && !$shipment->return_pl_number) {
            $showTemplates = true;
        }

        if (isset($this->context->cookie->dpd_error)) {
            $this->context->controller->errors[] = json_decode($this->context->cookie->dpd_error);
            unset($this->context->cookie->dpd_error);
        }
        $href = $this->context->link->getModuleLink(
            $this->name,
            'ShipmentReturn',
            [
                'id_order' => $params['order']->id,
                'dpd-return-submit' => ''
            ]
        );

        $this->context->smarty->assign(
            [
                'href' => $href,
                'return_template_ids' => $returnAddressTemplates,
                'show_template' => $showTemplates,
            ]
        );
        $html = $this->context->smarty->fetch(
            $this->getLocalPath() . 'views/templates/hook/front/order-detail.tpl'
        );

        return $html;
    }

    public function hookActionAdminOrdersListingFieldsModifier($params)
    {
        if (isset($params['select'])) {
            $params['select'] .= ' ,ds.`id_order` AS id_order_shipment ';
        }
        if (isset($params['join'])) {
            $params['join'] .= ' LEFT JOIN `' . _DB_PREFIX_ . 'dpd_shipment` ds ON ds.`id_order` = a.`id_order` ';
        }
        $params['fields']['id_order_shipment'] = [
            'title' => $this->l('DPD Label'),
            'align' => 'text-center',
            'class' => 'fixed-width-xs',
            'orderby' => false,
            'search' => false,
            'remove_onclick' => true,
            'callback_object' => 'dpdbaltics',
            'callback' => 'returnOrderListIcon'
        ];
    }

    /**
     * Callback function, it has to be static so can't call $this, so have to reload dpdBaltics module inside the function
     * @param $idOrder
     * @return string
     * @throws Exception
     */
    public static function returnOrderListIcon($orderId)
    {
        $dpdBaltics = Module::getInstanceByName('dpdbaltics');

        $dpdBaltics->context->smarty->assign('idOrder', $orderId);

        $dpdBaltics->context->smarty->assign(
            'message',
            $dpdBaltics->l('Print label(s) from DPD system. Once label is saved you won\'t be able to modify contents of shipments')
        );
        $icon = $dpdBaltics->context->smarty->fetch(
            $dpdBaltics->getLocalPath() . 'views/templates/hook/admin/order-list-save-label-icon.tpl'
        );


        $dpdBaltics->context->smarty->assign('icon', $icon);

        return $dpdBaltics->context->smarty->fetch($dpdBaltics->getLocalPath() . 'views/templates/hook/admin/order-list-icon-container.tpl');
    }


    public function hookDisplayAdminListBefore()
    {
        if ($this->context->controller instanceof AdminOrdersControllerCore) {
            return $this->context->smarty->fetch($this->getLocalPath() . 'views/templates/hook/admin/admin-orders-header-hook.tpl');
        }
    }

    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        if (!Config::isPrestashopVersionAbove177()) {
            return false;
        }

        $definition = $params['definition'];

        $definition->getColumns()
            ->addAfter(
                'date_add',
                (new ActionColumn('dpd_print_label'))
                    ->setName($this->l('Dpd print label'))
                    ->setOptions([
                        'actions' => $this->getGridAction()
                    ])
            );

        $definition->getBulkActions()
            ->add(
                (new SubmitBulkActionCustom('print_multiple_labels'))
                ->setName($this->l('Print multiple labels'))
                ->setOptions([
                    'submit_route' => 'dpdbaltics_download_multiple_printed_labels',
                ])
            )
        ;
    }

    /**
     * @return RowActionCollection
     */
    private function getGridAction()
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowActionCustom('print_delivery_slip'))
                    ->setName($this->l('Print label(s) from DPD system. Once label is saved you won\'t be able to modify contents of shipments'))
                    ->setIcon('print')
                    ->setOptions([
                        'route' => 'dpdbaltics_download_printed_label',
                        'route_param_name' => 'orderId',
                        'route_param_field' => 'id_order',
                        'confirm_message' => $this->l('Would you like to print shipping label?'),
                        'accessibility_checker' => $this->getModuleContainer()->get(PrintAccessibilityChecker::class),
                    ])
            );
    }

    private function getShipment($idOrder)
    {
        if (!$idOrder) {
            return false;
        }
        /** @var ShipmentRepository $shipmentRepository */
        $shipmentRepository = $this->getModuleContainer(ShipmentRepository::class);
        $shipmentId = $shipmentRepository->getIdByOrderId($idOrder);
        $shipment = new DPDShipment($shipmentId);

        if (!Validate::isLoadedObject($shipment)) {
            return false;
        }

        return $shipment;
    }

    private function handleLabelPrintService()
    {
        if (Tools::isSubmit('print_label')) {
            $idShipment = Tools::getValue('id_dpd_shipment');
            $this->printLabel($idShipment);
            return;
        }

        if (Tools::isSubmit('print_multiple_labels')) {
            $shipmentIds = json_decode(Tools::getValue('shipment_ids'));
            $this->printMultipleLabels($shipmentIds);
            return;
        }
    }
}
