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

<div class="search-block-container row">
    <div class="col-xl-8 col-lg-8 col-xs-8 dpd-city-block dpd-input-wrapper">
        <div class="dpd-input-placeholder dpd-select-placeholder">
            {l s='City' mod='dpdbaltics'}
        </div>
        <div class="row">
            <div class="col-lg-9">
                <select name="dpd-city" class="form-control">
                    {if !empty($city_list)}
                        {foreach from=$city_list item=city}
                            <option {if $selected_city === $city}selected{/if}
                                    value="{$city|escape:'htmlall':'UTF-8'}">{$city|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
            <div class="col-lg-3">
                <div class="dpd-pull-right">
                    <button name="search-pudo-services"
                            class="btn btn-secondary button button-small pudo-search-button dpd-action-button"
                            type="button" data-loading-text="{l s='Loading...' mod='dpdbaltics'}"
                            data-original-text="{l s='Search' mod='dpdbaltics'}">
                <span>
                    {l s='Search' mod='dpdbaltics'} <i class="icon icon-chevron-right"></i>
                </span>
                    </button>
                </div>
                {if isset($pudoHelper->isSSLEnabled) && $pudoHelper->isSSLEnabled}
                    <div class="dpd-pull-right">
                        <i class="material-icons  dpd-my-location" aria-hidden="true"
                           title="{l s='My current location' mod='dpdbaltics'}">&#xE55C;</i>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
