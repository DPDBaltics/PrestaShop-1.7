{*
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 *
 *}

<div class="dpd_address_prefill_block">
    <select title="{l s='Select address template' mod='dpdbaltics'}"
            class="address_prefill_select"
            data-target-prefix="{$prefix|escape:'htmlall':'UTF-8'}"
    >
        <option value="">{if empty($address_templates)}{l s='No addresses available' mod='dpdbaltics'}{/if}</option>
        {foreach $address_templates as $address}
            <option value="{$address.id_dpd_address_template|escape:'htmlall':'UTF-8'}"
                    data-type="{$address.type|escape:'htmlall':'UTF-8'}"
                    data-full-name="{$address.full_name|escape:'htmlall':'UTF-8'}"
                    data-mobile-phone-code="{$address.mobile_phone_code|escape:'htmlall':'UTF-8'}"
                    data-mobile-phone="{$address.mobile_phone|escape:'htmlall':'UTF-8'}"
                    data-email="{$address.email|escape:'htmlall':'UTF-8'}"
                    data-dpd-country-id="{$address.dpd_country_id|escape:'htmlall':'UTF-8'}"
                    data-zip-code="{$address.zip_code|escape:'htmlall':'UTF-8'}"
                    data-city="{$address.dpd_city_name|escape:'htmlall':'UTF-8'}"
                    data-address="{$address.address|escape:'htmlall':'UTF-8'}"
            >
                {$address.name|escape:'htmlall':'UTF-8'}
            </option>
        {/foreach}
    </select>

    <button class="btn btn-default dpd-prefill-btn" type="button">{l s='Prefill' mod='dpdbaltics'}</button>
</div>