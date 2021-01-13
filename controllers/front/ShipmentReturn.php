<?php

use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\ShipmentRepository;
use Invertus\dpdBaltics\Service\API\LabelApiService;
use Invertus\dpdBaltics\Service\Exception\ExceptionService;
use Invertus\dpdBaltics\Service\ShipmentService;
use Invertus\dpdBalticsApi\Exception\DPDBalticsAPIException;

class DpdbalticsShipmentReturnModuleFrontController extends ModuleFrontController
{
    const FILENAME = 'ShipmentReturn';

    public function postProcess()
    {
//        return phpinfo();
        if (!Tools::isSubmit('dpd-return-submit')) {
            return;
        }

        $orderid = Tools::getValue('id_order');
        $returnTemplateId = Tools::getValue('return_template_id');
        $this->printReturnLabel($orderid, $returnTemplateId);
    }

    private function printReturnLabel($orderId, $returnTemplateId)
    {
        $response['status'] = false;

        /** @var ShipmentRepository $shipmentRepo */
        $shipmentRepo = $this->module->getModuleContainer(ShipmentRepository::class);
        $dpdShipmentId = $shipmentRepo->getIdByOrderId($orderId);
        $dpdShipment = new DPDShipment($dpdShipmentId);
        /** @var LabelApiService $labelApiService */
        $labelApiService = $this->module->getModuleContainer(LabelApiService::class);
        if ($dpdShipment->return_pl_number) {
            try {
                $response = $labelApiService->printLabel($dpdShipment->return_pl_number);
                $this->validateLabel($response->getStatus(), $response->getErrLog(), $orderId);
                exit();
            } catch (Exception $e) {
                $this->context->cookie->dpd_error = json_encode($e->getMessage());
                Tools::redirect(
                    $this->context->link->getPageLink(
                        'order-detail',
                        true,
                        null,
                        [
                            'id_order' => $orderId,
                        ]
                    )
                );
            }
        }
        try {
            /** @var ShipmentService $shipmentService */
            $shipmentService = $this->module->getModuleContainer(ShipmentService::class);
            $dpdShipment = $shipmentService->createReturnServiceShipment($returnTemplateId, $orderId);
            $response = $labelApiService->printLabel($dpdShipment->return_pl_number);
            $this->validateLabel($response->getStatus(), $response->getErrLog(), $orderId);
            exit();
        } catch (DPDBalticsAPIException $e) {
            /** @var ExceptionService $exceptionService */
            $exceptionService = $this->module->getModuleContainer(ExceptionService::class);
            $errorMessage = $exceptionService->getErrorMessageForException(
                $e,
                $exceptionService->getAPIErrorMessages()
            );
            $this->context->cookie->dpd_error = json_encode($errorMessage);
            Tools::redirect(
                $this->context->link->getPageLink(
                    'order-detail',
                    true,
                    null,
                    [
                        'id_order' => $orderId,
                    ]
                )
            );
        } catch (Exception $e) {
            $this->context->cookie->dpd_error = json_encode($e->getMessage());
            Tools::redirect(
                $this->context->link->getPageLink(
                    'order-detail',
                    true,
                    null,
                    [
                        'id_order' => $orderId,
                    ]
                )
            );
        }
    }

    private function validateLabel($status, $errorLog, $orderId)
    {
        if ($status !== Config::API_SUCCESS_STATUS) {
            $this->context->cookie->dpd_error = json_encode($errorLog);
            Tools::redirect(
                $this->context->link->getPageLink(
                    'order-detail',
                    true,
                    null,
                    [
                        'id_order' => $orderId,
                    ]
                )
            );
        }
    }
}
