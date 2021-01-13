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

<form id="product-home-collection-form-{$idProduct}" class="js-product-form js-product-home-collection">
    <div class="row custom-product-row {if $isOddRow == true}odd-row{else}even-row{/if}">
        <input type="hidden" name="home-collection-product-id" value="{$idProduct}" disabled>
        <div class="col-xs-8">
            <h4>{$productName} {l s=" Home collection"}</h4>
        </div>

        <div class="product-select-wrapper js-home-collection-select col-xs-2">
            <select class="chosen" name="id_service" disabled>
                {foreach $homeCollectionProducts as $homeCollectionProduct}
                    <option
                        value="{$homeCollectionProduct.id_dpd_service_carrier}"
                        {if $activeHomeColService && $activeHomeColService == $homeCollectionProduct.id_dpd_service_carrier} selected="selected"{/if}
                    >
                        {$homeCollectionProduct.service_name}
                    </option>
                {/foreach}
            </select>
        </div>

        <div class="col-xs-1">
            <span class="switch prestashop-switch">
                <input type="radio"
                       name="product-active-{$idProduct}"
                       id="product-active-{$idProduct}-on"
                       value="1"
                       disabled
                       {if $activeHomeColService}checked="checked"{/if}
                >
                <label for="product-active-{$idProduct}-on" class="radioCheck">Yes</label>
                <input type="radio"
                       name="product-active-{$idProduct}"
                       id="product-active-{$idProduct}-off"
                       value="0"
                       disabled
                       {if !$activeHomeColService}checked="checked"{/if}
                >
                <label for="product-active-{$idProduct}-off" class="radioCheck">No</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
        <div class="col-xs-1">
            {include file='./buttons.tpl' dpd_product_id_identifier={$idProduct}}
        </div>
    </div>
</form>
