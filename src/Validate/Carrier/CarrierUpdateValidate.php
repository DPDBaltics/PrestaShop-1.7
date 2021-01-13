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

namespace Invertus\dpdBaltics\Validate\Carrier;

use Configuration;
use DPDBaltics;
use Invertus\dpdBaltics\Exception\ProductUpdateException;
use Language;
use Validate;

class CarrierUpdateValidate
{
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    public function validateCarrierName($carrierName)
    {
        if (!Validate::isCarrierName($carrierName)) {
            throw new ProductUpdateException($this->module->l('Carrier name is incorrect'));
        }

        if (!isset($carrierName) || '' == $carrierName) {
            throw new ProductUpdateException($this->module->l('Carrier name must not be empty'));
        }
    }

    public function validateCarrierDeliveryTime($deliveryTime)
    {
        $defaultLanguage = new Language(Configuration::get('PS_LANG_DEFAULT'));

        $defaultDeliveryTimeValue = $deliveryTime[$defaultLanguage->id];

        if (!isset($defaultDeliveryTimeValue) || '' == $defaultDeliveryTimeValue) {
            throw new ProductUpdateException(
                sprintf(
                    $this->module->l('The delivery time is required at least in %s.'),
                    $defaultLanguage->name
                )
            );
        }
    }
}
