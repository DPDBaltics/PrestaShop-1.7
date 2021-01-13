<?php

namespace Invertus\dpdBaltics\Service;

use Carrier;
use Cart;
use DPDBaltics;
use DPDPriceRule;
use Invertus\dpdBaltics\Repository\CarrierRepository;
use Invertus\dpdBaltics\Repository\DPDZoneRepository;
use Invertus\dpdBaltics\Repository\PaymentRepository;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\ShopRepository;
use Language;
use Module;
use Shop;
use Smarty;
use Tools;
use Validate;

class PriceRuleService
{

    const FILE_NAME = 'PriceRuleService';

    /**
     * @var PriceRuleRepository
     */
    private $priceRuleRepository;

    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var DPDZoneRepository
     */
    private $zoneRepository;
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var Smarty
     */
    private $smarty;
    /**
     * @var Language
     */
    private $language;
    /**
     * @var Shop
     */
    private $shop;
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    public function __construct(
        DPDBaltics $module,
        PriceRuleRepository $priceRuleRepository,
        ProductRepository $productRepository,
        DPDZoneRepository $zoneRepository,
        PaymentRepository $paymentRepository,
        CarrierRepository $carrierRepository,
        ShopRepository $shopRepository,
        Smarty $smarty,
        Language $language,
        Shop $shop
    ) {
        $this->priceRuleRepository = $priceRuleRepository;
        $this->module = $module;
        $this->productRepository = $productRepository;
        $this->zoneRepository = $zoneRepository;
        $this->paymentRepository = $paymentRepository;
        $this->carrierRepository = $carrierRepository;
        $this->smarty = $smarty;
        $this->language = $language;
        $this->shop = $shop;
        $this->shopRepository = $shopRepository;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function loadCarriers(DPDPriceRule $priceRule)
    {
        if (empty($this->errors)) {
            $result = $this->getCarriersFromRepository($priceRule);
        } else {
            $carriers = (array)Tools::getValue('dpd-carrier');
            if (empty($carriers)) {
                return $this->getCarriersFromRepository($priceRule);
            }
            $allCarriers = (array)$this->getCarriersFromRepository($priceRule);
            foreach ($allCarriers as $key => $carrier) {
                if (!isset($carriers[$carrier['id_carrier']])) {
                    $allCarriers[$key]['selected'] = false;
                }
            }
            $result = $allCarriers;
        }

        $this->smarty->assign([
            'carriers' => $result,
            'allCarriersChecked' => (!$priceRule->id) ? true :
                (int)$this->priceRuleRepository->getAllFlag(
                    'dpd_price_rule_carrier',
                    $priceRule->id
                )
        ]);
    }

    public function getCarriersFromRepository(DPDPriceRule $priceRule)
    {
        $result = (array) $this->carrierRepository->getAllPriceRuleCarriers(
            (int) $priceRule->id,
            (int) $this->shop->id,
            (Validate::isLoadedObject($priceRule))? false : true,
            $this->language->id
        );

        foreach ($result as $key => &$carrier) {
            $carrier['tooltip'] = [
                $this->module->l('Delay:', self::FILE_NAME) => $carrier['delay'],
            ];
        }

        return $result;
    }

    public function getPaymentsFromRepository(DPDPriceRule $priceRule)
    {
        $result = (array) $this->paymentRepository->getAllPriceRulePaymentMethods(
            (int) $priceRule->id,
            (Validate::isLoadedObject($priceRule))? false : true
        );

        foreach ($result as $key => $item) {
            $idModule = $item['id_module'];
            $module = Module::getInstanceById($idModule);

            if ($module) {
                $result[$key]['name'] = $module->displayName;
            } else {
                unset($result[$key]);
            }
        }

        return $result;
    }

    public function loadPayments(DPDPriceRule $priceRule)
    {
        $result = [];
        if (empty($this->errors)) {
            $result = $this->getPaymentsFromRepository($priceRule);
        } elseif (!empty($this->errors)) {
            $payments = (array) Tools::getValue('dpd-payment-method');
            if (empty($payments)) {
                $this->getPaymentsFromRepository($priceRule);
            }
            $availablePayments = (array) $this->getPaymentsFromRepository($priceRule);
            foreach ($availablePayments as $key => $availablePayment) {
                if (!isset($payments[$availablePayment['id_module']])) {
                    $availablePayments[$key]['selected'] = false;
                }
            }
            $result = $availablePayments;
        }

        $this->smarty->assign([
            'paymentMethods' => $result,
            'allPaymentsChecked' => (!$priceRule->id)? true :
                (int) $this->priceRuleRepository->getAllFlag(
                    'dpd_price_rule_payment',
                    $priceRule->id
                )
        ]);
    }


    public function validatePriceRanges($priceFrom, $priceTo)
    {
        if ((float) $priceFrom > (float) $priceTo) {
            $this->errors[] = $this->module->l('Order price from can not be higher than price to', self::FILE_NAME);
            unset($_POST['order_price_from']);
        }
    }

    public function validateWeightRanges($weightFrom, $weightTo)
    {
        if ((float) $weightFrom > (float) $weightTo) {
            $this->errors[] = $this->module->l('Weight from can not be higher than weight to', self::FILE_NAME);
            unset($_POST['weight_from']);
        }

        if ((float) $weightFrom == (float) $weightTo) {
            $this->errors[] = $this->module->l('Weight from can not be equal to weight to', self::FILE_NAME);
            unset($_POST['weight_from']);
            unset($_POST['weight_to']);
        }
    }


    public function addUpdateCarriers($object)
    {
        $dpdCarriers = (array) Tools::getValue('dpd-carrier');
        $checkedAll = 0;
        if (Tools::getValue('checkme_carrier')) {
            $checkedAll = 1;
        }
        if (empty($dpdCarriers)) {
            return true;
        }

        $carrierIds = [];

        foreach ($dpdCarriers as $carrierId) {
            $carrier = new Carrier($carrierId);

            if (Validate::isLoadedObject($carrier)) {
                $carrierIds[] = $carrier->id;
            }
        }

        if (empty($carrierIds)) {
            return true;
        }

        if (!$this->productRepository->updateProductsForPriceRule((int) $object->id, $dpdCarriers, $checkedAll)) {
            $this->errors[] = $this->module->l('Failed to add or update carriers', self::FILE_NAME);
            return false;
        }
        return true;
    }

    public function addUpdateZones($object)
    {
        $selectedZones = Tools::getValue('zones_select');
        $allSelected = false;

        foreach ($selectedZones as $selectedZone) {
            // Zero means "All zones" was selected
            if (0 == $selectedZone) {
                $allSelected = true;
                break;
            }
        }

        $this->zoneRepository->updatePriceRuleZones($object->id, $selectedZones, $allSelected);
    }

    public function addUpdateShops($object)
    {
        $selectedShops = Tools::getValue('shops_select');
        $allSelected = false;

        if (!$selectedShops) {
            $allSelected = true;
            $this->shopRepository->updatePriceRuleShops($object->id, [], $allSelected);
            return;
        }

        foreach ($selectedShops as $selectedShop) {
            // Zero means "All zones" was selected
            if (0 == $selectedShop) {
                $allSelected = true;
                break;
            }
        }

        $this->shopRepository->updatePriceRuleShops($object->id, $selectedShops, $allSelected);
    }

    public function addUpdatePaymentMethods($object)
    {
        $dpdPaymentMethods = (array) Tools::getValue('dpd-payment-method');
        $checkedAll = 0;

        if (Tools::getValue('checkme_payment')) {
            $checkedAll = 1;
        }
        if (empty($dpdPaymentMethods)) {
            return true;
        }

        if (!$this->paymentRepository->updatePriceRulePayments((int) $object->id, $dpdPaymentMethods, $checkedAll)) {
            $this->errors[] = $this->module->l('Failed to add or update payment methods', self::FILE_NAME);
            return false;
        }
        return true;
    }

    public function removeCarriers($idPriceRule)
    {
        $this->productRepository->removePriceRuleProducts($idPriceRule);
    }

    public function removePaymentMethods($idPriceRule)
    {
        $this->paymentRepository->removePriceRulePayments($idPriceRule);
    }

    public function removeZones($idPriceRule)
    {
        $this->zoneRepository->removePriceRuleZones($idPriceRule);
    }

    public function removeShops($idPriceRule)
    {
        $this->shopRepository->removePriceRuleShops($idPriceRule);
    }

    /**
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function duplicatePriceRule($idPriceRule)
    {
        $tables = ['dpd_price_rule_carrier', 'dpd_price_rule_payment', 'dpd_price_rule_zone'];

        $priceRule = new DPDPriceRule($idPriceRule);

        $lowestPosition = $this->priceRuleRepository->getLowestPosition();

        $priceRuleClone = clone $priceRule;
        $priceRuleClone->active = 0;
        $priceRuleClone->position = ++$lowestPosition;

        if (!$priceRuleClone->add()) {
            return false;
        }

        $priceRuleTablesData = $this->getPriceRuleTablesData($tables, $idPriceRule);

        if (!$priceRuleTablesData) {
            return false;
        }

        if (!$this->duplicatePriceRuleTablesData(
            $tables,
            $priceRuleTablesData,
            $priceRuleClone->id
        )) {
            return false;
        }

        return true;
    }

    public function applyPriceRuleForCarrier(Cart $cart, $priceRulesIds, $shopId)
    {
        foreach ($priceRulesIds as $priceRuleId) {
            if (!$this->priceRuleRepository->isAvailableInShop($priceRuleId, $shopId)) {
                return false;
            }
            $priceRule = new DPDPriceRule($priceRuleId, null, $shopId);

            // Check if price rule is applicable for this cart
            if ($priceRule->isApplicableForCart($cart)) {
                // If it's applicable - use price rule's price and don't check other price rules
                return Tools::convertPrice($priceRule->price, $cart->id_currency);
            }
        }

        return false;
    }

    private function getPriceRuleTablesData($tables, $idPriceRule)
    {
        $priceRulesTablesDate = [];

        foreach ($tables as $table) {
            $priceRulesTablesDate[$table] = $this->priceRuleRepository->getPriceRuleTableData($table, $idPriceRule);
        }

        return $priceRulesTablesDate;
    }

    private function duplicatePriceRuleTablesData($tables, $priceRuleTablesData, $newPriceRuleId)
    {
        foreach ($tables as $table) {
            if (!$this->priceRuleRepository->addPriceRuleTableData($table, $priceRuleTablesData[$table], $newPriceRuleId)) {
                return false;
            }
        }

        return true;
    }
}
