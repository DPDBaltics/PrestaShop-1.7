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

<div class="dpd-on-board-section">
    <div id="dpd-on-board"
         class="dpd-on-board-main-container
        {if $onBoardTemplateData.containerClass}
            {$onBoardTemplateData.containerClass}
        {/if}"
    >
        {include file="./on-board-pop-up.tpl"}
    </div>

    {if $onBoardTemplateData.progressBar}
        {include file="./on-board-progress.tpl"}
    {/if}
</div>