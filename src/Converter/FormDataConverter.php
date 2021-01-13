<?php

namespace Invertus\dpdBaltics\Converter;

use Invertus\dpdBaltics\DTO\CollectionRequestData;
use Invertus\dpdBaltics\DTO\CourierRequestData;
use Invertus\dpdBaltics\DTO\ShipmentData;
use Symfony\Component\DependencyInjection\Container;

class FormDataConverter
{
    public function convertShipmentFormDataToShipmentObj($formData)
    {
        $shipmentData = new ShipmentData();
        foreach ($formData as $datum) {
            $setter = 'set' . ucfirst(Container::camelize($datum['name']));
            $setter = self::dashesToCamelCase($setter);
            if (method_exists($shipmentData, $setter)) {
                $shipmentData->$setter($datum['value']);
            }
        }

        return $shipmentData;
    }

    public function convertCollectionRequestFormDataToCollectionRequestObj($formData)
    {
        $collectionRequest = new CollectionRequestData();
        foreach ($formData as $key => $value) {
            $setter = 'set' . ucfirst(Container::camelize($key));
            $setter = self::dashesToCamelCase($setter);
            if (method_exists($collectionRequest, $setter)) {
                $collectionRequest->$setter($value);
            }
        }

        return $collectionRequest;
    }

    public function convertCourierRequestFormDataToCourierRequestObj($formData)
    {
        $courierRequest = new CourierRequestData();
        foreach ($formData as $key => $value) {
            $setter = 'set' . ucfirst(Container::camelize($key));
            $setter = self::dashesToCamelCase($setter);
            if (method_exists($courierRequest, $setter)) {
                $courierRequest->$setter($value);
            }
        }

        return $courierRequest;
    }

    private static function dashesToCamelCase($id)
    {
        return strtr(ucwords(strtr($id, ['-' => ' ', '.' => '- ', '\\' => '- '])), [' ' => '']);
    }
}
