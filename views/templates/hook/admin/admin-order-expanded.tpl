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
