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


namespace Invertus\dpdBaltics\Repository;

use DbQuery;
use DPDParcel;
use DPDShipment;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\DTO\ShipmentData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ShipmentRepository extends AbstractEntityRepository
{
    public function getIdByOrderId($idOrder)
    {
        $query = new DbQuery();
        $query->select('id_dpd_shipment');

        $query->from('dpd_shipment', 's');
        $query->where('s.id_order = '.(int)$idOrder);

        $result = $this->db->getValue($query);

        return $result;
    }

    public function hasAnyShipments($idOrder)
    {
        $shipments = $this->getIdByOrderId($idOrder);

        return !empty($shipments);
    }

    public function saveShipment(ShipmentData $shipmentData, $shipmentId)
    {
        $shipment = new DPDShipment($shipmentId);
        $shipment->id_contract = $shipmentData->getProduct();
        $shipment->reference1 = $shipmentData->getReference1();
        $shipment->reference2 = $shipmentData->getReference2();
        $shipment->reference3 = $shipmentData->getReference3();
        $shipment->reference4 = $shipmentData->getReference4();
        $shipment->date_shipment = $shipmentData->getDateShipment();
        $shipment->label_format = $shipmentData->getLabelFormat();
        $shipment->label_position = $shipmentData->getLabelPosition();
        $shipment->weight = $shipmentData->getWeight();
        $shipment->num_of_parcels = $shipmentData->getParcelAmount();
        $shipment->goods_price = $shipmentData->getGoodsPrice();
        $shipment->id_service = $shipmentData->getProduct();
        $shipment->is_document_return_enabled = $shipmentData->isDpdDocumentReturn();
        $shipment->document_return_number = $shipmentData->getDpdDocumentReturnNumber();

        $shipment->update();
    }
}
