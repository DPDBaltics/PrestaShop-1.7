{*
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
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