<?php

namespace Invertus\dpdBaltics\Controller;

use Invertus\dpdBaltics\Converter\FormDataConverter;
use Invertus\dpdBaltics\Service\Label\LabelPrintingService;
use Invertus\dpdBaltics\Service\ShipmentService;
use Invertus\dpdBaltics\Util\ServerGlobalsUtility;
use Order;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tools;

class OrderLabelController extends FrameworkBundleAdminController
{
    /**
     * @var \DPDBaltics|\Module
     */
    private $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = \Module::getInstanceByName('dpdbaltics');
    }

    /**
     * Generates print label for given dpd order(when order shipment is already saved)
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $shipmentId
     * @param string $labelFormat
     * @param string $labelPosition
     *
     * @return Response
     */
    public function printLabelOrderViewAction($shipmentId, $labelFormat, $labelPosition)
    {
        /** @var LabelPrintingService $shipmentService */
        $printingService = $this->module->getModuleContainer('invertus.dpdbaltics.service.label.label_printing_service');
        $response = $printingService->setLabelOptions($shipmentId, $labelFormat, $labelPosition);

        if (!$response['status'] || !$response['id_dpd_shipment']) {
            $this->addFlash('error', $response['message']);

            $redirectLink = ServerGlobalsUtility::getHttpReferer();

            if ($redirectLink) {
                return $this->redirect($_SERVER['HTTP_REFERER'], 302);
            }

            return $this->redirectToRoute('admin_orders_index');
        }
        $this->module->printLabel($response['id_dpd_shipment']);

        //This part should never be reached
        exit;
    }

    /**
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     * @param integer $orderId
     * @return Response
     */
    public function saveAndPrintLabelOrderViewAction($orderId)
    {
        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->module->getModuleContainer('invertus.dpdbaltics.service.shipment_service');
        $response = $shipmentService->formatLabelAndCreateShipmentByOrderId($orderId);

        if (!$response['status'] || !$response['id_dpd_shipment']) {
            $this->addFlash('error', $response['message']);

            $redirectLink = ServerGlobalsUtility::getHttpReferer();

            if ($redirectLink) {
                return $this->redirect($_SERVER['HTTP_REFERER'], 302);
            }

            return $this->redirectToRoute('admin_orders_index');
        }
        $this->module->printLabel($response['id_dpd_shipment']);

        //This part should never be reached
        exit;
    }

    /**
     * Generates and prints label in order list
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param integer $orderId
     *
     * @return Response
     */
    public function printAndSaveLabelFromOrderListAction($orderId)
    {
        /** @var ShipmentService $shipmentService */
        $shipmentService = $this->module->getModuleContainer('invertus.dpdbaltics.service.shipment_service');
        $response = $shipmentService->formatLabelShipmentPrintResponse($orderId);

        if (!$response['status'] || !$response['id_dpd_shipment']) {
            $this->addFlash('error', $response['message']);

            $redirectLink = ServerGlobalsUtility::getHttpReferer();

            if ($redirectLink) {
                return $this->redirect($_SERVER['HTTP_REFERER'], 302);
            }

            return $this->redirectToRoute('admin_orders_index');
        }
        $this->module->printLabel($response['id_dpd_shipment']);

        //This part should never be reached
        exit;
    }

    /**
     * Generates print multiple lables in order list
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     *
     * @return Response
     */
    public function printMultipleLabelsFromOrderListAction()
    {
        $orderIds = Tools::getValue('order_orders_bulk');

        if (!$orderIds) {
            $this->addFlash('error', $this->module->l('Could not print labels order id\'s are missing'));

            return $this->redirectToRoute('admin_orders_index');
        }
        $shipmentService = $this->module->getModuleContainer('invertus.dpdbaltics.service.shipment_service');
        $response = $shipmentService->formatMultipleLabelShipmentPrintResponse($orderIds);

        if (!$response) {
            $this->addFlash('error', $this->module->l('Could not print labels, bad response'));

            $redirectLink = ServerGlobalsUtility::getHttpReferer();

            if ($redirectLink) {
                return $this->redirect($_SERVER['HTTP_REFERER'], 302);
            }

            return $this->redirectToRoute('admin_orders_index');
        }
        $shipmentIds = json_decode($response['shipment_ids']);

        if (!$response['status'] || empty($shipmentIds)) {
            $this->addFlash('error', $response['message']);

            $redirectLink = ServerGlobalsUtility::getHttpReferer();

            if ($redirectLink) {
                return $this->redirect($_SERVER['HTTP_REFERER'], 302);
            }

            return $this->redirectToRoute('admin_orders_index');
        }
        $this->module->printMultipleLabels($shipmentIds);

        //This part should never be reached
        exit;
    }
}
