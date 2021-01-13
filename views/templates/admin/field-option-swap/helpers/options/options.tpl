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

{extends file="helpers/options/options.tpl"}

{block name="input" append}
    {if $field['type'] == 'swap'}
        <div class="col-lg-5 {if isset($field['class'])}{$field['class']}{/if}" style="max-width: 500px;">
            {include file="../../../partials/swap.tpl"}
        </div>
    {/if}
    {if $field['type'] == 'custom_image_select'}
        <div class="col-lg-5 {if isset($field['class'])}{$field['class']}{/if}" style="max-width: 500px;">
            {include file="../../../partials/custom-image-select.tpl"}
        </div>
    {/if}
{/block}
