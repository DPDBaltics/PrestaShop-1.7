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

<div class="search-block-container">
        {if count($city_list) > 1}
    <div class="col-xl-6 col-lg-6 col-sm-12 dpd-city-block dpd-select-wrapper dpd-input-wrapper">
    <select name="dpd-city" class="form-control-chosen chosen-select">
        {else}
        <div class="col-xl-0 col-lg-0 col-sm-12 dpd-city-block dpd-select-wrapper">
        <select name="dpd-city" style="display: none">
        {/if}
            {if !empty($city_list)}
            {if !$selected_city}
                <option selected value=""> {l s='Please select a city' mod='dpdbaltics'}</option>
                {/if}
                {foreach from=$city_list item=city}
                <option {if strtolower($selected_city) === strtolower($city)}selected{/if}
                        value="{$city|escape:'htmlall':'UTF-8'}">{$city|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            {/if}
        </select>
        <div class="control-label dpd-input-placeholder dpd-select-placeholder">
            {l s='City' mod='dpdbaltics'}
        </div>
    </div>

    <div class="col-xl-6 col-lg-6 col-sm-12 dpd-city-block dpd-input-wrapper js-pudo-search-street">
        {include file='module:dpdbaltics/views/templates/hook/front/partials/pudo-search-street.tpl'}
    </div>
</div>
