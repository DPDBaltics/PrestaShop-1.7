$(document).ready(function () {

    var $dpdBlock = $('#dpd-order-panel');
    if (typeof shipment === "undefined") {
        return;
    }
    if (shipment.printed_label === '1') {
        disableInputs(true);
        return;
    }
    $("#target :input").prop("disabled", true);

    $('.js-dpd-datepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
    });

    toggleDocumentReturn();
    $dpdBlock.on('click', '.js-dpd-recipient-detail-edit', editAddressBlockEvent);
    $dpdBlock.on('click', '.js-dpd-recipient-detail-save', saveAddressBlockEvent);
    $dpdBlock.on('change', '.js-recipient-address-select', updateAddressBlockEvent);
    $dpdBlock.on('click', '.js-shipment-action-btn', processSavingEvent);
    $dpdBlock.on('click', '.js-add-parcel-btn', addNewParcelEvent);
    $dpdBlock.on('click', '.js-remove-parcel-btn', removeParcelEvent);
    $dpdBlock.on('click', '.js-toggle-shipment', toggleShipmentEvent);
    $dpdBlock.on('click', '.js-print-label-btn', printLabel);
    $dpdBlock.on('click', '.expand-collapse-pudo-extra-info', togglePudoInfoEvent);
    $dpdBlock.on('click', '.js-pudo-expand', togglePudoSearchOptions);
    $dpdBlock.on('change', '.js-contract-select', togglePudoContainer);
    $dpdBlock.on('change', '.js-contract-select', toggleDeliveryTime);
    $dpdBlock.on('change', 'input[name="DPD_DOCUMENT_RETURN"]', toggleDocumentReturn);

    function editAddressBlockEvent() {
        $('.js-dpd-recipient-detail, .js-dpd-recipient-detail-edit').addClass('hidden d-none');
        $('.recipient-address-container').removeClass('dpd-static-form');
        $('.js-dpd-recipient-detail-input, .js-dpd-recipient-detail-save, .js-edit-receiver-address-info').removeClass('hidden d-none');
        $('.dpd-admin-order-error').removeClass('dpd-admin-order-error').find('input').addClass('missing-required-input');
        $('.js-dpd-recipient-detail-input').parent().removeClass('col-4');
        $('.js-dpd-recipient-detail-input').closest('.form-group').find('label').removeClass('col-4 col-sm-4');
    }

    function saveAddressBlockEvent() {
        var error = false;
        var receiverAddressInputValues = {};

        DPDhideError();

        $('.missing-required-input').removeClass('missing-required-input');

        $('.recipient-address-container').find('.js-dpd-rec-input').each(function () {
            if ($(this).hasClass('dpd-required-input') && $(this).val() === '') {
                error = true;
                $(this).addClass('missing-required-input');
            }

            if (!error) {
                receiverAddressInputValues[$(this).attr('name')] = $(this).val();
            }
        });

        if (error === true) {
            DPDshowError(dpdMessages.dpdRecipientAddressError);

            return;
        }

        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: {
                action: 'changeReceiverAddressBlock',
                dpdReceiverAddress: JSON.stringify(receiverAddressInputValues),
                id_order: id_order,
                ajax: 1
            },
            success: function (response) {
                response = JSON.parse(response);

                if (typeof response.template !== 'undefined') {
                    $('.well.recipient-address-container').replaceWith(response.template);
                    $('.js-dpd-recipient-detail, .js-dpd-recipient-detail-edit').removeClass('hidden d-none');
                    $('.js-dpd-recipient-detail-input, .js-dpd-recipient-detail-save, .js-edit-receiver-address-info').addClass('hidden d-none');
                    $('.recipient-address-container').addClass('dpd-static-form');
                    $('.js-dpd-recipient-detail-input').parent().addClass('col-4');
                    $('.js-dpd-recipient-detail-input').closest('.form-group').find('label').addClass('col-4 col-sm-4');

                    DPDshowSuccessMessage(response.message);
                } else {
                    DPDshowError(response.message);
                }
            }
        });
    }

    /**
     * Process shipments saving
     */
    function processSavingEvent(event) {
        event.preventDefault();

        var $clickedBtn = $(this);
        var action = $clickedBtn.data('action');
        var data = $(this).closest('form').serializeArray();
        addButtonLoadingAnimation();
        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: {
                id_order: id_order,
                id_address_delivery: parseInt($('.js-recipient-address-select option:selected').val()),
                action: action,
                data: data,
            },
            success: function (response) {
                try {
                    response = JSON.parse(response);
                    removeButtonLoadingAnimation();
                    DPDhideError();

                    if (response.status == false) {
                        DPDshowError(response.message);

                        if (response.error && response.error === 'recipient_address_phone_area_error') {
                            var phoneContainer = $('#dpd-order-panel').find('.dpd-recipient-address-phone-area');
                            var phoneInput = phoneContainer.find('input');

                            if (phoneInput.is(':visible')) {
                                phoneInput.addClass('missing-required-input');
                            } else {
                                phoneContainer.addClass('dpd-admin-order-error');
                            }
                        }

                        if (response.error && response.error === 'recipient_address_pudo_error') {
                            $('#dpd-order-panel').find('.js-pickup-point').addClass('dpd-admin-order-error');
                        }

                        return;
                    }

                    if (response.status) {
                        DPDshowSuccessMessage(dpdMessages.successCreation);
                        hideShipmentError(response.id_dpd_shipment);

                    }

                    if ('save_and_print' === $clickedBtn.data('action')) {
                        var location = window.location +
                            '&print_label=1' +
                            '&id_dpd_shipment=' + encodeURIComponent(response.id_dpd_shipment);

                        disableInputs(false);
                        window.open(location, '_blank');
                        return;
                    }

                    if (typeof response.orderReturn !== 'undefined' && response.orderReturn) {
                        displayOrderReturnButton();
                    }
                } catch (e) {
                    DPDshowError(dpdMessages.unexpectedError);
                    removeButtonLoadingAnimation();
                }
            }
        });
    }

    function displayOrderReturnButton() {
        var $dpdBlock = $('#dpd-order-panel');
        var $orderReturnTemplate = getReturnOrderTemplate();
        $dpdBlock.find('.return-link-holder').replaceWith($orderReturnTemplate);
    }

    function updateAddressBlockEvent() {
        var input = $(this);
        var addressId = parseInt(input.val());

        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: {
                action: 'updateAddressBlock',
                id_address_delivery: addressId,
                id_order: id_order,
                ajax: 1
            },
            success: function (response) {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    DPDshowError(dpdMessages.unexpectedError);
                    return;
                }
                if (typeof response.template !== 'undefined') {
                    input.closest('.well').replaceWith(response.template)
                } else {
                    DPDshowError(response.message);
                }
            }
        });
    }

    /**
     * Add new parcel to shipment
     */
    function addNewParcelEvent() {

        var $parcelNr = $('input[name="parcels_number"]');
        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: {
                id_order: id_order,
                parcel_count: $parcelNr.val(),
                action: 'add_parcel',
            },
            success: function (response) {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    DPDshowError(dpdMessages.unexpectedError);
                    return;
                }
                $('#parcel-body').append(response.template);
                $parcelNr.val(parseInt($parcelNr.val()) + 1);
            }
        })
    }

    function printLabel() {
        event.preventDefault();
        var $clickedBtn = $(this);
        var action = $clickedBtn.data('action');
        var shipmentId = $clickedBtn.data('shipment-id');
        var labelFormat = $('select[name="label_format"]').val();
        var labelPosition = $('input[name="label_position"]').val();

        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: {
                id_order: id_order,
                shipment_id: shipmentId,
                action: action,
                labelFormat: labelFormat,
                labelPosition: labelPosition
            },
            success: function (response) {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    DPDshowError(dpdMessages.unexpectedError);
                    return;
                }
                if (response.status) {
                    var location = window.location +
                        '&print_label=1' +
                        '&id_dpd_shipment=' + encodeURIComponent(shipmentId);

                    disableInputs(false);
                    window.open(location);

                } else {
                    DPDshowError(response.message);

                }
            }
        })

    }

    /**
     * Remove selected parcel
     */
    function removeParcelEvent() {
        var $selectedParcel = $(this).closest('.js-parcel');
        $selectedParcel.remove();
    }

    /**
     *
     * @returns {*|jQuery}
     */
    function getReturnOrderTemplate() {
        return $('#orderReturnTemplate').find('.return-link-holder').clone();
    }

    /**
     * Hide shipment error
     *
     * @param {integer|string} idDpdShipment
     */
    function hideShipmentError(idDpdShipment) {
        var $errorBlock = $('#shipment_' + idDpdShipment).find('.js-shipment-errors');
        $errorBlock.find('.js-error-msg').empty();

        $errorBlock.addClass('hidden d-none');
    }

    function addButtonLoadingAnimation() {
        // $('.js-shipment-action-btn').button('loading');
        $(".js-shipment-action-btn").prop("disabled", true);
    }

    function removeButtonLoadingAnimation() {
        // $('.js-shipment-action-btn').button('reset');
        $(".js-shipment-action-btn").prop("disabled", false);
    }

    /**
     * Show/hide shipment block
     */
    function toggleShipmentEvent() {
        var $clickedButton = $(this);

        $clickedButton.closest('.js-shipment-block').find('.panel-body').toggle();
        $clickedButton.closest('.js-shipment-block').find('.panel-footer').toggle();

        var $closeText = $clickedButton.find('.js-shipment-close');
        var $expandText = $clickedButton.find('.js-shipment-expand');

        if ($closeText.hasClass('hidden') || $closeText.hasClass('d-none')) {
            $closeText.removeClass('hidden d-none');
            $expandText.addClass('hidden d-none');
        } else {
            $closeText.addClass('hidden d-none');
            $expandText.removeClass('hidden d-none');
        }
    }

    function disableInputs(addListeners) {
        $("#dpd-shipment-form :input").prop('disabled', true);
        $('.js-printout-format-select').prop('disabled', false);
        $('.js-toggle-shipment').prop('disabled', false);
        $('.js-shipment-save-btn').hide();
        $('.js-print-label-btn').show();
        if (addListeners) {
            $dpdBlock.on('click', '.js-toggle-shipment', toggleShipmentEvent);
            $dpdBlock.on('click', '.js-print-label-btn', printLabel);
        }
    }

    function togglePudoInfoEvent() {
        var $clickedElement = $(this);
        var $parent = $clickedElement.closest('.panel-body');
        var $workHours = $parent.find('.work-hours');
        var $extraInfo = $parent.find('.extra-info');
        if ($workHours.hasClass('hidden') || $workHours.hasClass('d-none')) {
            $clickedElement.text($clickedElement.data('less'));
            $workHours.removeClass('hidden d-none');
            $extraInfo.removeClass('hidden d-none');
        } else {
            $clickedElement.text($clickedElement.data('more'));
            $workHours.addClass('hidden d-none');
            $extraInfo.addClass('hidden d-none');
        }
    }

    function togglePudoSearchOptions() {
        $('.js-pickup-point').removeClass('dpd-admin-order-error');
        var $button = $(this);
        var $parent = $button.closest('.panel');
        if ($parent.find('.search-container').hasClass('hidden') || $parent.find('.search-container').hasClass('d-none')) {
            $parent.removeClass('col-lg-6').addClass('col-lg-12');
            $parent.find('.pudo-info-container').removeClass('col-lg-12').addClass('col-lg-6');
            $parent.find('.search-container').removeClass('hidden d-none');
            $button.text($button.data('close'));
        } else {
            $parent.find('.search-container').addClass('hidden d-none');
            $parent.removeClass('col-lg-12').addClass('col-lg-6');
            $parent.find('.pudo-info-container').removeClass('col-lg-6').addClass('col-lg-12');
            $button.text($button.data('open'));
        }
    }

    function togglePudoContainer() {
        var productId = $(this).val();
        var data = {
            'product_id': productId,
            'action': 'checkIfPudo'
        };
        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: data,
            success: function (response) {
                response = JSON.parse(response);
                $('#documentReturn').toggleClass('hidden', response.isPudo);
                if (response.status) {
                    if (response.isPudo) {
                        $('#pudoTemplate').removeClass('hidden d-none');
                        $('input[name="is_pudo"]').val(true);
                        return;
                    }
                    $('#pudoTemplate').addClass('hidden d-none');
                    $('input[name="is_pudo"]').val(false);
                }
            }
        })
    }

    function toggleDeliveryTime() {
        var productId = $(this).val();
        var data = {
            'product_id': productId,
            'action': 'checkIfHasDeliveryTime'
        };
        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: data,
            success: function (response) {
                response = JSON.parse(response);
                if (response.status) {
                    if (response.hasDelivery) {
                        $('select[name="delivery_time"]').closest('div.row').removeClass('hidden d-none');
                        return;
                    }
                    $('select[name="delivery_time"]').closest('div.row').addClass('hidden d-none');
                }
            }
        })
    }

    function toggleDocumentReturn() {
        var isDocumentReturnSwitchOn = $('#DPD_DOCUMENT_RETURN_on').is(':checked');
        $('input[name="dpd_document_return_number"]').closest('div.row').toggleClass('hidden d-none', !isDocumentReturnSwitchOn);
    }
});

/**
 * Show error
 *
 * @param {string} message
 */
function DPDshowError(message) {
    var $errorBlock = $('#dpd-order-panel').find('.js-error');
    $errorBlock.removeClass('hidden d-none').text(message);

    $('#dpd-order-panel').find('.js-success').addClass('hidden d-none');

    $('html, body').animate({
        scrollTop: $errorBlock.offset().top - 150
    }, 300);
}

function DPDshowSuccessMessage(message) {
    var $successBlock = $('#dpd-order-panel').find('.js-success');
    $successBlock.removeClass('hidden d-none').text(message);
    $('#dpd-order-panel').find('.js-error').addClass('hidden d-none');
    $('html, body').animate({
        scrollTop: $successBlock.offset().top - 150
    }, 300);
}

/**
 * Hides error block if it is displayed and removes its content
 */
function DPDhideError() {
    var $errorBlock = $('#dpd-order-panel').find('.js-error');
    $errorBlock.empty().addClass('hidden');
}
