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

$(document).ready(function () {
    var $form = $('#dpd_address_template_form');
    $('#mobile_phone_code').chosen();
    $form.find('input[name="type"]').on('change', toggleAddressFields);

    var isReturnService = $('input[name="type"]:checked').val()
    var $formWrapper = $('.form-wrapper');
    toggleRequiredFields(isReturnService, $formWrapper);

    var $typeInput = $('input[name="type"]');
    $typeInput.on('change', function () {
        isReturnService = $(this).val();
        toggleRequiredFields(isReturnService, $formWrapper);
    });

    $('#dpd_address_template_form').on('submit', function () {
       var valid = true;
        $('[required]').each(function() {
            if ($(this).is(':invalid') || !$(this).val()) {
                valid = false;
                $(this).addClass('empty-input-border');
            }
        });
        if (!valid) {
            showErrorMessage(inputWarningMessage);
            event.preventDefault();
        }
    });

    function toggleAddressFields() {
        var addressType = $form.find('input[name="type"]:checked').val();
        if (typeof addressType === 'undefined') {
            return;
        }

        switch (addressType) {
            case 'company':
                $form.find('#first_name').closest('.form-group').addClass('hidden d-none');
                $form.find('#last_name').closest('.form-group').addClass('hidden d-none');
                $form.find('#company_name').closest('.form-group').removeClass('hidden d-none');

                $form.find('#first_name').val('');
                $form.find('#last_name').val('');
                break;
            case 'individual':
                $form.find('#first_name').closest('.form-group').removeClass('hidden d-none');
                $form.find('#last_name').closest('.form-group').removeClass('hidden d-none');
                $form.find('#company_name').closest('.form-group').addClass('hidden d-none');

                $form.find('#company_name').val('');
                break;
        }
    }

    function toggleRequiredFields(isReturnService, $wrapper) {
        var isRequired = false;
        if (isReturnService === 'return_service') {
            isRequired = true;
        }

        toggleRequiredField('#full_name', $wrapper, isRequired);
        toggleRequiredField('#mobile_phone', $wrapper, isRequired);
        toggleRequiredField('#email', $wrapper, isRequired);
        toggleRequiredField('#dpd_country_id', $wrapper, isRequired);
        toggleRequiredField('#zip_code', $wrapper, isRequired);
        toggleRequiredField('#dpd_city_name', $wrapper, isRequired);
        toggleRequiredField('#address', $wrapper, isRequired);
    }

    function toggleRequiredField(input, $wrapper, isRequired) {
        $wrapper.find(input).attr("required", isRequired);
        $wrapper.find(input).closest('.form-group').find('label').toggleClass("required", isRequired);
    }
});
