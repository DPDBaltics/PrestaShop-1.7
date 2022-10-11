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
{if $show_shop_list === 'BLOCK'}
    <div {if count($street_list) < 5} class="dpd-hidden" {/if}>
        <input name="dpd-street">
        <div class="control-label dpd-input-placeholder dpd-select-placeholder">
            {l s='Street' mod='dpdbaltics'}
        </div>
    </div>
{else}
    <div class="dpd-select-wrapper">
        <select name="dpd-street" class="chosen-select form-control-chosen">
            {if !empty($street_list)}
                {foreach from=$street_list key=company item=street}
                    <option {if isset($selected_street) && $selected_street === $street}selected{/if}
                            value="{$street|escape:'htmlall':'UTF-8'}">
                        {$company|escape:'htmlall':'UTF-8'} - {$street|escape:'htmlall':'UTF-8'}
                    </option>
                {/foreach}
            {/if}
        </select>
        <div class="control-label dpd-input-placeholder dpd-select-placeholder">
            {l s='Street' mod='dpdbaltics'}
        </div>
    </div>
{/if}
