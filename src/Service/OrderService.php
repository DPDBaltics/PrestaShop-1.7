<?php

namespace Invertus\dpdBaltics\Service;

use Address;
use Customer;
use DPDBaltics;
use Invertus\dpdBaltics\Repository\OrderRepository;
use Order;

class OrderService
{
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    public function getOrderDetails(Order $order, $idLang, $idAddress = 0)
    {
        $details = [];

        $customer = new Customer($order->id_customer);

        $idOrderAddress = (int) $order->id_address_delivery;

        if ($idAddress) {
            $idOrderAddress = $idAddress;
        }

        $address = new Address($idOrderAddress, $idLang);
        $customerAddresses = $customer->getAddresses($idLang);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->module->getModuleContainer(OrderRepository::class);
        $dpdOrderPhone = $orderRepository->getPhoneByIdCart($order->id_cart);

        $details['customer'] = (array) $customer;
        $details['customer']['addresses'] = (array) $customerAddresses;
        $details['order_address'] = (array) $address;
        $details['dpd_order_phone'] = $dpdOrderPhone;

        return $details;
    }
}
