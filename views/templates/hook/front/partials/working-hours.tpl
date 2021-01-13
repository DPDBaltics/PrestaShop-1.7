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

{if isset($workingHours) && !empty($workingHours)}
    <ul>
        {foreach from=$workingHours item=workTime}
            <li>
                <b>{$workTime->getWeekDay()|escape:'htmlall':'UTF-8'}</b> {$workTime->getWorkHoursFormatted()|escape:'htmlall':'UTF-8'}
            </li>
        {/foreach}
    </ul>
{/if}