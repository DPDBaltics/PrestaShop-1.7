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

namespace Invertus\dpdBaltics\Service;

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Service\API\LabelApiService;
use Invertus\dpdBalticsApi\Api\DTO\Response\ParcelPrintResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LabelPrintingService
{
    private $labelApiService;
    private $shipmentService;

    public function __construct(
        LabelApiService $labelApiService,
        ShipmentService $shipmentService
    ) {
        $this->labelApiService = $labelApiService;
        $this->shipmentService = $shipmentService;
    }

    public function printOne($idShipment)
    {
        $shipment = new \DPDShipment($idShipment);
        $format = $shipment->label_format;
        $position = $shipment->label_position;
        $isAutomated = \Configuration::get(Config::AUTOMATED_PARCEL_RETURN);

        /** @var ParcelPrintResponse $parcelPrintResponse */
        if ($isAutomated) {
            if (!$shipment->return_pl_number) {
                $shipment = $this->shipmentService->createReturnServiceShipment(Config::RETURN_TEMPLATE_DEFAULT_ID, $shipment->id_order);
            }

            $plNumbers = [$shipment->pl_number, $shipment->return_pl_number];
        } else {
            $plNumbers = [$shipment->pl_number];
        }

        return $this->labelApiService->printLabel(implode('|', $plNumbers), $format, $position, false);
    }

    public function printMultiple($shipmentIds)
    {
        $position = \Configuration::get(Config::DEFAULT_LABEL_POSITION);
        $format = \Configuration::get(Config::DEFAULT_LABEL_FORMAT);
        $isAutomated = \Configuration::get(Config::AUTOMATED_PARCEL_RETURN);

        $plNumbers = [];
        foreach ($shipmentIds as $shipmentId) {
            $shipment = new \DPDShipment($shipmentId);
            $plNumbers[] = $shipment->pl_number;
            if ($isAutomated) {
                if(!$shipment->return_pl_number) {
                    $shipment = $this->shipmentService->createReturnServiceShipment(Config::RETURN_TEMPLATE_DEFAULT_ID, $shipment->id_order);
                }
                $plNumbers[] = $shipment->return_pl_number;
            }
        }

        return $this->labelApiService->printLabel(implode('|', $plNumbers), $format, $position, false);
    }
}