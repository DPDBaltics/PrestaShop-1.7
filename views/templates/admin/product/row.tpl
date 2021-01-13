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

<form id="product-service-form-{$idProduct}" class="js-product-form js-product-service">
    <div class="row custom-product-row {if $isOddRow == true}odd-row{else}even-row{/if}">
        <input type="hidden" name="id-product" value="{$idProduct}" disabled>
        <div class="col-xs-1">
            <h4>{$productName}</h4>
        </div>
        <div class="css-product-margin {if $isMultiShop}col-xs-1 {else}col-xs-2{/if}">
            <input
                    class="js-product-name"
                    type="text"
                    name="carrier-name"
                    value="{$carrierName}"
                    id="{$idProduct}"
                    disabled
            >
        </div>
        <div class="{if $isMultiShop}col-xs-2 {else}col-xs-3{/if} css-product-margin">
            <div class="row">
                <div class="col-xs-8 delivery-time-input">
                    {foreach from=$languages item=language}
                        <div>
                            <input type="text"
                                   name="delivery-time-{$language}"
                                   class="js-product-delivery-time"
                                   value="{$delays[$language]}"
                                   style="display: {if $selectedLanguage === $language}block{else}none{/if};"
                                   disabled
                            >
                        </div>
                    {/foreach}
                </div>
                <div class="col-xs-4 js-product-delivery-time-lang">
                    {html_options name="languages" options=$languages class="chosen" disabled='disabled'}
                </div>
            </div>
        </div>
        <div class="col-xs-2 js-product-zones css-product-margin">
            {$zoneBlock}
        </div>
        <div class="col-xs-1 js-product-availability css-product-margin">
            {if $hasAvailability}
                <a
                        class="btn btn-default dpd-button-center"
                        name="editAvailability"
                        disabled
                        href="{$link->getAdminLink("AdminDPDBalticsProductsAvailability", true, [], ['id_dpd_product' => {$idProduct}, 'updatedpd_product' => ''])}"
                >
                    <i class="icon-gear"></i>
                </a>
            {/if}
        </div>
        <div class="col-xs-2 js-product-shop css-product-margin{if !$isMultiShop} hidden{/if}">
            {$shopBlock}
        </div>
        <div class="col-xs-1 css-product-margin">
            <span class="switch prestashop-switch js-product-active-switch">
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
