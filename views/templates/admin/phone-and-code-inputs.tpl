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

<select
        id="{$inputName}_code"
        class="dpd-phone-and-code fixed-width-sm chosen hidden d-none"
        name="{$inputName}_code"
        required="required">
        {html_options class='fixed-width-xlg' options=$countryCallingCodes selected=$countryCallingCode}
 </select>
<input
        id="{$inputName}"
        class="dpd-phone-and-code fixed-width-xlg"
        type="text"
        name="{$inputName}"
        required="required"
        placeholder="Phone"
        {if isset($phoneNumber) && $phoneNumber} value="{$phoneNumber}" {/if}
>