/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
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
