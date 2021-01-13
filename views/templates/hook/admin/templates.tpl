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

<div id="dpdTempates">
    <div id="pudoTemplate" {if !$is_pudo}class="hidden d-none"{/if}>
        {include file='./partials/pudo-address.tpl'}
    </div>
    <div id="shipmentTemplate">
        {include file="./partials/shipment.tpl"}
    </div>

    <div id="orderReturnTemplate">
        <div class="col-lg-12 return-link-holder">
{*            <a href="{$orderReturnLink|escape:'htmlall':'UTF-8'}" class="btn btn-default js-order-return-button">*}
{*                {l s='Initiate return' mod='dpdbaltics'}*}
{*            </a>*}
        </div>
    </div>
</div>