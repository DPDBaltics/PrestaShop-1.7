<div class="dpd-document-return-panel panel card">
    <div class="panel-heading card-header">
        <span class="card-header-title">
            {l s='DOCUMENT RETURN' mod='dpdbaltics'}
        </span>
    </div>

    <div class="row form-row">
        <label class="control-label col-xs-2">
            {l s='Enable document return' mod='dpdbaltics'}
        </label>
        <div class="col-xs-4">
            <span class="switch ps-switch prestashop-switch fixed-width-lg">
                <input type="radio"
                       name="DPD_DOCUMENT_RETURN"
                       id="DPD_DOCUMENT_RETURN_on"
                       class="js-document-return ps-switch"
                       value="1">
                <label for="DPD_DOCUMENT_RETURN_on" class="radioCheck">{l s='Yes' mod='dpdbaltics'}</label>
                <input type="radio" name="DPD_DOCUMENT_RETURN"
                       class="ps-switch"
                       id="DPD_DOCUMENT_RETURN_off" value="0"
                       checked="checked">
                <label
                        for="DPD_DOCUMENT_RETURN_off" class="radioCheck">
                    {l s='No' mod='dpdbaltics'}
                </label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>

    <div class="dpd-separator"></div>

    <div class="row form-row">
        <label class="control-label col-xs-2">
            {l s='Document number' mod='dpdbaltics'}
        </label>
        <div class="col-xs-4">
            <input name="dpd_document_return_number" class="form-control fixed-width-lg" type="text"
                   value="{$shipment->document_return_number}">
        </div>
    </div>
</div>