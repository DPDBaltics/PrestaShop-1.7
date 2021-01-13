<?php

namespace Invertus\dpdBaltics\Validate\ShipmentData;

use Invertus\dpdBaltics\DTO\ShipmentData;
use Invertus\dpdBaltics\Validate\ShipmentData\Exception\InvalidShipmentDataField;

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
