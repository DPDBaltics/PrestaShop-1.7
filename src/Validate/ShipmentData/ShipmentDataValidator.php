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


namespace Invertus\dpdBaltics\Validate\ShipmentData;

use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Validate\ShipmentData\Exception\InvalidShipmentDataField;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ShipmentDataValidator
{
    /**
     * @param ShipmentData $shipmentData
     *
     * @return array
     * @throws InvalidShipmentDataField
     */
    public function validate($shipmentData)
    {
        if (!$this->validateProduct($shipmentData->getProduct())) {
            throw new InvalidShipmentDataField(
                'Invalid shipment field \'Product\'',
                InvalidShipmentDataField::ERROR_MESSAGE_CODE_INVALID_PRODUCT
            );
        }

        return [];
    }

    /**
     * @param mixed $productValue
     *
     * @return bool
     */
    private function validateProduct($productValue)
    {
        return !empty($productValue);
    }
}
