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

<div class="search-block-wrapper">
    <select class="col-xs-{if isset($col)}{$col|intval}{else}4{/if} searchable-multiselect search-block hidden" name="{if isset($name)}{$name|escape:'htmlall':'UTF-8'}{else}multiselect[]{/if}" multiple {if $disabled}disabled{/if}>
        {foreach from=$availableElements item=element}
            {if isset($includeAllOption) && $includeAllOption}
                <option value="0"{if $allSelected} selected{/if}>{l s='All' mod='dpdbaltics'}</option>

                {*We only need this option once*}
                {$includeAllOption = false}
            {/if}

            <option value="{$element.id|escape:'htmlall':'UTF-8'}"
                    {if $element.selected && !$allSelected} selected{/if}
            >
                {$element.name|escape:'htmlall':'UTF-8'}
            </option>
        {/foreach}
    </select>
    <p class="help-block">{l s='Start typing to see suggestions' mod='dpdbaltics'}</p>
</div>
