var isPudoPointSelected = false;

$(document).ready(function (){

    $(document).on('change', '.dpd-phone-block', function(){
        handlePhoneNumber($(this));
    });

    $(document).on('click','.payment_module a', function (e){
        e.preventDefault();

        if ($('.dpd-phone-block') !== undefined) {
            if(!handlePhoneNumber($('.dpd-phone-block'))) {
                return;
            }
        }
        if (!isPudoValid()) {
            showError(order_opc_errors['pickup_point_error']);
            return;
        }
        hideError();
        location.href = $(this).attr('href');
    });
});

function handlePhoneNumber(selector)
{
    if (!$('.dpd-phone-block')) {
        return true;
    }
    var phone = selector.find('input[name="dpd-phone"]').val();
    var phoneArea = selector.find('select[name="dpd-phone-area"] option:selected').val();

    if (!validatePhoneNumber(phone, selector)) {
        return false;
    }

    hideError();
    selector.find('input[name="dpd-phone"]').css('border-color', 'initial');

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
            'token': static_token
        },
        success: function (response) {
            if (!response.status) {
                DPDdisplayMessage($('.dpd-pudo-container'), response.template);
            }
        },
        error: function (response) {
            var responseText = JSON.parse(response.responseText);

            if (responseText) {
                DPDdisplayMessage($('.dpd-pudo-container'), responseText.template);
            }
        }
    });
}

function slideToError() {
    $('html, body').animate({
        scrollTop: ($('#order-opc-errors').offset().top - 300)
    }, 2000);
}

function validatePhoneNumber(phone, selector) {
    if (!isPhoneEmpty(phone)) {
        $('.dpd-checkout-phone-container .error-message').removeClass('hidden');
        selector.find('input[name="dpd-phone"]').css('border-color', 'red');
        showError(order_opc_errors['empty_phone_error']);

        return false;
    }
    if (!isPhoneValid(phone)) {
        $('.dpd-checkout-phone-container .error-message').removeClass('hidden');
        selector.find('input[name="dpd-phone"]').css('border-color', 'red');
        showError(order_opc_errors['invalid_phone_error']);

        return false;
    }

    return true;
}

function isPhoneEmpty(phone) {
    if (!phone) {
        return false;
    }

    return true;
}

function isPhoneValid(phone) {

    if (!$.isNumeric(phone)) {
        return false;
    }

    return true;
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
function showError(errorMessage) {
    $('#order-opc-errors').removeClass('hidden');
    $('#order-opc-errors').find('p').text(errorMessage);
    slideToError();
}
function hideError() {
    $('#order-opc-errors').addClass('hidden');
    $('#order-opc-errors').find('p').text('');
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
