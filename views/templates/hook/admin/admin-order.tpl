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

<div class="row">
    <div class="col-lg-12">
        <div class="panel card" id="dpd-order-panel">
            <div class="panel-heading card-header">
                <img src="{$dpdLogoUrl|escape:'htmlall':'UTF-8'}">

                {l s='dpdbaltics shipping' mod='dpdbaltics'}
                <span class="badge">
                    <a class="dpd-extra-expand form-hidden">
                        {l s='[expand]' mod='dpdbaltics'}
                    </a>
                </span>
                {if $testOrder}
                    <span class="badge">
                        {l s='TEST ORDER' mod='dpdbaltics'}
                    </span>
                {/if}
            </div>
            {if $shipment->return_pl_number}
                <div class="alert alert-warning" role="alert">
                    {l s='Return label has been created by customer!'}
                </div>
            {/if}
            <div class="panel-body card-body">
                {if !empty($shipment)}
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-center">{l s='Shipment' mod='dpdbaltics'}</th>
                                <th class="text-center">{l s='Parcels' mod='dpdbaltics'}</th>
                                <th class="text-center">{l s='Unique products' mod='dpdbaltics'}</th>
                                <th class="text-center">{l s='Label printed' mod='dpdbaltics'}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-center">{l s='id: ' mod='dpdbaltics'} {$shipment->id}</td>
                                <td class="text-center">{$shipment->num_of_parcels|intval}</td>
                                <td class="text-center">{$total_products|intval}</td>
                                <td class="text-center">{if $shipment->printed_label}{l s='Yes' mod='dpdbaltics'}{else}{l s='No' mod='dpdbaltics'}{/if}</td>
                                <td>
                                    <a href="#"
                                       class="btn btn-default pull-right js-print-label-btn"
                                       style="display: none"
                                       data-action="print"
                                       data-shipment-id={$shipment->id}
                                    >
                                        <i class="process-icon-save"></i>
                                        {if 'download' == $printLabelOption}
                                            {l s='Download label' mod='dpdbaltics'}
                                        {else}
                                            {l s='Print label' mod='dpdbaltics'}
                                        {/if}
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                {/if}
            </div>

            <div class="extra-shipment-container row">
                {include file='./admin-order-expanded.tpl'}
            </div>

            <div class="clearfix">&nbsp;</div>
            <div class="col-lg-12 return-link-holder">
                {if isset($orderReturn) && $orderReturn}
                    <a href="{$orderReturnLink|escape:'htmlall':'UTF-8'}" class="btn btn-default">
                        {l s='Initiate return' mod='dpdbaltics'}
                    </a>
                {/if}
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

{*<script>*}
{*    var dpdShipments = JSON.parse('{$encodedShipments}');*}
{*</script>*}
