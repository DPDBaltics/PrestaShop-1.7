{*
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 *}

<div id="content" class="dpd-on-board-bottom-progress-container bootstrap with-tabs">
    <div class="dpd-on-board-bottom-progress">
        {for $section = 1 to $onBoardTemplateData.progressBar->sections}
            <div class="dpd-on-board-progress-third">
                <div class="dpd-on-board-checkpoint">
                    {if $section < $onBoardTemplateData.progressBar->currentSection || ($section === $onBoardTemplateData.progressBar->currentSection && $onBoardTemplateData.progressBar->currentStep > 1)}
                        <img class="dpd-on-board-checkpoint-logo" src="{$dpdPathUri}views/img/on-board/logo.png" alt="{l s='DPD logo' mod='dpdbaltics'}">
                    {else}
                        {l s='%s' sprintf=[$section] mod='dpdbaltics'}
                    {/if}
                </div>

                {if $onBoardTemplateData.progressBar->currentSection === $section}
                    <div class="dpd-on-board-truck {$onBoardTemplateData.progressBar->truckProgressClass}">
                        <img src="{$dpdPathUri}views/img/on-board/truck.png" alt="{l s='On-board truck' mod='dpdbaltics'}">
                    </div>
                {/if}

                {if $section == 3}
                    <div class="dpd-on-board-checkpoint last-checkpoint">
                        <img class="dpd-on-board-reward" src="{$dpdPathUri}views/img/on-board/reward.png" alt="{l s='On-board reward' mod='dpdbaltics'}">
                    </div>
                {/if}
            </div>
        {/for}
    </div>
</div>