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
    var productAvailabilityData = new DPDProductAvailabilityData();
    var $availabilityRangeBlock = $('#productAvailabilityBlock');
    var $form = $('#dpd_product_form');
    $availabilityRangeBlock.on('click', '.js-add-product-availability-btn', addAvailabilityRangeEvent);
    $form.on('click', 'button[name="processSaveProductAvailabilities"]', processSaveProductAvailabilitiesEvent);
    $form.on('click', 'button[name="processSaveAndStayProductAvailabilities"]', processSaveProductAvailabilitiesEvent);
    $availabilityRangeBlock.on('click', '.js-remove-availability-range-btn', removeProductAvailabilitiesRangeEvent);
    $availabilityRangeBlock.on('change', '.js-range-input', updateProductAvailabilitiesRangeEvent);

    var availabilityRanges = dpdbaltics.entity.productAvailabilityRanges;
    availabilityRanges.forEach(function (availabilityRange) {
        if (productAvailabilityData.addAvailabilityRange(availabilityRange)) {
            renderAvailabilityRange(availabilityRange);
        }
    });
    
    /**
     * Add zone range to list & save into JS object
     */
    function addAvailabilityRangeEvent()
    {
        var $availabilityBlock = $(this).closest('.js-product-availability-range-block');

        var availabilityRange = {
            id: Math.random().toString(36).substr(2, 6),
            day: $availabilityBlock.find('.js-day-select').val(),
            from: $availabilityBlock.find('.js-product-availability-from-input').val(),
            to: $availabilityBlock.find('.js-product-availability-to-input').val()
        };

        clearAlerts();
        if ((0 === availabilityRange.day.length  && 0 === availabilityRange.from.length) ||
            0 === availabilityRange.to.length
        ) {
            showAlert(dpdbaltics.messages.error.emptyAvailabilityTimeValue, 'error');
            return;
        }

        productAvailabilityData.addAvailabilityRange(availabilityRange);

        renderAvailabilityRange(availabilityRange);
        resetZoneRangeForm();
    }

    /**
     * Render zone range html block
     *
     * @param availabilityRange
     */
    function renderAvailabilityRange(availabilityRange)
    {
        var $availabilityRange = getAvailabilityRangeTemplate();
        $availabilityRange.find('.js-day-select').val(availabilityRange.day);
        $availabilityRange.find('.js-product-availability-from-input').val(availabilityRange.from);
        $availabilityRange.find('.js-product-availability-to-input').val(availabilityRange.to);
        $availabilityRange.find('.js-availability-range-id').val(availabilityRange.id);

        // if (allRanges) {
        //     $zoneRange.find('.js-zip-input').hide();
        // }

        $availabilityRange.removeClass('hidden d-none');

        $('#productAvailabilityValues').prepend($availabilityRange);
    }

    /**
     * Reset zone range form to default state
     */
    function resetZoneRangeForm()
    {
        var $form = $('#zoneRangeForm');

        $form.find('.js-all-ranges-input').prop('checked', true);
        $form.find('.js-zip-input').show();
        $form.find('.js-zip-input').val('');
    }

    /**
     * Get zone range empty HTML template
     *
     * @return {*|jQuery}
     */
    function getAvailabilityRangeTemplate()
    {
        return $('#dpdAvailabilityRangeTemplates').find('.js-availability-range-template').clone();
    }

    /**
     * Clear all alerts
     */
    function clearAlerts()
    {
        $('#dpdAlertBlock').html('');
    }

    /**
     * Process save zone ranges event
     */
    function processSaveProductAvailabilitiesEvent(e)
    {
        var $button = $(this);
        var originalButtonText = $button.html();

        disableSaveButtons();

        $button.text(dpdbaltics.notifications.saveProgress);

        var buttonName = $button.attr('name');

        clearAlerts();

        var params = {
            id_dpd_product: $('input[name="product_id"]').val(),
            time_ranges: productAvailabilityData.productAvailability,
            buttonName: buttonName
        };

        if (!processSaveValidation(params)) {
            $button.html(originalButtonText);
            enableSaveButtons();

            return;
        }

        $.ajax(dpdbaltics.url.zonesControllerUrl, {
            method: 'POST',
            data: params,
            success: function (response) {
                response = JSON.parse(response);

                if (response.status) {
                    if ('processSaveAndStayProductAvailabilities' === buttonName) {
                        showSuccessMessage(dpdbaltics.messages.success.saved);
                        $button.html(originalButtonText);
                        enableSaveButtons();

                        window.location.href = dpdbaltics.url.productAvailabilityControllerUrl + '&id_dpd_product=' + response.id_dpd_product + '&updatedpd_product&conf=4';
                        return;
                    }

                    window.location.href = dpdbaltics.url.productControllerUrl + '&conf=4';
                    return;
                }

                $button.html(originalButtonText);
                enableSaveButtons();

                showAlert(response.error, 'error');
            }
        });
    }

    function disableSaveButtons()
    {
        $('[name=processSaveZoneRanges], [name=processSaveAndStayZoneRanges]').prop('disabled', true);
    }

    function enableSaveButtons()
    {
        $('[name=processSaveZoneRanges], [name=processSaveAndStayZoneRanges]').removeAttr('disabled');
    }

    /**
     * Validate if params sent to controller are valid
     *
     * @param {object} params
     */
    function processSaveValidation(params)
    {
        if (0 === params.time_ranges.length) {
            showAlert(dpdbaltics.messages.error.emptyProductAvailability, 'error');
            return false;
        }

        return true;
    }

    /**
     * Show alert message
     *
     * @param {string} message
     * @param {string} type
     */
    function showAlert(message, type)
    {
        switch (type) {
            case 'error':
                var $alert = $('#dpdAvailabilityRangeTemplates').find('.js-alert-error-template').clone();
                break;
            default:
                return;
        }

        $alert.find('.js-message').text(message);
        $('#dpdAlertBlock').append($alert);
    }

    /**
     * Remove zone range from html & js object
     */
    function removeProductAvailabilitiesRangeEvent()
    {
        var $availabilityBlock = $(this).closest('.js-availability-range-block');
        var availabilityRangeId = $availabilityRangeBlock.find('.js-availability-range-id').val();

        productAvailabilityData.removeAvailabilityRange(availabilityRangeId);

        $availabilityBlock.remove();
    }

    /**
     * Update zone range values in JS object
     */
    function updateProductAvailabilitiesRangeEvent()
    {
        var $availabilityBlock = $(this).closest('.js-availability-range-block');
        var availabilityRangeId = $availabilityBlock.find('.js-availability-range-id').val();

        var updatedValues = {
            day: $availabilityBlock.find('.js-day-select').val(),
            from: $availabilityBlock.find('.js-product-availability-from-input').val(),
            to: $availabilityBlock.find('.js-product-availability-to-input').val()
        };

        productAvailabilityData.updateProductAvailabilityRange(availabilityRangeId, updatedValues);
    }
});
