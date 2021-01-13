/*
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 *  International Registered Trademark & Property of INVERTUS, UAB
 */

$(document).ready(function() {
    var $form = $('#dpd_collection_request_form');

    togglePickupAddressFields();
    toggleReceiverAddressFields();

    $form.find('input[name="pickup_address_type"]').on('change', togglePickupAddressFields);
    $form.find('input[name="receiver_address_type"]').on('change', toggleReceiverAddressFields);
    $form.find('.dpd-prefill-btn').on('click', function () {
        var $select = $(this).siblings('.address_prefill_select').first();
        var prefix = $select.data('target-prefix');
        var $option = $select.find('option:selected');

        if (!$option.val()) {
            return;
        }

        prefillFieldset(prefix, $option);
    });

    toggleDisableForm();

    function togglePickupAddressFields()
    {
        var addressType = $form.find('input[name="pickup_address_type"]:checked').val();
        if (typeof addressType === 'undefined') {
            return;
        }

        switch (addressType) {
            case 'company':
                $form.find('#pickup_address_first_name').closest('.form-group').addClass('hidden d-none');
                $form.find('#pickup_address_last_name').closest('.form-group').addClass('hidden d-none');
                $form.find('#pickup_address_company_name').closest('.form-group').removeClass('hidden d-none');

                $form.find('#pickup_address_first_name').val('');
                $form.find('#pickup_address_last_name').val('');
                break;
            case 'individual':
                $form.find('#pickup_address_first_name').closest('.form-group').removeClass('hidden d-none');
                $form.find('#pickup_address_last_name').closest('.form-group').removeClass('hidden d-none');
                $form.find('#pickup_address_company_name').closest('.form-group').addClass('hidden d-none');

                $form.find('#pickup_address_company_name').val('');
                break;
        }
    }

    function toggleReceiverAddressFields()
    {
        var addressType = $form.find('input[name="receiver_address_type"]:checked').val();
        if (typeof addressType === 'undefined') {
            return;
        }

        switch (addressType) {
            case 'company':
                $form.find('#receiver_address_first_name').closest('.form-group').addClass('hidden d-none');
                $form.find('#receiver_address_last_name').closest('.form-group').addClass('hidden d-none');
                $form.find('#receiver_address_company_name').closest('.form-group').removeClass('hidden d-none');

                $form.find('#receiver_address_first_name').val('');
                $form.find('#receiver_address_last_name').val('');
                break;
            case 'individual':
                $form.find('#receiver_address_first_name').closest('.form-group').removeClass('hidden d-none');
                $form.find('#receiver_address_last_name').closest('.form-group').removeClass('hidden d-none');
                $form.find('#receiver_address_company_name').closest('.form-group').addClass('hidden d-none');

                $form.find('#receiver_address_company_name').val('');
                break;
        }
    }

    function toggleDisableForm()
    {
        var $submitBtn = $('button[name="submitAdddpd_collection_request"]');
        if ($submitBtn.length) {
            return;
        }

        var $form = $('#dpd_collection_request_form');
        $form.find('input').attr('disabled', true);
        $form.find('select').attr('disabled', true);
        $form.find('.btn').attr('disabled', true);
    }

    /**
     * Prefill collection request address from selected option
     *
     * @param {jQuery} $option
     * @param {jQuery} prefix
     */
    function prefillFieldset(prefix, $option)
    {
        $("input[name='" + prefix + "type'][value='" + $option.data('type') + "']").prop('checked', true).trigger('change');
        $("input[id='" + prefix + "reference']").val($option.data('reference'));
        $("input[id='" + prefix + "full_name']").val($option.data('full-name'));
        $("select[id='" + prefix + "mobile_phone_code']").val($option.data('mobile-phone-code')).trigger('chosen:updated');
        $("input[id='" + prefix + "mobile_phone']").val($option.data('mobile-phone'));
        $("input[id='" + prefix + "fix_phone']").val($option.data('fix-phone'));
        $("input[id='" + prefix + "email']").val($option.data('email'));
        $("select[id='" + prefix + "id_ws_country']").val($option.data('dpd-country-id')).trigger('chosen:updated');
        $("input[id='" + prefix + "zip_code']").val($option.data('zip-code'));
        $("input[id='" + prefix + "city']").val($option.data('city'));
        $("input[id='" + prefix + "address']").val($option.data('address'));
        $("input[id='" + prefix + "door_code']").val($option.data('door-code'));
        $("input[id='" + prefix + "additional_info']").val($option.data('additional-info'));
    }
});
