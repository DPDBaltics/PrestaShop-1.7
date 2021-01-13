<?php

namespace Invertus\dpdBaltics\Controller;

use Invertus\dpdBaltics\Service\ShipmentService;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
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
     * Generates print label PDF for given dpd carrier order
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     *
     * @return Response
     */
    public function printLabelAction($orderId)
    {
        $shipmentService = $this->module->getModuleContainer(ShipmentService::class);
        $response = $shipmentService->formatLabelShipmentPrintResponse($orderId);

        if (!$response['status'] || !$response['id_dpd_shipment']) {
            $this->addFlash('error', $response['message']);

            return $this->redirectToRoute('admin_orders_index');
        }
        $this->module->printLabel($response['id_dpd_shipment']);

        //This part should never be reached
        exit;
    }

    /**
     * Generates print labels PDF for given dpd carrier orders
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))", redirectRoute="admin_orders_index")
     *
     * @param int $orderId
     *
     * @return Response
     */
    public function printMultipleLabelsAction()
    {
        $orderIds = Tools::getValue('order_orders_bulk');

        if (!$orderIds) {
            $this->addFlash('error', $this->module->l('Could not print labels order id\'s are missing'));

            return $this->redirectToRoute('admin_orders_index');
        }
        $shipmentService = $this->module->getModuleContainer(ShipmentService::class);
        $response = $shipmentService->formatMultipleLabelShipmentPrintResponse($orderIds);

        if (!$response) {
            $this->addFlash('error', $this->module->l('Could not print labels, bad response'));

            return $this->redirectToRoute('admin_orders_index');
        }
        $shipmentIds = json_decode($response['shipment_ids']);

        if (!$response['status'] || empty($shipmentIds)) {
            $this->addFlash('error', $response['message']);

            return $this->redirectToRoute('admin_orders_index');
        }
        $this->module->printMultipleLabels($shipmentIds);

        //This part should never be reached
        exit;
    }
}
