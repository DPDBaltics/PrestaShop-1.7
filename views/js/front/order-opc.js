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
var isPudoPointSelected = false;
$(document).ready(function (){

    $(document).on('change', '.dpd-phone-block', function() {
        handlePhoneNumber($(this));
    });

    $('body').on('click', '#confirm_order', function(e) {
        e.preventDefault();

        if ($('.dpd-phone-block') !== undefined) {
            if(!handlePhoneNumber($('.dpd-phone-block'))) {
                return;
            }
        }

    });

    $(document).on('click','.payment_module a', function (e){
        e.preventDefault();

        if ($('.dpd-phone-block') !== undefined) {
            if(!handlePhoneNumber($('.dpd-phone-block'))) {
                return;
            }
        }

    });
});

function handlePhoneNumber(selector)
{
    if (!$('.dpd-phone-block')) {
        return true;
    }
    var phone = selector.find('input[name="dpd-phone"]').val();
    var phoneArea = selector.find('select[name="dpd-phone-area"] option:selected').val();

    if (currentController === 'supercheckout') {
        saveSelectedPhoneNumberSuperCheckout(phone, phoneArea)
    } else {
        saveSelectedPhoneNumber(phone, phoneArea)
    }

    return true;
}

function saveSelectedPhoneNumber(phoneNumber, phoneArea) {
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
            var $parent = $('.dpd-checkout-phone-container');
            if (!response.status) {
                DPDdisplayMessageOpc($parent, response.template);
            } else {
                DPDdisplayMessageOpc($parent, '');
            }
        },
        error: function (response) {
            console.error('Error while saving DPD phone number')
        }
    });
}

function DPDdisplayMessageOpc(parent, template) {
    var $messageContainer = parent.find('.dpd-message-container');
    $messageContainer.html(template);
}

// Module "onepagecheckoutps" compatibility
$(document).on('opc-load-review:completed', function() {
    $('.delivery-option.selected .carrier-extra-content').show();
    handlePhoneNumber($('.dpd-phone-block'));
});


