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
<div class="container dpd-checkout-delivery-time--container dpd-delivery-time-block">
    <div class="row form-group">
        <div class="col-lg-6 col-12 col-sm-9">
            <p>{l s='Desirable delivery time' mod='dpdbaltics'}</p></div>
        <div class="col-lg-4 col-sm-12 dpd-input-wrapper dpd-select-wrapper hasValue small-padding-sm-right">
            <select
                    class="chosen-select form-control-chosen"
                    name="dpd-delivery-time"
                    required="required">
                    {html_options options=$deliveryTimes selected=$selectedDeliveryTime}
            </select>
            <div class="control-label dpd-input-placeholder dpd-select-placeholder">
                {l s='Time' mod='dpdbaltics'}
            </div>
        </div>
    </div>
</div>
<hr class="delivery-time-hr">

