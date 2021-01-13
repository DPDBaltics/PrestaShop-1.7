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
{if isset($carriers) && !empty($carriers)}
        {include file=$checkbox_list_dir items=$carriers name=$carrierName type='carrier' checked=$allCarriersChecked}
    {else}
        <div class="alert alert-warning">
            {l s='There are no carriers to select.' mod='dpdbaltics'}
        </div>
{/if}
    </div>
</div>
