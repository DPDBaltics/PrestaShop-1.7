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

if (!defined('_PS_VERSION_')) {
    exit;
}

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
            'module:dpdbaltics/views/templates/hook/front/product-page-carriers.tpl'
        );
    }
}
