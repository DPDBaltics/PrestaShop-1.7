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
{foreach $products as $product}
<tr class="js-product">
    <td data-content="{l s='ID' mod='dpdbaltics'}" class="text-center js-product-id" scope="row">{$product.id_product}</td>
    <td data-content="{l s='Product' mod='dpdbaltics'}" class="text-center js-product-name">{$product.name}</td>
    {*todo: if weight by default is not in kg?*}
    <td data-content="{l s='Weight (kg)' mod='dpdbaltics'}" class="text-center js-product-weight">{$product.weight}</td>
    <td data-content="{l s='Parcel' mod='dpdbaltics'}" class="text-center js-product-parcel-number">1</td>
    <td data-content="{l s='Quantity' mod='dpdbaltics'}" class="text-center js-product-quantity">{$product.quantity}</td>
    <td data-content="{l s='Action' mod='dpdbaltics'}" class="text-center js-product-actions product-actions">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group form-group-sm">
                    <label class="control-label col-lg-12 text-center">
                        {l s='Move Quantity' mod='dpdbaltics'}
                    </label>
                    <div class="col-lg-12">
                        <input class="form-control js-product-qty-input"
                               type="number"
                               min="0"
                               title="{l s='Quantity' mod='dpdbaltics'}"
                        >
                        <span class="tooltips js-tooltips">{l s='Too many items. Max %count% product(s) can be moved' mod='dpdbaltics'}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group form-group-sm">
                    <label class="control-label col-lg-12 text-center">
                        {l s='Shipment' mod='dpdbaltics'}
                    </label>
                    <div class="col-lg-12">
                        <select title="{l s='Shipment' mod='dpdbaltics'}" class="js-shipment-select">
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group form-group-sm">
                    <label class="control-label col-lg-12 text-center">
                        {l s='Parcel' mod='dpdbaltics'}
                    </label>
                    <div class="col-lg-12">
                        <select title="{l s='Parcel' mod='dpdbaltics'}" class="js-parcel-select">
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="text-center product-actions">
        <button type="button" class="btn btn-default js-move-product {if $testOrder && !$testMode}disabled{/if}">{l s='Move' mod='dpdbaltics'}</button>
        <div id="deleteParcelBtnTemplate" class="hidden d-none">
            <button type="button" class="btn btn-default js-parcel-delete-btn">
                {l s='Delete' mod='dpdbaltics'}
            </button>
        </div>
    </td>
</tr>
{/foreach}
