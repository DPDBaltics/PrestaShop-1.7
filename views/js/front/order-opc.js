var isPudoPointSelected = false;

$(document).ready(function (){

    $(document).on('change', '.dpd-phone-block', function() {
        handlePhoneNumber($(this));
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

    saveSelectedPhoneNumber(phone, phoneArea)

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
                DPDdisplayMessage($parent, response.template);
            }
        },
        error: function (response) {

        }
    });
}

function isPudoValid() {

    var pudoContainer = $('.dpd-pudo-container');
    if (!pudoContainer.is(':visible')) {
        return true;
    }
    var pudoSelect = pudoContainer.find('.dpd-pudo-select');
    //If map and map parcel is selected
    if (( pudoSelect && pudoSelect.hasClass('button-medium'))
        && isPudoPointSelected ) {
        return true;
    }

    return false;
}

function uncheckSelectedPudoOnReload() {
    $('.dpd-pudo-container').find('.dpd-pudo-select').each(function () {
            $(this)
                .removeClass('button-medium')
                .addClass('button-small')
                .attr('disabled', false);
            $(this).find('span').text($(this).data('select'));
        });
}

function DPDdisplayMessage(parent, template) {
    var $messageContainer = parent.find('.dpd-message-container');
    console.log(template);
    $messageContainer.replaceWith(template);
}