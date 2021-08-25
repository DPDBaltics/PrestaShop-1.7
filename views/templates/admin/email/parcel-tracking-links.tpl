{if $tracking_urls}
    <ul>
    {foreach from=$tracking_urls item=url}
        <li>
            <a target="_blank" href="{$url}">{$url}</a>
        </li>
    {/foreach}
    </ul>
{/if}