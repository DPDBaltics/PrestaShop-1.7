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

<div id="product-page-carriers">
    {foreach $carriers as $carrier}
        <div class="product-carrier-container clearfix container">
            <div class="row">
                <div class="col-sm-4 image-container">
                    <img class="product-carrier-image" src="{$carrier.carrier_logo}">
                </div>

                <div class="col-sm-8 price-container">
                    <p class="product-carrier-name">
                        {$carrier.name}
                    </p>
                    <p class="product-carrier-text">
                        {l s='from ' mod='dpdbaltics'}
                        {$carrier.shipping_cost}
                    </p>
                </div>
            </div>
        </div>
    {/foreach}
</div>