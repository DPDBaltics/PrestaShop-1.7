{*
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 *
 *}

<div class="form-group">
    <div class="col-xs-12">
        <div class="form-control-static row swap-container">
            <div class="hidden dpd-group-type-container">
                {foreach $swap.options.query AS $option}
                    {if is_object($option)}
                        <span class="type-holder" data-type="{$option[$swap.options.type]|escape:'html':'utf-8'}" data-value="{$option->$swap.options.id|intval}">{$option->$swap.options.name|escape:'html':'utf-8'}</span>
                    {else}
                        <span class="type-holder" data-type="{$option[$swap.options.type]|escape:'html':'utf-8'}" data-value="{$option[$swap.options.id]|intval}">{$option[$swap.options.name]|escape:'html':'utf-8'}</span>
                    {/if}
                {/foreach}
            </div>
            <div class="col-xs-6">
                <select {if $swap.disabled}disabled="disabled"{/if} {if isset($swap.size)}size="{$swap.size|escape:'html':'utf-8'}"{/if}{if isset($swap.onchange)} onchange="{$swap.onchange|escape:'html':'utf-8'}"{/if} class="{if isset($swap.class)}{$swap.class|escape:'html':'utf-8'} {/if}availableSwap" name="{$swap.name|escape:'html':'utf-8'}_available[]" multiple="multiple">
                    {foreach $swap.options.query AS $option}
                        {if is_object($option)}
                            {if !in_array($option->$swap.options.id, $swap.fields_value)}
                                <option data-type="{$option[$swap.options.type]|escape:'html':'utf-8'}" value="{$option->$swap.options.id|intval}">{$option->$swap.options.name|escape:'html':'utf-8'}</option>
                            {/if}
                        {elseif $option == "-"}
                            <option value="">-</option>
                        {else}
                            {if !in_array($option[$swap.options.id], $swap.fields_value)}
                                <option data-type="{$option[$swap.options.type]|escape:'html':'utf-8'}" value="{$option[$swap.options.id]|intval}">{$option[$swap.options.name]|escape:'html':'utf-8'}</option>
                            {/if}
                        {/if}
                    {/foreach}
                </select>
                <a href="#" {if $swap.disabled}disabled="disabled"{/if} class="btn btn-default btn-block addSwapDPD">{l s='Add' mod='dpdbaltics'} <i class="icon-arrow-right"></i></a>
            </div>
            <div class="col-xs-6">
                <select {if $swap.disabled}disabled="disabled"{/if} {if isset($swap.size)}size="{$swap.size|escape:'html':'utf-8'}"{/if}{if isset($swap.onchange)} onchange="{$swap.onchange|escape:'html':'utf-8'}"{/if} class="{if isset($swap.class)}{$swap.class|escape:'html':'utf-8'} {/if}selectedSwap" name="{$swap.name|escape:'html':'utf-8'}_selected[]" multiple="multiple">
                    {foreach $swap.options.query AS $option}
                        {if is_object($option)}
                            {if in_array($option->$swap.options.id, $swap.fields_value)}
                                <option data-type="{$option[$swap.options.type]|escape:'html':'utf-8'}" value="{$option->$swap.options.id|escape:'html':'utf-8'}">
                                    {$option->$swap.options.name|escape:'html':'utf-8'}
                                </option>{/if}
                        {elseif $option == "-"}
                            <option value="">-</option>
                        {else}
                            {if in_array($option[$swap.options.id], $swap.fields_value)}
                                <option data-type="{$option[$swap.options.type]|escape:'html':'utf-8'}" value="{$option[$swap.options.id]|escape:'html':'utf-8'}">
                                    {$option[$swap.options.name]|escape:'html':'utf-8'}
                                </option>
                            {/if}
                        {/if}
                    {/foreach}
                </select>
                <a href="#" {if $swap.disabled}disabled="disabled"{/if} class="btn btn-default btn-block removeSwapDPD"><i class="icon-arrow-left"></i> {l s='Remove' mod='dpdbaltics'}</a>
            </div>
        </div>
    </div>
</div>
