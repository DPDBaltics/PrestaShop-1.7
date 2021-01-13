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
<div>
    <form class="form-horizontal" action="#" id="dpd-shipment-form">
        <div class="col-lg-12">
            <div class="alert alert-danger js-error hidden d-none"></div>
            <div class="alert alert-success js-success hidden d-none"></div>
        </div>

        <div class="col-lg-12">
            <div class="panel card">
                <div class="panel-heading card-header">
                    <i class="icon-user"></i>
                    {l s='Recipient' mod='dpdbaltics'}
                </div>
                <div class="panel-body card-block">
                    {include file='./partials/customer-order-credentials.tpl'}
                </div>
            </div>
            <div id="shipmentTemplate">
                {include file="./partials/shipment.tpl"}
            </div>
            <div id="documentReturn" {if $documentReturnEnabled && !$is_pudo}{else}class="hidden d-none"{/if}>
                {include file="./partials/document-return.tpl"}
            </div>

            {include file="./partials/shipment-footer.tpl"}
        </div>
    </form>
</div>