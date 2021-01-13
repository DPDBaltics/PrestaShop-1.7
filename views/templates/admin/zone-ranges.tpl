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
