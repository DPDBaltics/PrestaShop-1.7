<?php

namespace Invertus\dpdBaltics\Provider;

use Address;
use Carrier;
use Currency;
use DPDBaltics;
use DPDPriceRule;
use DPDZone;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\ZoneRepository;
use Product;
use Tools;

class ProductShippingCostProvider
{
    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ZoneRepository
     */
    private $zoneRepository;

    /**
     * @var PriceRuleRepository
     */
    private $priceRuleRepository;

    /**
     * @var Currency
     */
    private $currency;

    public function __construct(
        DPDBaltics $module,
        ProductRepository $productRepository,
        ZoneRepository $zoneRepository,
        PriceRuleRepository $priceRuleRepository,
        Currency $currency
    ) {
        $this->module = $module;
        $this->productRepository = $productRepository;
        $this->zoneRepository = $zoneRepository;
        $this->priceRuleRepository = $priceRuleRepository;
        $this->currency = $currency;
    }

    public function getProductShippingCost($carrierReference, $idAddress)
    {
        // This method is still called when module is disabled so we need to do a manual check here
        if (!$this->module->active) {
            return false;
        }

        $carrier = Carrier::getCarrierByReference($carrierReference);

        $deliveryAddress = new Address($idAddress);

        $carrierZones = $this->zoneRepository->findZonesIdsByCarrierReference($carrier->id_reference);

        $serviceCarrier = $this->productRepository->findProductByCarrierReference($carrier->id_reference);

        if ((bool) $serviceCarrier['is_home_collection']) {
            return false;
        }

        if ($idAddress && !DPDZone::checkAddressInZones($deliveryAddress, $carrierZones)) {
            return false;
        }

        // Get all price rules for current carrier
        $priceRulesIds = $this->priceRuleRepository->getByCarrierReference($deliveryAddress, $carrier->id_reference);

        $currentProduct = new Product(Tools::getValue('id_product'));
        $price = $currentProduct->price;
        $currency = $this->currency->id;
        $weight = $currentProduct->weight;

        if ($idAddress) {
            foreach ($priceRulesIds as $priceRuleId) {
                $priceRule = new DPDPriceRule($priceRuleId, null);

                // Check if price rule is applicable for this cart
                if ($priceRule->isApplicableForProduct($price, $currency, $weight, $idAddress)) {
                    // If it's applicable - use price rule's price and don't check other price rules
                    return Tools::convertPrice($priceRule->price, $currency);
                }
            }
        } else {
            $priceRulesPriceArray = [];

            foreach ($priceRulesIds as $priceRuleId) {
                $priceRule = new DPDPriceRule($priceRuleId, null);

                $priceRulesPriceArray[] = (float) $priceRule->price;
            }

            return !empty($priceRulesPriceArray) ? min($priceRulesPriceArray) : $priceRulesPriceArray;
        }

        return false;
    }
}
