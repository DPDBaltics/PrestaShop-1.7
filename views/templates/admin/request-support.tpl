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

<div class="panel">
    <div class="panel-heading">
        <i class="icon-phone-sign"></i>
        {l s='Request support' mod='dpdbaltics'}
    </div>

    <div class="form-wrapper">

        {foreach $requestSupportData as $requestSupportType => $requestSupportItem}
            <div class="form-group clearfix">
                <div id="{$requestSupportType}">
                    <label class="control-label col-lg-3">
                        {$requestSupportItem.name}
                    </label>

                    <div class="col-lg-9">
                    <span class="fixed-width-lg">
                        {$requestSupportItem.value}
                    </span>
                    </div>
                </div>
            </div>
        {/foreach}

        {if $isLogsOn}
            <div class="form-group clearfix">
                <div id="request-support-logs">
                    <label class="control-label col-lg-3">
                        {l s='DPD Logs' mod='dpdbaltics'}
                    </label>

                    <form action="{$downloadLogsAction}" method="post" enctype="multipart/form-data">
                        <div class="col-lg-9">
                            <button
                                    class="btn btn-default"
                                    name="submitDpdDownloadLogs"
                                    type="submit"
                            >
                                <i class="process-icon-download"></i>
                                {l s='Download logs' mod='dpdbaltics'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        {else}
            <div class="clearfix">
                <div class="help-block col-lg-12">
                    <div class="alert alert-info">
                        <span>{l s='Logs are turned off. Please reproduce problem with logs turned on' mod='dpdbaltics'}</span>
                    </div>
                </div>
            </div>
        {/if}

        <div>

        </div>

        <div class="clearfix">
            <div class="help-block col-lg-12">
                <div class="alert alert-info">
                    <span>{l s='Please give this information to your DPD support.' mod='dpdbaltics'}</span>
                </div>
            </div>
        </div>

        <div class="clearfix">
            <div class="help-block col-lg-12">
                <div class="alert alert-info">
                    <p>{l s='Please attach as much as possible data that can help to solve your issue:' mod='dpdbaltics'}</p>
                    <p>{l s='Steps' mod='dpdbaltics'}</p>
                    <p>{l s='Pictures' mod='dpdbaltics'}</p>
                    <p>{l s='Videos' mod='dpdbaltics'}</p>
                    <p>{l s='Logs' mod='dpdbaltics'}</p>
                </div>
            </div>
        </div>
    </div>
</div>