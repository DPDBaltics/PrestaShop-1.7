{if $tracking_urls}
    <ul>
    {foreach from=$tracking_urls item=url key=parcel_number}
        <li>
            <a target="_blank" href="{$url}">{$parcel_number}</a>
        </li>
    {/foreach}
    </ul>
{/if}