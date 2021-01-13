<?php

namespace Invertus\dpdBaltics\Service\Import;

use Configuration;
use DPDBaltics;
use DPDPriceRule;
use Exception;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\CarrierRepository;
use Invertus\dpdBaltics\Repository\DPDZoneRepository;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use Module;
use PaymentModule;
use Validate;

class PriceRulesImport implements ImportableInterface
{
    const FILE_NAME = 'PriceRulesImport';

    /**
     * Field position in row
     */
    const POSITION_CUSTOMER_TYPE = 0;
    const POSITION_NAME = 1;
    const POSITION_ORDER_PRICE_FROM = 2;
    const POSITION_ORDER_PRICE_TO = 3;
    const POSITION_WEIGHT_FROM = 4;
    const POSITION_WEIGHT_TO = 5;
    const POSITION_ALL_CARRIERS = 6;
    const POSITION_CARRIERS = 7;
    const POSITION_ALL_ZONES = 8;
    const POSITION_ZONES = 9;
    const POSITION_ALL_SHOPS = 10;
    const POSITION_SHOPS = 11;
    const POSITION_PRICE = 12;
    const POSITION_ALL_PAYMENTS = 13;
    const POSITION_PAYMENTS = 14;
    const POSITION_ACTIVE = 15;

    const COLUMN_NUM = 16;

    /**
     * @var array   Import errors
     */
    private $errors = [];

    /**
     * @var array
     */
    private $warnings = [];

    /**
     * @var int
     */
    private $importedRowsCount = 0;
    /**
     * @var DPDBaltics
     */
    private $module;
    /**
     * @var PriceRuleRepository
     */
    private $priceRuleRepository;
    /**
     * @var DPDZoneRepository
     */
    private $DPDZoneRepository;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    public function __construct(
        DPDBaltics $module,
        PriceRuleRepository $priceRuleRepository,
        DPDZoneRepository $DPDZoneRepository,
        CarrierRepository $carrierRepository
    ) {
        $this->module = $module;
        $this->priceRuleRepository = $priceRuleRepository;
        $this->DPDZoneRepository = $DPDZoneRepository;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function importRows(array $rows)
    {
        $this->validate($rows);

        if ($this->hasErrors()) {
            return;
        }

        $warning = [];

        foreach ($rows as $row) {
            $skiped = false;

            $priceRule = $this->createPriceRule($row);
            $this->priceRuleRepository->deletePriceRuleShops($priceRule->id);
            if (!$priceRule) {
                $skiped = true;
                continue;
            }

            $zones = $this->collectZones($row, $priceRule);
            $shops = $this->collectShops($row, $priceRule);
            $payments = $this->collectPayments($row, $priceRule);
            $carriers = $this->collectCarriers($row, $priceRule);

            // if one of required mappings is empty
            // then remove price rule and skip to next
            if (empty($zones) || empty($shops) || empty($payments) || empty($carriers)) {
                $priceRule->delete();
                $skiped = true;

                if (empty($zones)) {
                    if (!isset($warning['empty_zones'])) {
                        $warning['empty_zones'] = 0;
                    }

                    $warning['empty_zones'] += 1;
                }

                if (empty($shops)) {
                    if (!isset($warning['empty_shops'])) {
                        $warning['empty_shops'] = 0;
                    }

                    $warning['empty_shops'] += 1;
                }

                if (empty($payments)) {
                    if (!isset($warning['empty_payments'])) {
                        $warning['empty_payments'] = 0;
                    }

                    $warning['empty_payments'] += 1;
                }

                if (empty($carriers)) {
                    if (!isset($warning['empty_carriers'])) {
                        $warning['empty_carriers'] = 0;
                    }

                    $warning['empty_carriers'] += 1;
                }

                continue;
            }

            foreach ($zones as $zone) {
                try {
                    $this->priceRuleRepository->addPriceRuleZone($zone);
                } catch (Exception $e) {
                    $this->warnings[] =
                        $this->module->l(sprintf('Failed to add price rule zone in row: %s', self::FILE_NAME, $row[self::POSITION_NAME]));
                }
            }

            foreach ($shops as $shop) {
                try {
                    $this->priceRuleRepository->addPriceRuleShop($shop);
                } catch (Exception $e) {
                    $this->warnings[] =
                        $this->module->l(sprintf('Failed to add price rule shop in row: %s', self::FILE_NAME, $row[self::POSITION_NAME]));
                }
            }

            foreach ($payments as $payment) {
                try {
                    $this->priceRuleRepository->addPriceRulePayment($payment);
                } catch (Exception $e) {
                    $this->warnings[] =
                        $this->module->l(sprintf('Failed to add price rule payment in row: %s', self::FILE_NAME, $row[self::POSITION_NAME]));
                }
            }

            foreach ($carriers as $carrier) {
                try {
                    $this->priceRuleRepository->addPriceRuleCarrier($carrier);
                } catch (Exception $e) {
                    $this->warnings[] =
                        $this->module->l(sprintf('Failed to add price rule carrier in row: %s', self::FILE_NAME, $row[self::POSITION_NAME]));
                }
            }
        }

        if (!$skiped) {
            $this->importedRowsCount++;
        }

        if ($warning) {
            if (isset($warning['empty_zones'])) {
                $this->warnings[] = sprintf(
                    $this->module->l('Skipped %d price rules import in %s shop due to missing zones', self::FILE_NAME),
                    $row[self::POSITION_NAME],
                    $warning['empty_zones']
                );
            }

            if (isset($warning['empty_payments'])) {
                $this->warnings[] = sprintf(
                    $this->module->l('Skipped %d price rules import in %s shop due to missing payments', self::FILE_NAME),
                    $row[self::POSITION_NAME],
                    $warning['empty_payments']
                );
            }

            if (isset($warning['empty_carriers'])) {
                $this->warnings[] = sprintf(
                    $this->module->l('Skipped %d price rules import in %s shop due to missing carriers', self::FILE_NAME),
                    $row[self::POSITION_NAME],
                    $warning['empty_carriers']
                );
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmations()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function getImportedRowsCount()
    {
        return $this->importedRowsCount;
    }

    /**
     * {@inheritdoc}
     */
    public function useTransaction()
    {
    }

    /**
     * Validate data before import
     *
     * @param array $rows
     */
    private function validate(array $rows)
    {
        $rowNum = 0;
        foreach ($rows as $row) {
            ++$rowNum;

            if (count($row) != self::COLUMN_NUM) {
                $this->errors[] = sprintf(
                    $this->module->l('Invalid column number in row %s', self::FILE_NAME),
                    $rowNum
                );
                continue;
            }

            $name = $row[self::POSITION_NAME];
            if (empty($name)) {
                $this->errors[] = sprintf(
                    $this->module->l('Invalid price rule name in row %s', self::FILE_NAME),
                    $rowNum
                );
                continue;
            }

            $customerType = $row[self::POSITION_CUSTOMER_TYPE];
            if (!in_array($customerType, [
                DPDPriceRule::CUSTOMER_TYPE_ALL,
                DPDPriceRule::CUSTOMER_TYPE_COMPANY,
                DPDPriceRule::CUSTOMER_TYPE_REGULAR,
            ])) {
                $this->errors[] = sprintf(
                    $this->module->l('Invalid customer type in row %s', self::FILE_NAME),
                    $rowNum
                );
                continue;
            }

            $orderPriceTo = $row[self::POSITION_ORDER_PRICE_TO];
            $orderPriceFrom = $row[self::POSITION_ORDER_PRICE_FROM];
            if (!is_numeric($orderPriceTo) || !is_numeric($orderPriceFrom) || $orderPriceFrom > $orderPriceTo) {
                $this->errors[] = sprintf(
                    $this->module->l('Invalid order price ranges in row %s', self::FILE_NAME),
                    $rowNum
                );
                continue;
            }

            $weightTo = $row[self::POSITION_WEIGHT_TO];
            $weightFrom = $row[self::POSITION_WEIGHT_FROM];
            if (!is_numeric($weightTo) || !is_numeric($weightFrom) || $weightFrom > $weightTo) {
                $this->errors[] = sprintf(
                    $this->module->l('Invalid weight ranges in row %s', self::FILE_NAME),
                    $rowNum
                );
                continue;
            }

            $price = $row[self::POSITION_PRICE];
            if (!is_numeric($price)) {
                $this->errors[] = sprintf(
                    $this->module->l('Invalid price in row %s', self::FILE_NAME),
                    $rowNum
                );
                continue;
            }

            $allZones = (bool)$row[self::POSITION_ALL_ZONES];
            if (!$allZones) {
                $zones = $row[self::POSITION_ZONES];
                if (empty($zones)) {
                    $this->errors[] = sprintf(
                        $this->module->l('Missing zones in row %s', self::FILE_NAME),
                        $rowNum
                    );
                    continue;
                }
            }

            $allShops = (bool)$row[self::POSITION_ALL_SHOPS];
            if (!$allShops) {
                $shops = $row[self::POSITION_SHOPS];
                if (empty($shops)) {
                    $this->errors[] = sprintf(
                        $this->module->l('Missing shops in row %s', self::FILE_NAME),
                        $rowNum
                    );
                    continue;
                }
            }

            $allPayments = (bool)$row[self::POSITION_ALL_PAYMENTS];
            if (!$allPayments) {
                $payments = $row[self::POSITION_PAYMENTS];
                if (empty($payments)) {
                    $this->errors[] = sprintf(
                        $this->module->l('Missing payments in row %s', self::FILE_NAME),
                        $rowNum
                    );
                    continue;
                }
            }

            $allCarriers = (bool)$row[self::POSITION_ALL_CARRIERS];
            if (!$allCarriers) {
                $carriers = $row[self::POSITION_CARRIERS];
                if (empty($carriers)) {
                    $this->errors[] = sprintf(
                        $this->module->l('Missing carriers in row %s', self::FILE_NAME),
                        $rowNum
                    );
                    continue;
                }
            }
        }
    }

    /**
     * Create new price rule object from row
     *
     * @param array $row
     * @return DPDPriceRule
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createPriceRule(array $row)
    {
        $priceRule = new DPDPriceRule();
        $priceRule->active = (int)$row[self::POSITION_ACTIVE];
        $priceRule->customer_type = $row[self::POSITION_CUSTOMER_TYPE];
        $priceRule->name = $row[self::POSITION_NAME];
        $priceRule->order_price_from = $row[self::POSITION_ORDER_PRICE_FROM];
        $priceRule->order_price_to = $row[self::POSITION_ORDER_PRICE_TO];
        $priceRule->weight_from = $row[self::POSITION_WEIGHT_FROM];
        $priceRule->weight_to = $row[self::POSITION_WEIGHT_TO];
        $priceRule->price = (float)$row[self::POSITION_PRICE];

        $priceRule->save();

        return $priceRule;
    }

    /**
     * Collect zones for price rule import
     *
     * @param array $row
     * @param DPDPriceRule $priceRule
     * @return array    Array of zones to import
     * @throws Exception
     */
    private function collectZones(array $row, DPDPriceRule $priceRule)
    {
        $allZones = (bool)$row[self::POSITION_ALL_ZONES];
        if ($allZones) {
            return [
                [
                    'id_dpd_price_rule' => $priceRule->id,
                    'id_dpd_zone' => 0,
                    'all_zones' => 1,
                ],
            ];
        }

        $multiValueSeparator = Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR);

        $zones = [];
        $zoneNames = explode($multiValueSeparator, $row[self::POSITION_ZONES]);

        foreach ($zoneNames as $zoneName) {
            if (empty($zoneName)) {
                continue;
            }

            $zoneId = $this->DPDZoneRepository->getByName($zoneName);

            if (!$zoneId) {
                continue;
            }

            $zones[] = [
                'id_dpd_zone' => $zoneId,
                'id_dpd_price_rule' => $priceRule->id,
                'all_zones' => 0,
            ];
        }

        return $zones;
    }

    /**
     * Collect zones for price rule import
     *
     * @param array $row
     * @param DPDPriceRule $priceRule
     * @return array    Array of zones to import
     * @throws Exception
     */
    private function collectShops(array $row, DPDPriceRule $priceRule)
    {
        $shops = [];
        $allZones = (bool)$row[self::POSITION_ALL_SHOPS];
        if ($allZones) {
            return [
                [
                    'id_dpd_price_rule' => $priceRule->id,
                    'id_shop' => 0,
                    'all_shops' => 1,
                ],
            ];
        }

        $multiValueSeparator = Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR);

        $shopIds = explode($multiValueSeparator, $row[self::POSITION_SHOPS]);

        foreach ($shopIds as $shopId) {
            if (empty($shopId)) {
                continue;
            }

            $shops[] = [
                'id_shop' => $shopId,
                'id_dpd_price_rule' => $priceRule->id,
                'all_shops' => 0,
            ];
        }

        return $shops;
    }

    /**
     * Collect payment methods for price rule
     *
     * @param array $row
     * @param DPDPriceRule $priceRule
     * @return array    Array of payment method mappings
     */
    private function collectPayments(array $row, DPDPriceRule $priceRule)
    {
        $allPayments = (bool)$row[self::POSITION_ALL_PAYMENTS];
        if ($allPayments) {
            return [
                [
                    'id_dpd_price_rule' => $priceRule->id,
                    'id_module' => 0,
                    'all_payments' => 1,
                ],
            ];
        }

        $payments = [];
        $multiValueSeparator = Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR);
        $paymentNames = explode($multiValueSeparator, $row[self::POSITION_PAYMENTS]);
        foreach ($paymentNames as $paymentName) {
            if (empty($paymentName)) {
                continue;
            }

            $paymentName = $this->changePaymentModuleName($paymentName);
            $paymentModule = Module::getInstanceByName($paymentName);
            if (Validate::isLoadedObject($paymentModule) && $paymentModule instanceof PaymentModule) {
                $payments[] = [
                    'id_dpd_price_rule' => $priceRule->id,
                    'id_module' => $paymentModule->id,
                    'all_payments' => 0,
                ];
            }
        }

        return $payments;
    }

    /**
     * Checks if payment name is given from PS1.6 version and if yes changes it to PS1.7 name
     *
     * @param $paymentName
     * @return mixed
     */
    private function changePaymentModuleName($paymentName)
    {
        $paymentModuleNames = [
            'cheque' => 'ps_checkpayment',
            'bankwire' => 'ps_wirepayment'
        ];

        if (array_key_exists($paymentName, $paymentModuleNames)) {
            return $paymentModuleNames[$paymentName];
        }

        return $paymentName;
    }

    /**
     * Collect carriers for price rule
     *
     * @param array $row
     * @param DPDPriceRule $priceRule
     * @return array
     */
    private function collectCarriers(array $row, DPDPriceRule $priceRule)
    {
        $allCarriers = (bool)$row[self::POSITION_ALL_CARRIERS];
        if ($allCarriers) {
            return [
                [
                    'id_dpd_price_rule' => $priceRule->id,
                    'id_reference' => 0,
                    'all_carriers' => 1,
                ],
            ];
        }

        $carriers = [];
        $multiValueSeparator = Configuration::get(Config::IMPORT_FIELD_MULTIPLE_SEPARATOR);
        $carrierNames = explode($multiValueSeparator, $row[self::POSITION_CARRIERS]);

        foreach ($carrierNames as $carrierName) {
            if (empty($carrierName)) {
                continue;
            }

            $carrierId = $this->carrierRepository->findCarrierIdByName($carrierName);
            if (!$carrierId) {
                continue;
            }

            $carriers[] = [
                'id_dpd_price_rule' => $priceRule->id,
                'id_reference' => $carrierId,
                'all_carriers' => 0,
            ];
        }

        return $carriers;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOldData()
    {
        $this->priceRuleRepository->deleteOldData();
    }
}