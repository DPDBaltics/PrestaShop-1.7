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

<form id="product-service-form-{$idProduct}" class="js-product-form js-product-pickup-return">
    <div class="row custom-product-row {if $isOddRow == true}odd-row{else}even-row{/if}">
        <input type="hidden" name="id-product" value="{$idProduct}" disabled>
        <div class="col-xs-10">
            <h4>{$productName}</h4>
        </div>

        <div class="col-xs-1">
            <span class="switch prestashop-switch">
                <input type="radio"
                       name="product-active-{$idProduct}"
                       id="product-active-{$idProduct}-on"
                       value="1"
                       disabled
                       {if $isActive}checked="checked"{/if}
                >
                <label for="product-active-{$idProduct}-on" class="radioCheck">Yes</label>
                <input type="radio"
                       name="product-active-{$idProduct}"
                       id="product-active-{$idProduct}-off"
                       value="0"
                       disabled
                       {if !$isActive}checked="checked"{/if}
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
