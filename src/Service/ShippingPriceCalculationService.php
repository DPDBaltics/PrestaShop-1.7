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

namespace Invertus\dpdBaltics\Service;

use Cart;

class ShippingPriceCalculationService
{
    /**
     * @param Cart $cart
     * @param PriceRuleService $priceRuleService
     * @param array<string, string> $priceRulesIds
     *
     * @return float
     */
    public function calculate(Cart $cart, PriceRuleService $priceRuleService, array $priceRulesIds)
    {
        $products = $cart->getProducts();
        $additionalShippingCosts = 0.0;
        foreach ($products as $product) {
            if((float) $product['additional_shipping_cost']) {
                $additionalShippingCosts += (float) $product['additional_shipping_cost'];
            }
        }

        return $priceRuleService->applyAdditionalCosts($cart, $priceRulesIds, $additionalShippingCosts);
    }
}
