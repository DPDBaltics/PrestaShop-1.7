

$( document ).ajaxComplete(function( event, request, settings ) {

    if (currentController !== 'supercheckout') {
        return;
    }

    if (!settings.data) {
        return;
    }

    var method = DPDgetUrlParam('method', settings.data)

    if (method === 'updateCheckoutBehaviour') {
        //Handles phone number on payment method load on supercheckout
        handlePhoneNumber($('.dpd-phone-block'));
        $.ajax(dpdHookAjaxUrl, {
            type: 'POST',
            data: {
                'ajax': 1,
                'action': 'validateOrderCustom',
                'token': typeof prestashop !== 'undefined' ? prestashop.static_token : ''
            },
            success: function (response) {
                response = JSON.parse(response);
                var $parent = $('#supercheckout-empty-page-content');
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

function DPDdisplayMessageOnSuperCheckout(parent, template) {
    parent.html('<div class="dpd-error">' + template + '</div>');
}
