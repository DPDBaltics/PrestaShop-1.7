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
