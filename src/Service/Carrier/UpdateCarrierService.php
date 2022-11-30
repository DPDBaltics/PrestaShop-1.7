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



namespace Invertus\dpdBaltics\Service\Carrier;

use Carrier;
use DPDProduct;
use Exception;
use Invertus\dpdBaltics\Exception\ProductUpdateException;
use Invertus\dpdBaltics\Service\LanguageService;
use Invertus\dpdBaltics\Validate\Carrier\CarrierUpdateValidate;
use Language;

class UpdateCarrierService
{
    /**
     * @var LanguageService
     */
    private $languageService;
    /**
     * @var CarrierUpdateValidate
     */
    private $carrierUpdateValidate;

    public function __construct(LanguageService $languageService, CarrierUpdateValidate $carrierUpdateValidate)
    {
        $this->languageService = $languageService;
        $this->carrierUpdateValidate = $carrierUpdateValidate;
    }

    /**
     * @param $productId
     * @param $params
     *
     * @throws ProductUpdateException
     */
    public function updateCarrier($productId, $params)
    {
        try {
            $dpdProduct = new DPDProduct($productId);

            $languages = $this->languageService->getShopLanguagesIsoCodes();

            $deliveryTime = [];

            foreach ($languages as $language) {
                $deliveryTime[Language::getIdByIso($language)] =  $params['delivery-time-' . $language];
            }

            $this->carrierUpdateValidate->validateCarrierName($params['carrier-name']);
            $this->carrierUpdateValidate->validateCarrierDeliveryTime($deliveryTime);

            $carrier = Carrier::getCarrierByReference($dpdProduct->id_reference);
            $carrier->delay = $deliveryTime;
            $carrier->name = $params['carrier-name'];
            $carrier->active = $dpdProduct->active;
            $carrier->update();
        } catch (Exception $e) {
            throw new ProductUpdateException($e->getMessage());
        };
    }

    /**
     * @param int $productId
     * @param $name
     *
     * @throws ProductUpdateException
     */
    public function updateCarrierName($productId, $name)
    {
        try {
            $dpdProduct = new DPDProduct($productId);

            $this->carrierUpdateValidate->validateCarrierName($name);
            $carrier = Carrier::getCarrierByReference($dpdProduct->id_reference);

            if (!$carrier) {
                return;
            }
            $carrier->name = $name;
            $carrier->update();
        } catch (Exception $e) {
            throw new ProductUpdateException($e->getMessage());
        }
    }
}
