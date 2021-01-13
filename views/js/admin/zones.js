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
    var zoneRangesData = new DPDZoneRangesData();
    var zoneRanges = dpdbaltics.entity.zoneRanges;

    zoneRanges.forEach(function (zoneRange) {
        if (zoneRangesData.addZoneRange(zoneRange)) {
            renderZoneRange(zoneRange);
        }
    });
    var $form = $('#dpd_zone_form');
    var $zoneRangeForm = $('#zoneRangeForm');
    var $zoneRangeBlock = $('#zoneRangesBlock');
    $zoneRangeForm.find('.js-all-ranges-input').prop('checked', true);
    $zoneRangeBlock.on('change', '.js-all-ranges-input', toggleZipRangesInputEvent);
    $zoneRangeBlock.on('click', '.js-add-zone-range-btn', addZoneRangeEvent);
    $zoneRangeBlock.on('input', '#zoneRangeValues .js-zip-input', updateZoneRangeEvent);
    $zoneRangeBlock.on('input',  '.js-zip-input', updateZoneCheckBoxEvent);

    $zoneRangeBlock.on('change', '#zoneRangeValues .js-all-ranges-input', updateZoneRangeEvent);
    $zoneRangeBlock.on('click', '#zoneRangeValues .js-remove-zone-range-btn', removeZoneRangeEvent);
    // $zoneRangeBlock.on('click', '#zoneRangeValues .js-remove-zone-range-btn', removeZoneRangeEvent);

    $form.on('click', 'button[name="processSaveZoneRanges"]', processSaveZoneRangesEvent);
    $form.on('click', 'button[name="processSaveAndStayZoneRanges"]', processSaveZoneRangesEvent);

    /**
     * Toggle zip code ranges input on event
     */
    function toggleZipRangesInputEvent()
    {
        var $zoneRangeBlock = $(this).closest('.js-zone-range-block');
        var isChecked = $(this).is(':checked');
        if (isChecked) {
            $zoneRangeBlock.find('.js-zone-range-from').val('');
            $zoneRangeBlock.find('.js-zone-range-to').val('');
        }
    }

    /**
     * Add zone range to list & save into JS object
     */
    function addZoneRangeEvent()
    {
        var $zoneRangeBlock = $(this).closest('.js-zone-range-block');

        var zoneRange = {
            id: Math.random().toString(36).substr(2, 6),
            countryId: parseInt($zoneRangeBlock.find('.js-country-select').val()),
            countryName: $zoneRangeBlock.find('.js-country-select option:selected').text().trim(),
            allRanges: $zoneRangeBlock.find('.js-all-ranges-input').is(':checked') ? 1 : 0,
            zipFrom: $zoneRangeBlock.find('.js-zone-range-from').val(),
            zipTo: $zoneRangeBlock.find('.js-zone-range-to').val()
        };

        clearAlerts();
        if ((0 === zoneRange.allRanges && 0 === zoneRange.zipFrom.length  && 0 === zoneRange.zipTo.length) ||
            -1 === zoneRange.countryId
        ) {
            showAlert(dpdbaltics.messages.error.emptyZoneRangeValue, 'error');
            return;
        }

        zoneRangesData.addZoneRange(zoneRange);

        renderZoneRange(zoneRange);
        resetZoneRangeForm();
    }

    /**
     * Update zone range values in JS object
     */
    function updateZoneRangeEvent()
    {
        var $zoneRangeBlock = $(this).closest('.js-zone-range-block');
        var zoneRangeId = $zoneRangeBlock.find('.js-zone-range-id').val();

        var updatedValues = {
            allRanges: $zoneRangeBlock.find('.js-all-ranges-input').is(':checked') ? 1 : 0,
            zipFrom: $zoneRangeBlock.find('.js-zone-range-from').val(),
            zipTo: $zoneRangeBlock.find('.js-zone-range-to').val()
        };

        zoneRangesData.updateZoneRange(zoneRangeId, updatedValues);
    }

    /**
     * Update zone range values in JS object
     */
    function updateZoneCheckBoxEvent()
    {
        var $zoneRangeBlock = $(this).closest('.js-zone-range-block');
        if ($zoneRangeBlock.find('.js-zone-range-from').val() !== ''
            || $zoneRangeBlock.find('.js-zone-range-to').val() !== '')  {
            $zoneRangeBlock.find('.js-all-ranges-input').prop('checked', false);
        }
    }

    /**
     * Remove zone range from html & js object
     */
    function removeZoneRangeEvent()
    {
        var $zoneRangeBlock = $(this).closest('.js-zone-range-block');
        var zoneRangeId = $zoneRangeBlock.find('.js-zone-range-id').val();

        zoneRangesData.removeZoneRange(zoneRangeId);

        $zoneRangeBlock.remove();
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
     * Render zone range html block
     *
     * @param {object} zoneRange
     */
    function renderZoneRange(zoneRange)
    {
        var $zoneRange = getZoneRangeTemplate();
        var allRanges = !!parseInt(zoneRange.allRanges);
        $zoneRange.find('.js-zone-range-country').text(zoneRange.countryName);
        $zoneRange.find('.js-all-ranges-input').prop('checked', allRanges);
        $zoneRange.find('.js-zone-range-from').val(zoneRange.zipFrom);
        $zoneRange.find('.js-zone-range-to').val(zoneRange.zipTo);
        $zoneRange.find('.js-zone-range-id').val(zoneRange.id);

        // if (allRanges) {
        //     $zoneRange.find('.js-zip-input').hide();
        // }

        $zoneRange.removeClass('hidden d-none');

        $('#zoneRangeValues').prepend($zoneRange);
    }

    /**
     * Get zone range empty HTML template
     *
     * @return {*|jQuery}
     */
    function getZoneRangeTemplate()
    {
        return $('#dpdZoneRangeTemplates').find('.js-zone-range-template').clone();
    }

    /**
     * Process save zone ranges event
     */
    function processSaveZoneRangesEvent(e)
    {
        var $button = $(this);
        var originalButtonText = $button.html();

        disableSaveButtons();

        $button.text(dpdbaltics.notifications.saveProgress);

        var buttonName = $button.attr('name');

        clearAlerts();

        var params = {
            zone_name: $('#zone_name').val(),
            zone_id: $('#id_dpd_zone').val(),
            zone_ranges: zoneRangesData.zoneRanges,
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
                    $('#id_dpd_zone').val(response.id_dpd_zone);

                    if ('processSaveAndStayZoneRanges' === buttonName) {
                        showSuccessMessage(dpdbaltics.messages.success.saved);
                        $button.html(originalButtonText);
                        enableSaveButtons();

                        if((typeof(onBoard) != "undefined" && onBoard !== null) &&
                            (typeof(response.onBoardStepTemplate) != "undefined" && response.onBoardStepTemplate)
                        ) {
                            var $onBoardSection = $('.dpd-on-board-section');

                            $onBoardSection.find('#dpd-on-board').html(response.onBoardStepTemplate.onBoardTemplate);

                            if(typeof response.onBoardStepTemplate.progressBarTemplate !== 'undefined') {
                                if ($onBoardSection.find('.dpd-on-board-bottom-progress-container').length) {
                                    $onBoardSection.find('.dpd-on-board-bottom-progress-container').replaceWith(
                                        response.onBoardStepTemplate.progressBarTemplate
                                    );
                                } else {
                                    $onBoardSection.append(response.onBoardStepTemplate.progressBarTemplate);
                                }
                            }

                            $('#subtab-AdminDPDBalticsProducts').addClass('on-board-border-secondary-color');
                            $('button[name="processSaveZoneRanges"].on-board-border-secondary-color').removeClass('on-board-border-secondary-color');
                        }

                        window.location.href = dpdbaltics.url.zonesControllerUrl + '&id_dpd_zone=' + response.id_dpd_zone + '&updatedpd_zone&conf=4';
                        return;
                    }

                    window.location.href = dpdbaltics.url.zonesControllerUrl + '&conf=4';
                    return;
                }

                $button.html(originalButtonText);
                enableSaveButtons();

                showAlert(response.error, 'error');
            }
        });
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
                var $alert = $('#dpdZoneRangeTemplates').find('.js-alert-error-template').clone();
                break;
            default:
                return;
        }

        $alert.find('.js-message').text(message);
        $('#dpdAlertBlock').append($alert);
    }

    /**
     * Clear all alerts
     */
    function clearAlerts()
    {
        $('#dpdAlertBlock').html('');
    }

    /**
     * Validate if params sent to controller are valid
     *
     * @param {object} params
     */
    function processSaveValidation(params)
    {
        if (0 === params.zone_name.length) {
            showAlert(dpdbaltics.messages.error.emptyZoneName, 'error');
            return false;
        }

        if (0 === params.zone_ranges.length) {
            showAlert(dpdbaltics.messages.error.emptyZoneRanges, 'error');
            return false;
        }

        return true;
    }

    function disableSaveButtons()
    {
        $('[name=processSaveZoneRanges], [name=processSaveAndStayZoneRanges]').prop('disabled', true);
    }

    function enableSaveButtons()
    {
        $('[name=processSaveZoneRanges], [name=processSaveAndStayZoneRanges]').removeAttr('disabled');
    }
});
