{**
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
 *}

<div>
    <div class="form-group col-lg-3">
        <label class="control-label">
            {l s='Order price from (inclusive)' mod='dpdbaltics'}
        </label>
        <div class="input-group ranges-input">
            <input type="text" name="order_price_from" id="order_price_from" {if isset($priceFrom) && $priceFrom}value="{$priceFrom|floatval}"{/if}>
            <span class="input-group-addon">
                {Context::getContext()->currency->sign|escape:'htmlall':'UTF-8'}
            </span>
        </div>
    </div>
    <div class="form-group col-lg-3">
        <label class="control-label">
            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='You can set price equal to Order price from to make this rule for exact price. This is price of order alone not counting shipping.' mod='dpdbaltics'}">
                {l s='Order price to (inclusive)' mod='dpdbaltics'}
			</span>
        </label>
        <div class="input-group ranges-input">
            <input type="text" name="order_price_to" id="order_price_to" {if isset($priceTo) && $priceTo}value="{$priceTo|floatval}"{/if}>
            <span class="input-group-addon">
                {Context::getContext()->currency->sign|escape:'htmlall':'UTF-8'}
            </span>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-lg-3">
        <label class="control-label">
            {l s='Weight from (inclusive)' mod='dpdbaltics'}
        </label>
        <div class="input-group ranges-input">
            <input type="text" name="weight_from" id="weight_from" {if isset($weightFrom) && $weightFrom}value="{$weightFrom|floatval}"{/if}>
            <span class="input-group-addon">
                {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
            </span>
        </div>
    </div>
    <div class="form-group col-lg-3">
        <label class="control-label">
            {l s='Weight to (inclusive)' mod='dpdbaltics'}
        </label>
        <div class="input-group ranges-input">
            <input type="text" name="weight_to" id="weight_to" {if isset($weightTo) && $weightTo}value="{$weightTo|floatval}"{/if}>
            <span class="input-group-addon">
                {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
            </span>
        </div>
    </div>
</div>