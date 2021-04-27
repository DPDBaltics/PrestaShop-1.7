<div class="container dpd-checkout-phone-container dpd-phone-block{if $isAbove177} new-version {/if}">
    <div class="row form-group">
        <div class="col-lg-5 col-12 ">
            <p class="form-control-label">{l s='This a phone number that will be used for deliveries' mod='dpdbaltics'}</p></div>
        <div class="col-lg-3 col-4 col-sm-2 dpd-input-wrapper dpd-select-wrapper hasValue small-padding-sm-right css-dpd-phone-prefix">
            <select
                    class="chosen-select form-control form-control-chosen"
                    name="dpd-phone-area"
                    required="required">
                    {html_options options=$dpdPhoneArea selected=$contextPrefix}
            </select>
            <div class="control-label dpd-input-placeholder dpd-select-placeholder">
                {l s='Code' mod='dpdbaltics'}
            </div>
        </div>

        <div class="col-lg-4 col-8 dpd-input-wrapper{if isset($dpdPhone) && $dpdPhone} hasValue{/if} small-padding-sm-left">
            <input name="dpd-phone" type="text"  class="form-control" {if isset($dpdPhone) && $dpdPhone}value="{$dpdPhone}"{/if}>
            <div class="dpd-input-placeholder" for="dpd-phone">{l s='Phone' mod='dpdbaltics'}</div>
        </div>
    </div>
</div>
<hr class="phone-block-hr">

