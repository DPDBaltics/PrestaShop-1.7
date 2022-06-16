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

{if isset($currentController) && $currentController === 'supercheckout' || isset($is_super_checkout)}
    <div id="supercheckout-empty-page-content" class="supercheckwout-empty-page-content" style="display:block">
        {if isset($messages) && !empty($messages)}
            {foreach from=$messages item=msg}
                <div class="permanent-warning">{$msg|escape:'htmlall':'UTF-8'}</div>
            {/foreach}
        {/if}
    </div>
    {else}
    <div class="dpd-message-container">
        <div class="alert alert-{$messageType|escape:'htmlall':'UTF-8'} {if isset($messageType_pudo)} alert-danger{/if} {if !isset($displayMessage)}dpd-hidden{/if}" role="alert">
            <p>&nbsp;</p>
            <ol>
                {if isset($messages) && !empty($messages)}
                    {foreach from=$messages item=msg}
                        <li>{$msg|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                {/if}
            </ol>
        </div>
    </div>

{/if}

