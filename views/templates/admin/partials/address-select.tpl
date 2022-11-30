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