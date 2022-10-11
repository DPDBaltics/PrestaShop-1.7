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

<div class="panel">
    <div class="panel-heading">
        <span class="check-box-all-message">{l s='Check all' mod='dpdbaltics'}</span>
        <input type="checkbox" name="checkme_{$type|escape:'htmlall':'UTF-8'}" class="checkme" {if $checked}checked{/if}>
        <p class="help-block {if !$checked}hidden d-none{/if}">{l s='All current and future %s are selected.' mod='dpdbaltics' sprintf=[$name|escape:'htmlall':'UTF-8']}</p>
        <div class="clearfix">&nbsp;</div>
    </div>
    <div class="panel-body">
        <div class="col-lg-12">
            {$columns = 12}
{*            {if Configuration::get(Config::CHECKBOX_COLUMNS_COUNT)}*}
{*                {$divider = Configuration::get(Config::CHECKBOX_COLUMNS_COUNT)}*}
{*                {if (count($items) > $divider) && count($items) % $divider}*}
{*                    {$columns = 6}*}
{*                {/if}*}
{*            {/if}*}
            {foreach from=$items item=value}
                <div class="checkbox col-lg-{$columns|intval} checkbox-list-option">
                    <label>
                        <input
                                type="checkbox"
                                class="checkbox-input"
                                name="{if isset($value['id_reference'])}dpd-carrier[{$value['id_reference']|intval}]{elseif isset($value['id_module'])}dpd-payment-method[{$value['id_module']|intval}]{/if}"
                                value="{if isset($value['id_reference'])}{$value['id_reference']|intval}{elseif isset($value['id_module'])}{$value['id_module']|intval}{/if}"
                                {if isset($value['selected']) && $value['selected']}checked{/if}
                        >
                        {if isset($value['tooltip'])}
                        <span class="label-tooltip"
                              data-toggle="tooltip"
                              data-html="true"
                              title="
                            {if is_array($value['tooltip'])}
                                {foreach from=$value['tooltip'] item=tooltip_value key=tooltip_title}
                                    <span>{$tooltip_title|escape:'htmlall':'UTF-8'}</span> {$tooltip_value|escape:'htmlall':'UTF-8'}
                                    <br>
                                {/foreach}
                            {else}
                                {$value['tooltip']|escape:'htmlall':'UTF-8'}
                            {/if}"
                        >
                        {/if}
                            {$value['name']|escape:'htmlall':'UTF-8'}
                            {if isset($value['tooltip'])}
                            </span>
                        {/if}
                    </label>
                </div>
            {/foreach}
        </div>
    </div>
</div>