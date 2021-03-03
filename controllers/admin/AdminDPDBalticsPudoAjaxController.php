<?php

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Controller\AbstractAdminController;
use Invertus\dpdBaltics\Provider\CurrentCountryProvider;
use Invertus\dpdBaltics\Repository\ParcelShopRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\PudoRepository;
use Invertus\dpdBaltics\Service\OrderService;

require_once dirname(__DIR__).'/../vendor/autoload.php';

class AdminDPDBalticsPudoAjaxController extends AbstractAdminController
{
    public function postProcess()
    {
        $action = Tools::getValue('action');
        if ($action === 'getPudoCarriers') {
            $this->ajaxProcessGetPudoCarriers();
        }

        if ($action === 'addPudoCart') {
            $this->ajaxProcessAddPudoCart();
        }
    }

    public function ajaxProcessGetPudoCarriers()
    {
        $idAddress = (int) Tools::getValue('id_address');
        $carrierId = (int) Tools::getValue('id_carrier');

        $idCart = (int) Tools::getValue('id_cart');
        $cart = new Cart($idCart);

        if (!$idAddress && !$carrierId && Validate::isLoadedObject($cart)) {
            $idAddress = $cart->id_address_delivery;
            $carrierId = $cart->id_carrier;
        }

        $address = new Address($idAddress, $this->context->language->id);
        if (!Validate::isLoadedObject($address)) {
            $this->ajaxDie('');
        }


        /** @var ProductRepository $productRepo */
        $productRepo = $this->module->getModuleContainer(ProductRepository::class);
        $product = $productRepo->findProductByCarrierReference($carrierId);
        $ispudo = $product['is_pudo'];
        if (!$ispudo) {
            $this->ajaxDie('');
        }

        $searchTemplate = $this->getPudoSearch(
            $cart
        );

        $this->ajaxDie(
            Tools::jsonEncode(
                array(
                    'searchTemplate' => $searchTemplate
                )
            )
        );
    }

    public function ajaxProcessAddPudoCart()
    {
        $idCart = (int) Tools::getValue('id_cart');
        $pudoId = Tools::getValue('pudo_id');
        $idCarrier = (int) Tools::getValue('id_carrier');
        $countryCode = Tools::getValue('country_code');
        $city = Tools::getValue('city');
        $street = Tools::getValue('street');
        $postCode = Tools::getValue('post_code');

        $carrier = new Carrier($idCarrier);
        /** @var PudoRepository $pudoRepository */
        $pudoRepository = $this->module->getModuleContainer(PudoRepository::class);
        $idPudoOrder = $pudoRepository->getPudoIdByCarrierId($carrier->id, $idCart);

        $pudoOrder = new DPDPudo($idPudoOrder);
        $pudoOrder->id_carrier = $carrier->id;
        $pudoOrder->id_cart = $idCart;
        $pudoOrder->pudo_id = $pudoId;
        $pudoOrder->country_code = $countryCode;
        $pudoOrder->city = $city;
        $pudoOrder->street = $street;
        $pudoOrder->post_code = $postCode;
        $this->ajaxDie($pudoOrder->save());
    }

    private function getPudoSearch(Cart $cart)
    {
        /** @var CurrentCountryProvider $currentCountryProvider */
        $currentCountryProvider = $this->module->getModuleContainer(CurrentCountryProvider::class);
        /** @var ParcelShopRepository $parcelShopRepo */
        $parcelShopRepo = $this->module->getModuleContainer(ParcelShopRepository::class);

        $countryCode = $currentCountryProvider->getCurrentCountryIsoCode($cart);
        $cityList = $parcelShopRepo->getAllCitiesByCountryCode($countryCode);

        $selectedAddress = new Address($cart->id_address_delivery);

        $this->context->smarty->assign(
            [
                'receiverAddressCountries' => Country::getCountries($this->context->language->id, true),
                'city_list' => $cityList,
                'selected_city' => $selectedAddress->city
            ]
        );
        return $this->context->smarty->fetch(
            $this->module->getLocalPath().'views/templates/hook/admin/partials/new-order-admin.tpl'
        );
    }
}
