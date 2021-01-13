<?php

namespace Invertus\dpdBaltics\Service\Label;

use DPDBaltics;
use DPDShipment;
use http\Exception;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Service\API\ShipmentApiService;
use Invertus\dpdBaltics\Service\Exception\ExceptionService;
use Invertus\dpdBalticsApi\Api\DTO\Response\ShipmentCreationResponse;
use Invertus\dpdBalticsApi\Exception\DPDBalticsAPIException;

class LabelPrintingService
{
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var ShipmentApiService
     */
    private $shipmentApiService;
    /**
     * @var ExceptionService
     */
    private $exceptionService;

    public function __construct(
        DPDBaltics $module,
        ShipmentApiService $shipmentApiService,
        ExceptionService $exceptionService
    )
    {
        $this->module = $module;
        $this->shipmentApiService = $shipmentApiService;
        $this->exceptionService = $exceptionService;

    }

    public function setLabelOptions($shipmentId, $labelFormat, $labelPosition)
    {
        $shipment = new DPDShipment($shipmentId);

        if ($shipment->printed_label && !$shipment->printed_manifest) {
            $shipment->label_format = $labelFormat;
            $shipment->label_position = $labelPosition;
            try {
                $shipment->update();
            } catch (Exception $e) {
                $response['message'] = $this->module->l('Failed to update shipment label');
                return $response;
            }
            $response['status'] = true;
        }
        $response['id_dpd_shipment'] = $shipmentId;
        $response['message'] = $this->module->l('Parcel cannot be printed');

        return $response;
    }

    public function printAndSaveLabel(ShipmentData $shipmentData, $shipmentId, $orderId)
    {
        $response['status'] = false;

        try {
            /** @var ShipmentCreationResponse $shipmentCreationResponse */
            $shipmentCreationResponse = $this->shipmentApiService->createShipment(
                $shipmentData->getAddressId(),
                $shipmentData,
                $orderId
            );

        } catch (DPDBalticsAPIException $e) {

            $response['message'] = $this->exceptionService->getErrorMessageForException(
                $e,
                $this->exceptionService->getAPIErrorMessages()
            );

            return $response;

        } catch (Exception $e) {
            $response['message'] = $this->module->l("Failed to created shipment: {$e->getMessage()}");

            return $response;
        }

        if ($shipmentCreationResponse->getStatus() !== Config::API_SUCCESS_STATUS) {
            $response['message'] = $this->module->l(
                "Failed to created shipment: {$shipmentCreationResponse->getErrLog()}"
            );

            return $response;
        }

        $shipment = new DPDShipment($shipmentId);
        $shipment->pl_number = $shipmentCreationResponse->getPlNumbersAsString();
        try {
            $shipment->update();
        } catch (Exception $e) {
            $response['message'] = $this->module->l(
                "Failed to update shipment: {$e->getMessage()}"
            );

            return $response;
        }

        $response['status'] = true;
        $response['id_dpd_shipment'] = $shipmentId;

        return $response;
    }
}
