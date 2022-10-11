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

