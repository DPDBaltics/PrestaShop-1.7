<?php

namespace Invertus\dpdBaltics\Service\Payment;

use Address;
use Cache;
use Carrier;
use Cart;
use Configuration;
use Context;
use DPDBaltics;
use DPDPriceRule;
use DPDProduct;
use DPDPudo;
use Exception;
use Hook;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Repository\CodPaymentRepository;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use Invertus\dpdBaltics\Repository\ProductRepository;
use Invertus\dpdBaltics\Repository\PudoRepository;
use Module;
use PaymentModule;
use Shop;
use Validate;

class PaymentService
{

    const ID_BEGINNING_FOR_PICKUP_TYPE_IDENTIFICATION = 4;
    
    /**
     * @var CodPaymentRepository
     */
    private $codPaymentRepository;

    /**
     * @var DPDBaltics
     */
    private $module;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Shop
     */
    private $shop;

    /**
     * @var PudoRepository
     */
    private $pudoRepository;
    /**
     * @var PriceRuleRepository
     */
    private $priceRuleRepository;

    public function __construct(
        CodPaymentRepository $codPaymentRepository,
        PudoRepository $pudoRepository,
        DPDBaltics $module,
        ProductRepository $productRepository,
        PriceRuleRepository $priceRuleRepository,
        Shop $shop
    ) {
        $this->codPaymentRepository = $codPaymentRepository;
        $this->pudoRepository = $pudoRepository;
        $this->module = $module;
        $this->productRepository = $productRepository;
        $this->shop = $shop;
        $this->priceRuleRepository = $priceRuleRepository;
    }

    /**
     * @param $paymentModuleName
     *
     * @return bool
     *
     * @throws Exception
     */
    public function isOrderPaymentCod($paymentModuleName)
    {
        $codPaymentModules = $this->codPaymentRepository->getCodPaymentModules();

        foreach ($codPaymentModules as $codPaymentModule) {
            $codPaymentModuleName = Module::getInstanceById($codPaymentModule)->name;

            if ($paymentModuleName === $codPaymentModuleName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter payment methods by selected price rule
     */
    public function filterPaymentMethods(Cart $cart)
    {
        if (!$cart->id_carrier) {
            $deliveryOptions = $cart->getDeliveryOption();
            if (!isset($deliveryOptions[$cart->id_address_delivery])) {
                return;
            }
            $cart->id_carrier = (int) $deliveryOptions[$cart->id_address_delivery];
        }
        $carrier = new Carrier($cart->id_carrier);

        // If non-dpd carrier is selected - do nothing
        if ($carrier->external_module_name != $this->module->name) {
            return;
        }

        $deliveryAddress = new Address($cart->id_address_delivery);

        // Get all price rules for current carrier
        $priceRulesIds =
            $this->priceRuleRepository->getByCarrierReference(
                $deliveryAddress,
                $carrier->id_reference
            );

        // All payment methods available for current customer by default
        $allPaymentMethods = PaymentModule::getPaymentModules();

        $disableAll = false;

        foreach ($priceRulesIds as $priceRuleId) {
            $priceRule = new DPDPriceRule($priceRuleId);

            // Check if price rule is applicable for this cart
            if ($priceRule->isApplicableForCart($cart)) {
                // If it's applicable - disable unwanted payment methods and don't check other price rules

                // Payment methods allowed by price rule
                $allowedPaymentMethodsIds = (array) $this->priceRuleRepository->getAllowedPaymentMethodsIds($priceRule->id);

                // If ALL payment methods are allowed by the price rule - do not disable anything, just break the loop
                if (isset($allowedPaymentMethodsIds[0]) && 'all' === $allowedPaymentMethodsIds[0] && !$disableAll) {
                    break;
                }

                foreach ($allPaymentMethods as $paymentMethod) {
                    if (!in_array($paymentMethod['id_module'], $allowedPaymentMethodsIds) || $disableAll) {
                        $this->disablePaymentModule($paymentMethod['id_module']);
                    }
                }

                // We don't want other price rules to take effect
                break;
            }
        }
    }

    public function filterPaymentMethodsByCod(Cart $cart)
    {
        if (!$cart->id_carrier) {
            return;
        }

        $carrier = new Carrier($cart->id_carrier);

        if ($carrier->external_module_name !== $this->module->name) {
            return;
        }

        $productId = $this->productRepository->getProductIdByCarrierReference($carrier->id_reference);
        $product = new DPDProduct($productId);

        if (!$product->getIsCod()) {
            $this->removePaymentsWithCod();
            return;
        } else {
            $this->removePaymentsWithoutCod();
        }

        $pudoId = $this->pudoRepository->getIdByCart($cart->id);
        if (!$pudoId) {
            return;
        }
        $isProductPudo = $this->productRepository->isProductPudo($carrier->id_reference);
        if (!$isProductPudo) {
            return;
        }
        $pudo = new DPDPudo($pudoId);
        $isPickupCod = $this->checkIfPickupCanBeCOD($pudo->pudo_id);
        if (!$isPickupCod) {
            $this->removePaymentsWithCod();
        }
    }

    private function removePaymentsWithCod()
    {
        $codPaymentModules = $this->codPaymentRepository->getCodPaymentModules();

        $allPaymentMethods = PaymentModule::getPaymentModules();

        foreach ($allPaymentMethods as $paymentMethod) {
            if (in_array($paymentMethod['id_module'], $codPaymentModules, false)) {
                $this->disablePaymentModule($paymentMethod['id_module']);
            }
        }
    }

    private function removePaymentsWithoutCod()
    {
        $codPaymentModules = $this->codPaymentRepository->getCodPaymentModules();

        $allPaymentMethods = PaymentModule::getPaymentModules();

        foreach ($allPaymentMethods as $paymentMethod) {
            if (!in_array($paymentMethod['id_module'], $codPaymentModules, false)) {
                $this->disablePaymentModule($paymentMethod['id_module']);
            }
        }
    }
    private function disablePaymentModule($moduleId)
    {
        $module = Module::getInstanceById($moduleId);

        if (!Validate::isLoadedObject($module)) {
            return;
        }

        $cacheId = 'exceptionsCache';
        // existing cache
        $exceptionsCache = (Cache::isStored($cacheId)) ? Cache::retrieve($cacheId) : [];
        $controller = 0 == Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order' : 'orderopc';
        // ID of hook we are going to manipulate
        $id_hook = Hook::getIdByName('paymentOptions');

        $key = (int)$id_hook . '-' . (int)$module->id;
        $exceptionsCache[$key][$this->shop->id][] = $controller;

        Cache::store($cacheId, $exceptionsCache);
    }

    private function checkIfPickupCanBeCOD($pudoId)
    {
        $pudoIdBeginning = substr($pudoId, 0, self::ID_BEGINNING_FOR_PICKUP_TYPE_IDENTIFICATION);
        if (in_array($pudoIdBeginning, Config::AVAILABLE_PUDO_COD_IDS, false)) {
            return true;
        }
        return false;
    }
}
