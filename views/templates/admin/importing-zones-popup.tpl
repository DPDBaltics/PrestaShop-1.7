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

<div id="import-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="text-left">
                <img style="padding: 10px" src="{$loading_gif}"/>
                <label id="js-loading" for="message-text" class="col-form-label">{l s='Loading...' mod='dpdbaltics'}</label>
                <label id="js-importing-zones" for="message-text" class="col-form-label" style="display: none">{l s='Importing zones...' mod='dpdbaltics'}</label>
            </div>
        </div>
    </div>
</div>
{if $selectedCountry === 'LV'}
    <div id="import-modal-confirm-latvia" class="modal fade import-modal-confirm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false" data-country="latvia" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close js-close-zone-import js-import-zones-close-button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{l s='Do you want to import Latvia zones?' mod='dpdbaltics'}</h4>
                </div>
                <div class="modal-footer">
                    <button id="import-zones-close-button" type="button" class="btn btn-default js-import-zones-close-button" data-dismiss="modal">
                        {l s='Close' mod='dpdbaltics'}
                    </button>

                    <button data-country="latvia" type="button" class="btn btn-primary import-zones-button">{l s='Import' mod='dpdbaltics'}</button>
                </div>
            </div>
        </div>
    </div>
{/if}
{if $selectedCountry === 'LT'}
    <div id="import-modal-confirm-lithuania" class="modal fade import-modal-confirm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="false" data-country="lithuania" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close js-close-zone-import js-import-zones-close-button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{l s='Do you want to import Lithuania zones?' mod='dpdbaltics'}</h4>
                </div>
                <div class="modal-footer">
                    <button id="import-zones-close-button" type="button" class="btn btn-default js-import-zones-close-button" data-dismiss="modal">
                        {l s='Close' mod='dpdbaltics'}
                    </button>

                    <button data-country="lithuania" type="button" class="btn btn-primary import-zones-button">{l s='Import' mod='dpdbaltics'}</button>
                </div>
            </div>
        </div>
    </div>
{/if}