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
<div class="col-lg-12 pudo-info-container">
    <input class="hidden d-none form-control" name="selected-pudo-id" value="{$selectedPudo->getParcelShopId()}">
    <input class="hidden d-none form-control" name="selected_pudo_iso_code" value="{$selectedPudo->getCountry()}">
    <div class="card-body">
        <div class="form-horizontal form-group">
            <div class="form-row row dpd-form-group">
                <label class="control-label col-lg-3 dpd-no-padding">{l s='Address: ' mod='dpdbaltics'}</label>
                <div class="address col-lg-9">
                    {$selectedPudo->getStreet()}
                </div>
                <input name="dpd-street" type="hidden" value="{$selectedPudo->getStreet()}">
                </div>
            </div>
            <div class="form-row row dpd-form-group">
                <label class="control-label col-lg-3 dpd-no-padding">{l s='Post code: ' mod='dpdbaltics'}</label>
                <div class="zip-code col-lg-9">
                    {$selectedPudo->getPCode()}
                </div>
                <input name="dpd-zip-code" type="hidden" value="{$selectedPudo->getPCode()}">
            </div>
            <div class="form-row row dpd-form-group">
                <label class="control-label col-lg-3 dpd-no-padding">{l s='City: ' mod='dpdbaltics'}</label>
                <div class="city col-lg-9">
                    {$selectedPudo->getCity()}
                </div>
            </div>
            <div class="form-row row dpd-form-group">
                <label class="control-label col-lg-3 dpd-no-padding ">{l s='Country: ' mod='dpdbaltics'}</label>
                <div class="country col-lg-9">
                    {$selectedPudo->getCountry()}
                </div>
            </div>
            <div class="form-row row dpd-form-group">
                <label class="control-label col-lg-3 dpd-no-padding "></label>
                <div class="col-lg-9">
                    <b class="expand-collapse-pudo-extra-info"
                       data-more="{l s='More information' mod='dpdbaltics'}"
                       data-less="{l s='Less information' mod='dpdbaltics'}">{l s='More information' mod='dpdbaltics'}</b>
                </div>
            </div>
            <div class="form-row row dpd-form-group">
                <label class="control-label col-lg-3 dpd-no-padding "></label>
                <div class="col-lg-9">
                    <ul class="work-hours d-none hidden">
                        {l s='Working hours:' mod='dpdbaltics'}
                        {foreach $selectedPudo->getOpeningHours() as $workHours}
                            <li>{$workHours->getWorkHoursFormatted()}</li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>