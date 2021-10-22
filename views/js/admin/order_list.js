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
    $('.js-bulk-action-submit-btn-dpd-labels').on('click', function (e){
        var form = $('form[name="order"]');
        form.submit(function() {
            form.attr('target', '_blank');
            return true;
        });
    })

    $(document).on('click', '.print-dpd-label', function () {
        toggleMessageBoxPrinted();
        $.ajax(dpdHookAjaxShipmentController, {
            data: {
                id_order: $(this).data('id-order'),
                action: 'printLabelFromList',
                ajax: 1
            },
            success: function (response) {
                response = JSON.parse(response);
                // toggleMessageBoxResult(response.success, response.message);

                // $.each(response.icon_replacers, function (key, value) {
                //     replaceIcon(value.id_order, value.icon_replacer);
                // });

                if (response.status) {
                    var location = window.location +
                        '&print_label=1' +
                        '&id_dpd_shipment=' + encodeURIComponent(response.id_dpd_shipment);
                    if (!is_label_download_option) {
                        window.open(location, '_blank');
                    } else {
                        window.location.href = location;
                    }
                    hideMessageBoxPrinted()
                }

                if (!response.status) {
                    toggleMessageBoxResult(false, response.message);
                }
                return;
            },
            error: function(xhr, ajaxOptions, thrownError) {
                toggleMessageBoxResult(false, xhr.statusText);
            }
        });
        return false;
    });

    $(document).on('click', '#download-selected-labels', function (event) {
        event.preventDefault();
        var selectedOrderIdArray = [];

        $(".row-selector input:checked").each(function()
        {
            selectedOrderIdArray.push($(this).val());
        });

        toggleMessageBoxPrinted();
        $.ajax(dpdHookAjaxShipmentController, {
            data: {
                order_ids: JSON.stringify(selectedOrderIdArray),
                action: 'printMultipleLabelsFromList',
                ajax: 1
            },
            success: function (response) {
                response = JSON.parse(response);
                toggleMessageBoxResult(response.status, response.message);
                if (response.shipment_ids) {
                    var location = window.location +
                        '&print_multiple_labels=1' +
                        '&shipment_ids=' + encodeURIComponent(response.shipment_ids);
                    window.open(location, '_blank');
                    hideMessageBoxPrinted();
                    return;
                }

                if (!response.status) {
                    toggleMessageBoxResult(false, response.message);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                toggleMessageBoxResult(false, xhr.statusText);
            }
        });
    });

    function toggleMessageBoxPrinted()
    {
        var messageBox = $('.message-text-box');
        messageBox.html(shipmentIsBeingPrintedMessage);
        messageBox.addClass('alert-warning');
        messageBox.removeClass('alert-danger');
        messageBox.removeClass('alert-success');
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('.label-printing-message').show();
    }

    function hideMessageBoxPrinted() {
        $('.label-printing-message').hide();
    }

    function toggleMessageBoxResult(success, message) {
        var messageBox = $('.message-text-box');
        messageBox.html(message);
        if (success) {
            messageBox.removeClass('alert-warning');
            messageBox.addClass('alert-success');
        }

        if (!success) {
            messageBox.removeClass('alert-warning');
            messageBox.addClass('alert-danger');
        }
    }

    function replaceIcon(idOrder, iconReplacer)
    {
        $('.dpd-icon-container[data-id-order="' + idOrder + '"]').html(iconReplacer);
    }

    $('.bulk-actions .dropdown-menu').append(downloadSelectedLabelsButton);
});
