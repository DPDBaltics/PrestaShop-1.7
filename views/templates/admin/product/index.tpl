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

<div class="panel products-block">
    <div class="row product-header-row custom-product-row">
        <div class="col-xs-1">
            <p>{l s='Product' mod='dpdbaltics'}</p>
        </div>
        <div class="css-product-margin {if $isMultiShop}col-xs-1{else}col-xs-2{/if}">
            <p>{l s='Carrier name' mod='dpdbaltics'}</p>
        </div>
        <div class="css-product-margin {if $isMultiShop}col-xs-2{else}col-xs-3{/if} row">
            <p>{l s='Carrier delivery time' mod='dpdbaltics'}</p>
        </div>
        <div class="css-product-margin col-xs-2">
            <p>{l s='Zone' mod='dpdbaltics'}</p>
        </div>
        <div class="css-product-margin col-xs-1">
            <p>{l s='Availability' mod='dpdbaltics'}</p>
        </div>
        <div class="css-product-margin col-xs-2 {if !$isMultiShop}hidden d-none{/if}">
            <p>{l s='Shop' mod='dpdbaltics'}</p>
        </div>
        <div class="col-xs-1">
            <p>{l s='Active' mod='dpdbaltics'}</p>
        </div>

        <div class="col-xs-1">

        </div>
    </div>
    {foreach $rows as $row}
        {$row}
    {/foreach}
</div>
