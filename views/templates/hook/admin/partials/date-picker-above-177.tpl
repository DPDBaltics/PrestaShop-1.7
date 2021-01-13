<div class="col-lg-4 form-group">
    <div class="form-row row">
        <label class="control-label col-lg-12" for="">
            {l s='Shipment date' mod='dpdbaltics'}
        </label>
        <div class="input-group  datepicker col-lg-12">
            <input type="text"
                   data-format="YYYY-MM-DD H:m:s"
                   name="date_shipment"
                   class="js-dpd-datepicker form-control"
                   title="{l s='Shipment date' mod='dpdbaltics'}"
                   value="{$shipment->date_shipment}"
            >
            <div class="input-group-text"><i class="material-icons">date_range</i></div>
        </div>
    </div>
</div>