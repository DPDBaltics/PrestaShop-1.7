<div class="container dpd-checkout-delivery-time--container dpd-delivery-time-block">
    <div class="row">
        <div class="col-lg-6 col-12 ">
            <p>{l s='Desirable delivery time' mod='dpdbaltics'}</p></div>
        <div class="col-lg-4 dpd-input-wrapper dpd-select-wrapper hasValue small-padding-sm-right">
            <select
                    class="chosen-select form-control-chosen"
                    name="dpd-delivery-time"
                    required="required">
                    {html_options options=$deliveryTimes selected=$selectedDeliveryTime}
            </select>
            <div class="control-label dpd-input-placeholder dpd-select-placeholder">
                {l s='Time' mod='dpdbaltics'}
            </div>
        </div>
    </div>
</div>
<hr class="delivery-time-hr">

