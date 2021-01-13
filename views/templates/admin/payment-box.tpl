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
    <div class="col-lg-6">
        {if isset($paymentMethods) && !empty($paymentMethods)}
            {$items = $paymentMethods}
            {include file=$checkbox_list_dir name=$paymentsName type='payment' checked=$allPaymentsChecked}
        {else}
            <div class="alert alert-warning">
                {l s='There are no payment methods to select' mod='dpdbaltics'}
            </div>
        {/if}
    </div>
</div>
