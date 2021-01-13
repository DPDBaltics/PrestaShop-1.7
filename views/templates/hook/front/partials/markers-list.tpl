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

{if !empty($pudoServices)}
    <div class="list-group dpd-services-block">
        <ul class="list-inline">
            {foreach from=$pudoServices item=service}
                <li class="list-group-item list-inline-item" data-listid="{$service->getParcelShopId()|escape:'htmlall':'UTF-8'}">
                    <h4 class="list-group-item-heading" style="padding-right:40px">
                        {if $service->type === 'locker'}
                            <img class="dpd-pickup-logo" height="40px" width="40px" src="{$dpd_locker_logo}"
                                 alt="{l s='DPD pickup point' mod='dpdbaltics'}">
                        {/if}
                        {if $service->type === 'parcel_shop'}
                            <img class="dpd-pickup-logo" height="40px" width="40px" src="{$dpd_pickup_logo}"
                                 alt="{l s='DPD pickup point' mod='dpdbaltics'}">
                        {/if}
                        {$service->getCompany()|escape:'htmlall':'UTF-8'}
                    </h4>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="list-group-item-text">
                                    {$service->getStreet()|escape:'htmlall':'UTF-8'}<br>
                                    {$service->getPCode()|escape:'htmlall':'UTF-8'} {$service->getCity()|escape:'htmlall':'UTF-8'}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-7">
                            <p
                                    class="clearfix dpd-more-information expand"
                                    tabindex="0"
                                    data-trigger="focus"
                                    data-expand="{l s='more information' mod='dpdbaltics'}"
                                    data-collapse="{l s='less information' mod='dpdbaltics'}"
                                    data-toggle="popover"
                                    data-placement="right"
                                    data-html="true"
                                    data-container=".dpd-pudo-container"
                                    title="{l s='Working hours' mod='dpdbaltics'}"
                                    data-content="{include file='module:dpdbaltics/views/templates/hook/front/partials/working-hours.tpl' workingHours=$service->getOpeningHours()}{if isset($service->extraInfo)}{$service->extraInfo}{/if}">
                                {l s='more information' mod='dpdbaltics'}
                            </p>
                        </div>
                        <div class="col-lg-5">
                            <button
                                    class="dpd-action-button btn btn-secondary button button-medium dpd-pudo-select"
                                    type="button"
                                    data-id="{$service->getParcelShopId()}"
                                    data-select="{l s='Select' mod='dpdbaltics'}"
                                    data-selected="{l s='Selected' mod='dpdbaltics'}"
                                    data-change="{l s='Change' mod='dpdbaltics'}"
                                    data-countrycode="{$service->getCountry()}"
                                    data-zipcode="{$service->getPCode()}"
                                    data-address="{$service->getStreet()}"
                                    data-city="{$service->getCity()}"
                                    data-countryId="{$service->getCountry()}"
                                    {if $service->getParcelShopId() === $saved_pudo_id}
                                        disabled
                                    {/if}
                            >

                                <span>
                                    {if $service->getParcelShopId() === $saved_pudo_id}
                                        {l s='Selected' mod='dpdbaltics'}
                                    {else}
                                        {l s='Select' mod='dpdbaltics'}
                                    {/if}
                                </span>
                            </button>
                        </div>
                    </div>


                    <div class="extra-info-list">
                        {if !empty($service->getOpeningHours())}
                            <div class="clearfix">&nbsp;</div>
                            <div class="extra-info-working-hours dpd-hidden">
                                <p>{l s='Working hours:' mod='dpdbaltics'}</p>
                                {include file='module:dpdbaltics/views/templates/hook/front/partials/working-hours.tpl' workingHours=$service->getOpeningHours()}
                                {if isset($service->extraInfo)}
                                    <p class="extra-info-text">{$service->extraInfo|escape:'htmlall':'UTF-8'}</p>
                                {/if}
                            </div>
                        {/if}
                        <div class="clearfix">
                            &nbsp;
                        </div>
                    </div>
                    <input name="saved_pudo_id" class="dpd-hidden hidden" hidden value="{$saved_pudo_id}">
                    <input name="pudo-type" class="dpd-hidden hidden" hidden value="{$service->type}">
                    <input name="pudo-lat" class="dpd-hidden hidden" hidden value="{$service->getLatitude()}">
                    <input name="pudo-lng" class="dpd-hidden hidden" hidden value="{$service->getLongitude()}">
                </li>
            {/foreach}
        </ul>
    </div>
{else}
    <div class="dpd-services-block">
        <div class="alert alert-warning">
            {l s='No pickup points found' mod='dpdbaltics'}
        </div>
    </div>
{/if}
