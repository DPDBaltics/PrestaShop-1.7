<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\PudoRepository;
use Invertus\dpdBaltics\Repository\ShipmentRepository;
use Invertus\dpdBaltics\Service\API\LabelApiService;
use Invertus\dpdBaltics\Service\API\ParcelShopSearchApiService;
use Invertus\dpdBaltics\Service\Exception\ExceptionService;
use Invertus\dpdBaltics\Service\GoogleApiService;
use Invertus\dpdBaltics\Service\Parcel\ParcelShopService;
use Invertus\dpdBaltics\Service\PudoService;
use Invertus\dpdBaltics\Service\ShipmentService;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelShopSearchResponse;
use Invertus\dpdBalticsApi\Exception\DPDBalticsAPIException;
use Symfony\Component\HttpFoundation\Response;

class DpdBalticsAjaxModuleFrontController extends ModuleFrontController
{
    const FILENAME = 'Ajax';

    /** @var  array */
    public $messages;

    /** @var DPDBaltics */
    public $module;

    public function checkAccess()
    {
        $isAjaxRequest = $this->isXmlHttpRequest();
        $isAjaxParameter = $this->ajax;
        $isTokenValid = $this->isTokenValid();
        $isParent = parent::checkAccess();
        $isCustomer = $this->context->customer->isLogged(true);

        $isAccessGranted = $isTokenValid &&
            $isAjaxParameter &&
            $isTokenValid &&
            $isParent &&
            $isAjaxRequest &&
            $isCustomer;

        if (!$isAccessGranted) {
            $error = $this->module->l('Unauthorized access', self::FILENAME);
            $this->messages[] = $error;
            $response = [
                'template' => $this->getMessageTemplate('danger'),
                'status' => false
            ];

            http_response_code(401);
            $this->ajaxDie(json_encode($response));
        }

        return true;
    }

    public function init()
    {
        if (!$this->ajax) {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }
        parent::init();
    }

    public function postProcess()
    {

        $action = Tools::getValue('action');
        $currentCountryProvider = $this->module->getModuleContainer(CurrentCountryProvider::class);
        $countryCode = $currentCountryProvider->getCurrentCountryIsoCode();
        $city = Tools::getValue('city_name');
        $carrierId = (int)Tools::getValue('id_carrier');
        switch ($action) {
            case 'searchPudoServices':
                $cartId = Context::getContext()->cart->id;
                try {
                    $response = $this->searchPudoServices($countryCode, $city, $carrierId, $cartId);
                } catch (Exception $e) {
                    $this->messages[] = $this->module->l('Parcel shop search failed!');
                    $this->ajaxDie(json_encode(
                        [
                            'status' => false,
                            'template' => $this->getMessageTemplate('danger'),
                        ]
                    ));
                }
                $this->ajaxDie(json_encode($response));
                break;
            case 'savePudoPickupPoint':
                $pudoId = Tools::getValue('id_pudo');
                $this->savePudoPickupPoint($pudoId, $countryCode);
                break;
            case 'updateStreetSelect':
                $city = Tools::getValue('city');
                $this->updateStreetSelect($countryCode, $city);
                break;
            case 'updateParcelBlock':
                $street = Tools::getValue('street');
                $city = Tools::getValue('city');
                $cartId = Context::getContext()->cart->id;
                try {
                    $response = $this->searchPudoServices($countryCode, $city, $carrierId, $cartId, $street);
                } catch (Exception $e) {
                    $this->messages[] = $this->module->l('Parcel shop search failed!');
                    $this->ajaxDie(json_encode(
                        [
                            'status' => false,
                            'template' => $this->getMessageTemplate('danger'),
                        ]
                    ));
                }
                $this->ajaxDie(json_encode($response));
                break;
            case 'saveSelectedStreet':
                $city = Tools::getValue('city');
                $street = Tools::getValue('street');
                $this->saveParcelShop($countryCode, $city, $street);
                break;
            default:
                break;
        }
        parent::postProcess();
    }

    private function searchPudoServices($countryCode, $city, $carrierId, $cartId, $street = '')
    {
        $carrier = new Carrier($carrierId);
        /** @var ParcelShopService $parcelShopService */
        /** @var ProductRepository $productRepo */
        $parcelShopService = $this->module->getModuleContainer(ParcelShopService::class);
        $productRepo = $this->module->getModuleContainer(ProductRepository::class);

        $product = $productRepo->findProductByCarrierReference($carrier->id_reference);
        $isSameDayDelivery = ($product['product_reference'] === Config::PRODUCT_TYPE_SAME_DAY_DELIVERY);
        if ($isSameDayDelivery) {
            $city = Config::SAME_DAY_DELIVERY_CITY;
        }
        if ($street) {
            $parcelShops = $parcelShopService->getFilteredParcels($countryCode, $city, $street);
        } else {
            $parcelShops = $parcelShopService->getParcelShopsByCountryAndCity($countryCode, $city);
        }

        /** @var PudoService $pudoService */
        $pudoService = $this->module->getModuleContainer(PudoService::class);

        $pudoServices = $pudoService->setPudoServiceTypes($parcelShops);
        $pudoServices = $pudoService->formatPudoServicesWorkHours($pudoServices);

        /** @var PudoRepository $pudoRepo */
        $pudoRepo = $this->module->getModuleContainer(PudoRepository::class);
        $pudoId = $pudoRepo->getIdByCart($cartId);
        $selectedPudo = new DPDPudo($pudoId);
        $coordinates = [];

        if (isset($parcelShops[0])) {
            $coordinates = [
                'lat' => $parcelShops[0]->getLatitude(),
                'lng' => $parcelShops[0]->getLongitude(),
            ];
        }

        $this->context->smarty->assign(
            [
                'carrierId' => $carrierId,
                'pickUpMap' => Configuration::get(Config::PICKUP_MAP),
                'pudoServices' => $pudoServices,
                'dpd_pickup_logo' => $this->module->getPathUri() . 'views/img/pickup.png',
                'dpd_locker_logo' => $this->module->getPathUri() . 'views/img/locker.png',
                'countryList' => Country::getCountries($this->context->language->id, true),
                'saved_pudo_id' => $selectedPudo->pudo_id
            ]
        );

        return [
            'template' => $this->context->smarty->fetch(
                $this->module->getLocalPath() . '/views/templates/hook/front/partials/markers-list.tpl'
            ),
            'status' => true,
            'coordinates' => json_encode($coordinates)
        ];
    }

    private function savePudoPickupPoint($pudoId, $countryCode)
    {
        /** @var PudoService $pudoService */
        $pudoService = $this->module->getModuleContainer(PudoService::class);

        $addPudoCartOrderStatus = $pudoService->savePudoCartOrder(
            $this->context->cart->id_carrier,
            $this->context->cart->id,
            $pudoId,
            $countryCode
        );

        if (!$addPudoCartOrderStatus) {
            $this->messages[] = $this->l('Failed to save pickup point.');
            $this->ajaxDie(json_encode([
                'template' => $this->getMessageTemplate('danger'),
                'status' => false
            ]));
        }
        $this->ajaxDie(json_encode([
            'status' => true
        ]));
    }

    public function getFileName()
    {
        return self::FILENAME;
    }

    protected function getMessageTemplate(
        $type
    ) {
        $flashMessageTypes = ['success', 'info', 'warning', 'danger'];

        if (!in_array($type, $flashMessageTypes)) {
            $message = sprintf(
                'Invalid flash message type "%s" supplied. Available types are: %s',
                $type,
                implode(',', $flashMessageTypes)
            );
            throw new Exception($message);
        }

        $this->context->smarty->assign([
            'messageType' => $type,
            'messages' => $this->messages,
            'displayMessage' => true
        ]);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/hook/front/partials/dpd-message.tpl'
        );
    }

    private function updateStreetSelect($countryCode, $city)
    {
        /** @var ParcelShopRepository $parcelShopRepo */
        $parcelShopRepo = $this->module->getModuleContainer(ParcelShopRepository::class);
        $streetList = $parcelShopRepo->getAllAddressesByCountryCodeAndCity($countryCode, $city);

        $this->context->smarty->assign(
            [
                'street_list' => $streetList,
                'show_shop_list' => Configuration::get(Config::PARCEL_SHOP_DISPLAY),
            ]
        );

        $streetSelectTpl = $this->context->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/hook/front/partials/pudo-search-street.tpl'
        );

        $this->ajaxDie(json_encode([
            'status' => true,
            'template' => $streetSelectTpl
        ]));
    }

    private function saveParcelShop($countryCode, $city, $street)
    {
        /** @var PudoService $pudoService */
        $pudoService = $this->module->getModuleContainer(PudoService::class);

        $cartId = $this->context->cart->id;
        $idCarrier = $this->context->cart->id_carrier;
        $isSuccess = $pudoService->saveSelectedParcel($cartId, $city, $street, $countryCode, $idCarrier);

        if (!$isSuccess) {
            $this->messages[] = $this->l('Failed to save pickup point.');
            $this->ajaxDie(json_encode([
                'template' => $this->getMessageTemplate('danger'),
                'status' => false
            ]));
        }

        $pudoId = $pudoService->getPudoIdByCityAndAddress($city, $street);
        $parcelShops = $pudoService->getClosestParcelShops($pudoId);
        $coordinates = [];
        $selectedPudo = null;
        if (isset($parcelShops[0])) {
            $coordinates = [
                'lat' => $parcelShops[0]->getLatitude(),
                'lng' => $parcelShops[0]->getLongitude(),
            ];
            $selectedPudo = $parcelShops[0];
        }
        $pudoServices = $pudoService->setPudoServiceTypes($parcelShops);
        $pudoServices = $pudoService->formatPudoServicesWorkHours($pudoServices);

        $this->context->smarty->assign(
            [
//                'carrierId' => $carrierId,
                'pickUpMap' => Configuration::get(Config::PICKUP_MAP),
                'pudoId' => 1,
                'pudoServices' => $pudoServices,
                'dpd_pickup_logo' => $this->module->getPathUri() . 'views/img/pickup.png',
                'dpd_locker_logo' => $this->module->getPathUri() . 'views/img/locker.png',
                'countryList' => Country::getCountries($this->context->language->id, true),
                'selectedPudo' => $selectedPudo,
                'saved_pudo_id' => $selectedPudo->getParcelShopId()
            ]
        );

        $this->ajaxDie(json_encode([
            'template' => $this->context->smarty->fetch(
                $this->module->getLocalPath() . '/views/templates/hook/front/partials/markers-list.tpl'
            ),
            'status' => true,
            'selectedPudoId' => $selectedPudo->getParcelShopId(),
            'coordinates' => $coordinates
        ]));
    }
}
