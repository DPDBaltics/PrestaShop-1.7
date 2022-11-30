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

<div id="zoneRangesBlock">
    <div class="row">
        <div class="col-md-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">{l s='Country' mod='dpdbaltics'}</th>
                        <th class="text-center">{l s='All ranges' mod='dpdbaltics'}</th>
                        <th class="text-center zone-range-table-column">{l s='From (inclusive)' mod='dpdbaltics'}</th>
                        <th class="text-center zone-range-table-column">{l s='To (inclusive)' mod='dpdbaltics'}</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody id="zoneRangeForm">
                    <tr class="js-zone-range-block active">
                        <td class="text-center">
                            <select title="{l s='Country' mod='dpdbaltics'}" class="js-country-select chosen">
                                <option value="-1"></option>
                                {foreach $countryList as $country}
                                    <option value="{$country.id_country|intval}">
                                        {$country.name|escape:'htmlall':'UTF-8'}
                                    </option>
                                {/foreach}
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="checkbox"
                                   title="{l s='All ranges' mod='dpdbaltics'}"
                                   class="js-all-ranges-input"
                            >
                        </td>
                        <td class="text-center">
                            <input class="js-zip-input js-zone-range-from form-control"
                                   title="{l s='From' mod='dpdbaltics'}"
                                   size="5"
                            >
                        </td>
                        <td class="text-center">
                            <input class="js-zip-input js-zone-range-to form-control"
                                   title="{l s='To' mod='dpdbaltics'}"
                                   size="5"
                            >
                        </td>
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm js-add-zone-range-btn" type="button">
                                {l s='Add' mod='dpdbaltics'}
                            </button>
                        </td>
                    </tr>
                </tbody> <!-- ./#zoneRangeForm -->

                <tbody id="zoneRangeValues">
                </tbody> <!-- ./#zoneRangeValues -->
            </table>
        </div>
    </div>
</div>

<!-- HTML templates used in Javascript -->
<div class="hidden" id="dpdZoneRangeTemplates">
    <table>
        <tr class="js-zone-range-block js-zone-range-template">

            <td class="text-center">
                <span class="js-zone-range-country"></span>
            </td>

            <td class="text-center">
                <input type="checkbox"
                       title="{l s='All ranges' mod='dpdbaltics'}"
                       class="js-all-ranges-input"
                >
            </td>

            <td class="text-center">
                <input class="js-zip-input js-zone-range-from form-control"
                       title="{l s='From' mod='dpdbaltics'}"
                       size="5"
                >
            </td>

            <td class="text-center">
                <input class="js-zip-input js-zone-range-to form-control"
                       title="{l s='To' mod='dpdbaltics'}"
                       size="5"
                >
            </td>

            <td class="text-center">
                <button class="btn btn-danger btn-sm js-remove-zone-range-btn" type="button">
                    {l s='Remove' mod='dpdbaltics'}
                </button>

                <input type="hidden" class="js-zone-range-id">
            </td>
        </tr>
    </table>

    <div class="alert alert-danger js-alert-error-template" role="alert">
        <span class="js-message"></span>
    </div>
</div>
