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

<div class="dpd-on-board-images-container">
    <img class="dpd-on-board-mascot" src="{$dpdPathUri}views/img/on-board/mascot.png" alt="{l s='On-board mascot' mod='dpdbaltics'}">
</div>

<div class="dpd-on-board-icons-container">
    <div class="dpd-on-close-container">
        <div class="dpd-on-board-close dpd-on-board-top-button js-dpd-stop-on-board">
            <div class="leftright"></div>
            <div class="rightleft"></div>
        </div>
    </div>
    <div class="dpd-on-pause-container">
        <div class="dpd-on-board-pause dpd-on-board-top-button js-dpd-pause-on-board">
            <i class="icon-pause"></i>
        </div>
    </div>

    {if !empty($onBoardTemplateData.fastMoveButtons)}
        {foreach $onBoardTemplateData.fastMoveButtons as $fastMoveButton}
            <div class="dpd-on-fast-move-container  {$fastMoveButton->direction}">
                <div
                    class="dpd-on-fast-move dpd-on-board-top-button js-dpd-next-step"
                    data-next-step="{$fastMoveButton->step}"
                >
                    <i class="icon-{$fastMoveButton->direction}"></i>
                </div>
            </div>
        {/foreach}
    {/if}
</div>

<div class="dpd-on-board-text-container">
    <p class="dpd-on-board-heading">{if $onBoardTemplateData.heading}{$onBoardTemplateData.heading}{/if}</p>

    {if !empty($onBoardTemplateData.paragraphs)}
        {foreach $onBoardTemplateData.paragraphs as $paragraph}
            <p class="dpd-on-board-text {if $paragraph->class}{$paragraph->class}{/if}">{$paragraph->text}</p>
        {/foreach}
    {/if}
</div>

{if $onBoardTemplateData.importedMainFilesDataArray}
    <div class="dpd-on-board-main-import-container">
        {foreach $onBoardTemplateData.importedMainFilesDataArray as $importedMainFile}
            <div class="dpd-on-board-main-import-item">
                <i class="{$importedMainFile.iconClass}"></i><span>{$importedMainFile.name}</span>
            </div>
        {/foreach}
    </div>
{/if}

<div class="dpd-on-board-buttons-container clearfix">
    {if !empty($onBoardTemplateData.buttons)}
        {foreach $onBoardTemplateData.buttons as $button}
            <button
                    class="dpd-on-board-button btn {if $button->class}{$button->class}{/if}"
                    {if $button->nextStep}data-next-step="{$button->nextStep}"{/if}
                    {if $button->validateField}data-validate="{$button->validateField}"{/if}
            >
                {$button->text}
            </button>
        {/foreach}
    {/if}
</div>

{if $onBoardTemplateData.manualConfigProgress}
    <p class="dpd-on-board-manual-config-progress">{$onBoardTemplateData.manualConfigProgress}</p>
{/if}