<?php

namespace Invertus\dpdBaltics\Factory;

use Address;
use Carrier;
use Configuration;
use Customer;
use DPDOrderPhone;
use DPDProduct;
use DPDPudo;
use DPDShipment;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Repository\OrderRepository;
use Invertus\dpdBaltics\Repository\PudoRepository;
use Invertus\dpdBaltics\Repository\ShipmentRepository;
use Order;

class ShipmentDataFactory
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;
    /**
     * @var PudoRepository
     */
    private $pudoRepository;

    public function __construct(
        OrderRepository $orderRepository,
        ShipmentRepository $shipmentRepository,
        PudoRepository $pudoRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->pudoRepository = $pudoRepository;
    }

    public function getShipmentDataByIdOrder($orderId)
    {
        $shipmentData = new ShipmentData();
        $order = new Order($orderId);
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery);

        /** Address data */
        $shipmentData->setAddressId($order->id_address_delivery);
        $shipmentData->setCompany($address->company);
        $shipmentData->setName($address->firstname);
        $shipmentData->setSurname($address->lastname);
        $shipmentData->setAddress1($address->address1);
        $shipmentData->setAddress2($address->address2);
        $shipmentData->setPostCode($address->postcode);
        $shipmentData->setCity($address->city);
        $shipmentData->setCountry($address->country);
        $shipmentData->setEmail($customer->email);

        /** Phone */
        $dpdOrderPhone = $this->orderRepository->getPhoneByIdCart($order->id_cart);
        if (isset($dpdOrderPhone['phone_area'])) {
            $shipmentData->setPhoneArea($dpdOrderPhone['phone_area']);
        }

        if (isset($dpdOrderPhone['phone'])) {
            $shipmentData->setPhone($dpdOrderPhone['phone']);
        }

        /** Shipment */
        $shipmentId = $this->shipmentRepository->getIdByOrderId($orderId);
        $shipment = new DPDShipment($shipmentId);

        $shipmentData->setProduct($shipment->id_service);
        $shipmentData->setDateShipment($shipment->date_shipment);
        $shipmentData->setReference1($shipment->reference1);
        $shipmentData->setReference2($shipment->reference2);
        $shipmentData->setReference3($shipment->reference3);
        $shipmentData->setReference4(Config::getPsAndModuleVersion());
        $shipmentData->setWeight($shipment->weight);
        $shipmentData->setParcelAmount($shipment->num_of_parcels);
        $shipmentData->setGoodsPrice($shipment->goods_price);
        $shipmentData->setLabelFormat(Configuration::get(Config::DEFAULT_LABEL_FORMAT));
        $shipmentData->setLabelPosition(Configuration::get(Config::DEFAULT_LABEL_POSITION));

        /** Pudo */
        $dpdProduct = new DPDProduct($shipment->id_service);
        $shipmentData->setIsPudo($dpdProduct->is_pudo);
        if ($dpdProduct->is_pudo) {
            $carrier = new Carrier($order->id_carrier);
            $pudoId = $this->pudoRepository->getPudoIdByCarrierId($carrier->id, $order->id_cart);

            $dpdPudo = new DPDPudo($pudoId);
            /** Pudo settings */
            $shipmentData->setIdPudo($pudoId);
            $shipmentData->setDpdCountry($dpdPudo->country_code);
            $shipmentData->setSelectedPudoIsoCode($dpdPudo->country_code);
            $shipmentData->setDpdZipCode($address->postcode);
            $shipmentData->setSelectedPudoId($dpdPudo->pudo_id);
        }

        return $shipmentData;
    }
}
