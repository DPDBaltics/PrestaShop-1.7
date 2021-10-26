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
