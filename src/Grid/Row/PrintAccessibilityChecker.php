<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\Grid\Row;

use Invertus\dpdBaltics\Repository\ShipmentRepository;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;

final class PrintAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    public function __construct(ShipmentRepository $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(array $record)
    {
        if (!$this->shipmentRepository->getIdByOrderId($record['id_order'])) {
            return false;
        }

        return true;
    }
}
