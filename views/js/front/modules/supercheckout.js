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
function isDPdCarrierSelected() {
    if (document.querySelector('.supercheckout_shipping_option:checked')) {
        var selectedCarrierValue = parseInt(document.querySelector('.supercheckout_shipping_option:checked').value);

        if (!dpd_carrier_ids.includes(selectedCarrierValue)) {
            return true;
        }
    }

    return false;
}

$( document ).ajaxComplete(function( event, request, settings ) {

    if (currentController !== 'supercheckout') {
        return;
    }

    if (!settings.data) {
        return;
    }

    if (!isDPdCarrierSelected()) {
        return;
    }

    var method = DPDgetUrlParam('method', settings.data)

    if (method === 'updateCarrier') {
        //Handles phone number on payment method load on supercheckout
        handlePhoneNumber($('.dpd-phone-block'));
        $.ajax(dpdHookAjaxUrl, {
            type: 'POST',
            data: {
                'ajax': 1,
                'action': 'validateOrderCustom',
                'token': typeof prestashop !== 'undefined' ? prestashop.static_token : '',
                'super_checkout_controller': currentController
            },
            success: function (response) {

                response = JSON.parse(response);
                var $parent = $('.supercheckout-dpd-phone-error');
                if (!response.status) {
                    DPDdisplayMessageOnSuperCheckout($parent, response.template);
                    $("html, body").animate({
                        scrollTop: 0
                    }, "fast");
                } else {
                    DPDdisplayMessageOnSuperCheckout($parent, '');
                }
            },
            error: function (response) {
                console.error('Error validating order')
            }
        });
    }

});

function saveSelectedPhoneNumberSuperCheckout(phoneNumber, phoneArea) {
    $.ajax(dpdHookAjaxUrl, {
        type: 'POST',
        data: {
            'ajax': 1,
            'phone_number': phoneNumber,
            'phone_area': phoneArea,
            'action': 'saveSelectedPhoneNumber',
            'token': typeof prestashop !== 'undefined' ? prestashop.static_token : ''
        },
        success: function (response) {
            response = JSON.parse(response);
            var $parent = $('.supercheckout-dpd-phone-error');
            if (!response.status) {
                DPDdisplayMessageOnSuperCheckout($parent, response.template);
                $("html, body").animate({
                    scrollTop: 0
                }, "fast");
            } else {
                DPDdisplayMessageOnSuperCheckout($parent, '');
            }
        },
        error: function (response) {
            console.error('Error while saving DPD phone number')
        }
    });
}

function DPDdisplayMessageOnSuperCheckout(parent, template) {
    parent.html(template);
}
