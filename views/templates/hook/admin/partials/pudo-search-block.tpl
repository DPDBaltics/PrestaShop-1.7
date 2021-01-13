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
