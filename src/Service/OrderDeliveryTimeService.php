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

use DPDOrderDeliveryTime;
use Invertus\dpdBaltics\Repository\OrderDeliveryTimeRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderDeliveryTimeService
{
    /**
     * @var OrderDeliveryTimeRepository
     */
    private $deliveryTimeRepository;

    public function __construct(OrderDeliveryTimeRepository $deliveryTimeRepository)
    {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    public function saveDeliveryTime($cartId, $deliveryTime)
    {
        $orderDeliveryTimeId = $this->deliveryTimeRepository->getOrderDeliveryIdByCartId($cartId);

        $dpdOrderDeliveryTime = new DPDOrderDeliveryTime($orderDeliveryTimeId);

        $dpdOrderDeliveryTime->delivery_time = $deliveryTime;
        $dpdOrderDeliveryTime->id_cart = $cartId;

        if (!$dpdOrderDeliveryTime->save()) {
            return false;
        }

        return true;
    }
}