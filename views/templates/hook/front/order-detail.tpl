<form id="order-return-form" action="{$href}" method="post">
    <div class="box">
        <h3>
            {l s='DPD Baltics' d='dpdbaltics'}
        </h3>
        <p>
            {l s='If you wish to return one or more products, please click the button below to download label.' d='dpdbaltics'}
        </p>
        <footer class="form-footer">
            <div class="row">
                <div class="pl-1">
                    <select
                            name="return_template_id"
                            {if !$show_template}style="display: none"{/if}
                            class="selectpicker col-lg-3"
                            data-style="btn-primary"
                    >
                        {foreach $return_template_ids as $key => $return_template_id}
                            <option value="{$key}">
                                {$return_template_id}
                            </option>
                        {/foreach}
                    </select>
                </div>
                <div class="clearfix">&nbsp;</div>
                <div class="pl-1">
                    <button
                            type="submit"
                            class="btn btn-primary col-lg-3"
                    >
                        {l s='Request DPD return' d='dpdbaltics'}
                    </button>
                </div>
            </div>
        </footer>
    </div>
</form>
