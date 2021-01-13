<?php

namespace Invertus\dpdBaltics\Service\Address;

use Address;
use Cart;
use Country;
use DPDBaltics;
use Invertus\dpdBaltics\Repository\OrderRepository;
use Invertus\dpdBaltics\Repository\PhonePrefixRepository;
use Invertus\dpdBaltics\Repository\ReceiverAddressRepository;
use Invertus\dpdBaltics\Service\OrderService;
use Invertus\dpdBaltics\Service\ShipmentService;
use Language;
use Order;
use DPDOrderPhone;
use PrestaShopDatabaseException;
use PrestaShopException;
use Smarty;
use SmartyException;
use State;
use Validate;

class ReceiverAddressService
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
     * @var ReceiverAddressRepository
     */
    private $receiverAddressRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var ShipmentService
     */
    private $shipmentService;
    /**
     * @var PhonePrefixRepository
     */
    private $phonePrefixRepository;
    /**
     * @var OrderService
     */
    private $orderService;

    public function __construct(
        DPDBaltics $module,
        Smarty $smarty,
        Language $language,
        ReceiverAddressRepository $receiverAddressRepository,
        OrderRepository $orderRepository,
        ShipmentService $shipmentService,
        PhonePrefixRepository $phonePrefixRepository,
        OrderService $orderService
    ) {
        $this->module = $module;
        $this->smarty = $smarty;
        $this->language = $language;
        $this->receiverAddressRepository = $receiverAddressRepository;
        $this->orderRepository = $orderRepository;
        $this->shipmentService = $shipmentService;
        $this->phonePrefixRepository = $phonePrefixRepository;
        $this->orderService = $orderService;
    }

    /**
     * @param $receiverAddressData
     * @param $orderId
     * @return Address|bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function addReceiverCustomAddress($receiverAddressData, $orderId)
    {
        $previousAddressesId = $this->receiverAddressRepository->getAddressIdByOrderId($orderId);

        $originalAddress = new Address($receiverAddressData->addressId);

        $originalAddressAliasAddition = $this->module->l(' - edited');

        foreach ($previousAddressesId as $addressId) {
            if ((int)$addressId === (int)$originalAddress->id) {
                $originalAddressAliasAddition = '';
            }
        }
        $customizedAddress = new Address();

        $customizedAddress->id_country = $receiverAddressData->country;
        $customizedAddress->id_state = $receiverAddressData->state ?: 0;
        $customizedAddress->alias = $originalAddress->alias . $originalAddressAliasAddition;
        $customizedAddress->company = $receiverAddressData->company;
        $customizedAddress->lastname = $receiverAddressData->surname;
        $customizedAddress->firstname = $receiverAddressData->name;
        $customizedAddress->address1 = $receiverAddressData->address1;
        $customizedAddress->address2 = $receiverAddressData->address2;
        $customizedAddress->postcode = $receiverAddressData->postcode;
        $customizedAddress->city = $receiverAddressData->city;
        $customizedAddress->phone = $receiverAddressData->phoneArea . $receiverAddressData->phone;

        if (!$customizedAddress->save()) {
            return false;
        };

        $orderCartId = Cart::getCartIdByOrderId($orderId);

        $idDpdOrderPhone = $this->orderRepository->getOrderPhoneIdByCartId($orderCartId);

        if (!$orderCartId) {
            return false;
        }

        $dpdOrderPhone = new DPDOrderPhone($idDpdOrderPhone);

        $dpdOrderPhone->phone = $receiverAddressData->phone;
        $dpdOrderPhone->phone_area = $receiverAddressData->phoneArea;
        $dpdOrderPhone->id_cart = $orderCartId;

        if (!$dpdOrderPhone->save()) {
            return false;
        };


        return $customizedAddress;
    }

    /**
     * updates address block and order id address delivery
     * @param Order $order
     * @param $idAddressDelivery
     * @return array|bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function processUpdateAddressBlock(Order $order, $idAddressDelivery)
    {
        $idLang = (int)$this->language->id;

        $address = new Address($idAddressDelivery);

        if (!Validate::isLoadedObject($address)) {
            return false;
        }

        $order->id_address_delivery = $idAddressDelivery;

        if (!$order->update()) {
            return false;
        }

        $orderDetails = $this->orderService->getOrderDetails(
            $order,
            $idLang,
            $idAddressDelivery
        );

        $customOrderAddressesIds = $this->receiverAddressRepository->getAddressIdByOrderId($order->id);
        $customAddresses = [];
        foreach ($customOrderAddressesIds as $customOrderAddressId) {
            $customAddress = new Address($customOrderAddressId);

            $customAddresses[] = [
                'id_address' => $customAddress->id,
                'alias' => $customAddress->alias
            ];
        }

        $combinedCustomerAddresses = array_merge($orderDetails['customer']['addresses'], $customAddresses);

        $this->smarty->assign(
            [
                'orderDetails' => $orderDetails,
                'receiverAddressCountries' => Country::getCountries($this->language->id),
                'stateName' => State::getNameById($orderDetails['order_address']['id_state']),
                'combinedAddresses' => $combinedCustomerAddresses,
                'mobilePhoneCodeList' => $this->phonePrefixRepository->getCallPrefixes(),
            ]
        );

        $template =
            $this->smarty->fetch(
                $this->module->getLocalPath() . 'views/templates/hook/admin/partials/customer-order-credentials.tpl'
            );

        return [
            'template' => $template
        ];
    }

    /**
     * @param $orderId
     * @return bool
     */
    public function deletePreviousEditedAddress($orderId)
    {
        $previousAddressesId = $this->receiverAddressRepository->getAddressIdByOrderId($orderId);

        foreach ($previousAddressesId as $idReceiverAddress => $peviousAddressId) {
            $previousAddress = new Address($peviousAddressId);

            if (!$previousAddress->delete()) {
                return false;
            }

            $this->receiverAddressRepository->deleteOldReceiverAddress($idReceiverAddress);
        }

        return true;
    }
}
