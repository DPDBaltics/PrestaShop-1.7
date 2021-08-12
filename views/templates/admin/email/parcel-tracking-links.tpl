{if $tracking_urls}
    {foreach from=$tracking_urls item=url}
        <a target="_blank" href="{$url}">{$url}</a> <br/>
    {/foreach}
{/if}