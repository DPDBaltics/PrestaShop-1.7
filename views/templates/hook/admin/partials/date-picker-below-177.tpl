<div class="col-lg-4">
    <div class="form-row row">
        <label class="control-label col-lg-12" for="">
            {l s='Shipment date' mod='dpdbaltics'}
        </label>
        <div class="input-group col-lg-12">
            <input type="text"
                   name="date_shipment"
                   class="js-dpd-datepicker"
                   title="{l s='Shipment date' mod='dpdbaltics'}"
                   value="{$shipment->date_shipment}"
            >
            <div class="input-group-addon">
                <i class="icon-calendar-o"></i>
            </div>
        </div>
    </div>
</div>

