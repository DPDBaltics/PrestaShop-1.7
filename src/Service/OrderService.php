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


namespace Invertus\dpdBaltics\Service;

use Address;
use Customer;
use DPDBaltics;
use Invertus\dpdBaltics\Exception\DpdCarrierException;
use Invertus\dpdBaltics\Repository\OrderRepository;
use Order;
use Validate;

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
        $orderRepository = $this->module->getModuleContainer('invertus.dpdbaltics.repository.order_repository');
        $dpdOrderPhone = $orderRepository->getPhoneByIdCart($order->id_cart);

        $details['customer'] = (array) $customer;
        $details['customer']['addresses'] = (array) $customerAddresses;
        $details['order_address'] = (array) $address;
        $details['dpd_order_phone'] = $dpdOrderPhone;

        return $details;
    }

    /**
     * @throws \PrestaShopException
     * @throws DpdCarrierException
     */
    public function updateOrderCarrier(Order $order, $newCarrierId)
    {
        if (!Validate::isLoadedObject($order)) {
          throw new DpdCarrierException('Could not load order');
        }

        $order->id_carrier = $newCarrierId;

        return $order->save();
    }
}
