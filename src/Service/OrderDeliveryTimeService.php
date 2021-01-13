<?php

namespace Invertus\dpdBaltics\Service;

use DPDOrderDeliveryTime;
use Invertus\dpdBaltics\Repository\OrderDeliveryTimeRepository;

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