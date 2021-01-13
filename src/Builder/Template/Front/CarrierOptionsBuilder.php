<?php

namespace Invertus\dpdBaltics\Builder\Template\Front;

use Address;
use Carrier;
use Configuration;
use Context;
use Invertus\dpdBaltics\Config\Config;
use Invertus\dpdBaltics\Provider\ProductShippingCostProvider;
use Invertus\dpdBaltics\Repository\CarrierRepository;
use Invertus\dpdBaltics\Repository\PriceRuleRepository;
use PrestaShopBundle\Controller\Admin\ProductController;
use Tools;

class CarrierOptionsBuilder
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var PriceRuleRepository
     */
    private $priceRuleRepository;

    /**
     * @var ProductShippingCostProvider
     */
    private $productShippingCostProvider;

    private $moduleLocalPath;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    public function __construct(
        Context $context,
        PriceRuleRepository $priceRuleRepository,
        ProductShippingCostProvider $productShippingCostProvider,
        $moduleLocalPath,
        CarrierRepository $carrierRepository
    ) {
        $this->context = $context;
        $this->priceRuleRepository = $priceRuleRepository;
        $this->productShippingCostProvider = $productShippingCostProvider;
        $this->moduleLocalPath = $moduleLocalPath;
        $this->carrierRepository = $carrierRepository;
    }

    public function renderCarrierOptionsInProductPage()
    {
        if (!$this->context->controller instanceof \ProductControllerCore ||
            !Configuration::get(Config::SHOW_CARRIERS_IN_PRODUCT_PAGE)
        ) {
            return false;
        }

        $dpdCarriers = $this->carrierRepository->getDpdCarriers(
            $this->context->shop->id,
            $this->context->language->id
        );

        $idAddress = false;

        if ($this->context->cart->id_address_delivery) {
            $idAddress = (int) $this->context->cart->id_address_delivery;
        } elseif ($this->context->customer->id) {
            $idAddress = Address::getFirstCustomerAddressId($this->context->customer->id);
        }

        foreach ($dpdCarriers as $key => $dpdCarrier) {
            $productShippingCost = Tools::displayPrice(
                $this->productShippingCostProvider->getProductShippingCost($dpdCarrier['id_reference'], $idAddress)
            );

            if (!$productShippingCost) {
                unset($dpdCarriers[$key]);

                continue;
            }

            $dpdCarriers[$key]['shipping_cost'] = $productShippingCost;

            $carrier = Carrier::getCarrierByReference($dpdCarrier['id_reference']);
            $dpdCarriers[$key]['carrier_logo'] = Config::getCarrierLogo($carrier->id);
        }

        $this->context->smarty->assign([
            'carriers' => $dpdCarriers
        ]);

        return $this->context->smarty->fetch(
            $this->moduleLocalPath . 'views/templates/hook/front/product-page-carriers.tpl'
        );
    }
}
