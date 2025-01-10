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


namespace Invertus\dpdBaltics\Converter;

use Invertus\dpdBaltics\DTO\CollectionRequestData;
use Invertus\dpdBaltics\DTO\CourierRequestData;
use Invertus\dpdBaltics\DTO\ShipmentData;
use Symfony\Component\DependencyInjection\Container;

if (!defined('_PS_VERSION_')) {
    exit;
}


class FormDataConverter
{
    public function convertShipmentFormDataToShipmentObj($formData)
    {
        $shipmentData = new ShipmentData();
        if (!$formData) {
            return $shipmentData;
        }
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
