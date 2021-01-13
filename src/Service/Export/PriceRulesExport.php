<?php

namespace Invertus\dpdBaltics\Service\Export;

use Carrier;
use Configuration;
use DPDBaltics;
use DPDPriceRule;
use DPDZone;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use PaymentModule;
use Validate;

/**
 * Class DPDPriceRulesExport is responsible for providing price rules data that needs to be exported
 */
class PriceRulesExport implements ExportableInterface
{
    const FILE_NAME = 'PriceRulesExport';

    private $priceRulesIds = [];
    
    /**
     * @var PriceRuleRepository
     */
    private $priceRuleRepository;
    
    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(
        PriceRuleRepository $priceRuleRepository,
        DPDBaltics $module
    ) {
        $this->priceRuleRepository = $priceRuleRepository;
        $this->module = $module;
    }

    /**
     * {@inheritdoc}
     */
    public function getRows()
    {
        $rows = [];

        $separator = Configuration::get(Config::EXPORT_FIELD_MULTIPLE_SEPARATOR);

        $priceRules = $this->priceRuleRepository->findAllPriceRuleIds();


        foreach ($priceRules as $priceRuleId) {
            $priceRule = new DPDPriceRule($priceRuleId);

            // collect data for carriers
            $carrierNames = [];
            $implodedCarrierNames = '';
            $isAllCarriers = $this->priceRuleRepository->findIsAllCarriersAssigned($priceRule->id);
            if (!$isAllCarriers) {
                $carrierReferences = $this->priceRuleRepository->findAllCarriersAssigned($priceRule->id);
                foreach ($carrierReferences as $carrierReference) {
                    $carrier = Carrier::getCarrierByReference($carrierReference);
                    if ($carrier) {
                        $carrierNames[] = $carrier->name;
                    }
                }
                $implodedCarrierNames = implode($separator, $carrierNames);
            }

            // collect data for zones
            $zoneNames = [];
            $implodedZoneNames = '';
            $isAllZones = $this->priceRuleRepository->findIsAllZonesAssigned($priceRuleId);
            if (!$isAllZones) {
                $zonesIds = $this->priceRuleRepository->findAllZonesAssigned($priceRule->id);
                foreach ($zonesIds as $zoneId) {
                    $zone = new DPDZone($zoneId);
                    if (Validate::isLoadedObject($zone)) {
                        $zoneNames[] = $zone->name;
                    }
                }
                $implodedZoneNames = implode($separator, $zoneNames);
            }

            // collect data for shops
            $implodedShopIds = '';
            $isAllShops = $this->priceRuleRepository->findIsAllShopsAssigned($priceRule->id);
            if (!$isAllShops) {
                $ShopsIds = $this->priceRuleRepository->findAllShopsAssigned($priceRule->id);
                $implodedShopIds = implode($separator, $ShopsIds);
            }

            // collect data for payments
            $paymentNames = [];
            $implodedPaymentNames = '';
            $isAllPayments = $this->priceRuleRepository->findIsAllPaymentsAssigned($priceRule->id);
            if (!$isAllPayments) {
                $paymentIds = $this->priceRuleRepository->findAllPaymentsAssigned($priceRule->id);
                foreach ($paymentIds as $paymentId) {
                    $this->module = PaymentModule::getInstanceById($paymentId);
                    $paymentNames[] = $this->module->name;
                }
                $implodedPaymentNames = implode($separator, $paymentNames);
            }

            // final row data
            $row = [
                $priceRule->customer_type,
                $priceRule->name,
                $priceRule->order_price_from,
                $priceRule->order_price_to,
                $priceRule->weight_from,
                $priceRule->weight_to,
                (int)$isAllCarriers,
                $implodedCarrierNames,
                (int)$isAllZones,
                $implodedZoneNames,
                (int)$isAllShops,
                $implodedShopIds,
                $priceRule->price,
                (int)$isAllPayments,
                $implodedPaymentNames,
                (int)$priceRule->active,
            ];

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return sprintf(Config::IMPORT_EXPORT_OPTION_PRICE_RULES . '_%s.csv', date('Y-m-d_His'));
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return [
            $this->module->l('Customer type', self::FILE_NAME),
            $this->module->l('Name', self::FILE_NAME),
            $this->module->l('Order price from', self::FILE_NAME),
            $this->module->l('Order price to', self::FILE_NAME),
            $this->module->l('Weight from', self::FILE_NAME),
            $this->module->l('Weight to', self::FILE_NAME),
            $this->module->l('All carriers', self::FILE_NAME),
            $this->module->l('Carriers', self::FILE_NAME),
            $this->module->l('All zones', self::FILE_NAME),
            $this->module->l('Zones', self::FILE_NAME),
            $this->module->l('All shops', self::FILE_NAME),
            $this->module->l('Shops', self::FILE_NAME),
            $this->module->l('Price', self::FILE_NAME),
            $this->module->l('All payments', self::FILE_NAME),
            $this->module->l('Payments', self::FILE_NAME),
            $this->module->l('Active', self::FILE_NAME),
        ];
    }

    /**
     * checks if has errors
     * @return bool
     */
    public function hasErrors()
    {
        return false;
    }

    /**
     * gets array of errors
     * @return array
     */
    public function getErrors()
    {
        return [];
    }
}
