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

<div id="productAvailabilityBlock">
    <input type="hidden" name="product_id" value="{$productId}">
    <div class="row">
        <div class="col-md-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">{l s='Day' mod='dpdbaltics'}</th>
                        <th class="text-center zone-range-table-column">{l s='From (inclusive)' mod='dpdbaltics'}</th>
                        <th class="text-center zone-range-table-column">{l s='To (inclusive)' mod='dpdbaltics'}</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody id="zoneRangeForm">
                    <tr class="js-product-availability-range-block active">
                        <td class="text-center">
                            <select title="{l s='Day' mod='dpdbaltics'}" class="js-range-input js-day-select">
                                {foreach $daysList as $day}
                                    <option value="{$day}">
                                        {$day}
                                    </option>
                                {/foreach}
                            </select>
                        </td>
                        <td class="text-center">
                            <input class="js-range-input js-product-availability-from-input form-control"
                                   title="{l s='From' mod='dpdbaltics'}"
                                   type="time"
                            >
                        </td>
                        <td class="text-center">
                            <input class="js-range-input js-product-availability-to-input form-control"
                                   title="{l s='To' mod='dpdbaltics'}"
                                   type="time"
                            >
                        </td>
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm js-add-product-availability-btn" type="button">
                                {l s='Add' mod='dpdbaltics'}
                            </button>
                        </td>
                    </tr>
                </tbody>

                <tbody id="productAvailabilityValues">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- HTML templates used in Javascript -->
<div class="hidden" id="dpdAvailabilityRangeTemplates">
    <table>
        <tr class="js-availability-range-block js-availability-range-template">
            <td class="text-center">
                <select title="{l s='Day' mod='dpdbaltics'}" class="js-range-input js-day-select">
                    {foreach $daysList as $day}
                        <option value="{$day}">
                            {$day}
                        </option>
                    {/foreach}
                </select>
            </td>
            <td class="text-center">
                <input class="js-range-input js-product-availability-from-input form-control"
                       title="{l s='From' mod='dpdbaltics'}"
                       type="time"
                >
            </td>

            <td class="text-center">
                <input class="js-range-input js-product-availability-to-input form-control"
                       title="{l s='To' mod='dpdbaltics'}"
                       type="time"
                >
            </td>

            <td class="text-center">
                <button class="btn btn-danger btn-sm js-remove-availability-range-btn" type="button">
                    {l s='Remove' mod='dpdbaltics'}
                </button>

                <input type="hidden" class="js-availability-range-id">
            </td>
        </tr>
    </table>

    <div class="alert alert-danger js-alert-error-template" role="alert">
        <span class="js-message"></span>
    </div>
</div>
