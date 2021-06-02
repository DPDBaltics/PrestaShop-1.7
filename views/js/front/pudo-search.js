$(document).ready(function () {
    if ($('input[name="saved_pudo_id"]').val() === undefined) {
        updateStreet();
    } else {
        isPudoPointSelected = true;
    }
    $(document).on('change', 'select[name="dpd-city"]', function () {
        var city = $('select[name="dpd-city"]').val();
        updateStreetSelect(city);
    });

    $(document).on('change', 'select[name="dpd-street"]', function () {
        var city = $('select[name="dpd-city"]').val();
        var street = $('select[name="dpd-street"]').val();
        saveSelectedStreet(city, street);
    });

    $(document).on('keyup', 'input[name="dpd-street"]', function () {
        var city = $('select[name="dpd-city"]').val();
        var street = $('input[name="dpd-street"]').val();
        updateParcelBlock(city, street);
    });
});

function updateStreet() {
    var city = $('select[name="dpd-city"]').val();
    if (city) {
        updateStreetSelect(city);
    }
}

function updateStreetSelect(city) {
    $.ajax(dpdHookAjaxUrl, {
        type: 'POST',
        data: {
            'ajax': 1,
            'city': city,
            'action': 'updateStreetSelect',
            'token': typeof prestashop !== 'undefined' ? prestashop.static_token : ''
        },
        success: function (response) {
            response = JSON.parse(response);
            if (!response.status) {
                var $parent = $('.dpd-pudo-container');
                DPDdisplayMessage($parent, response.template);
            }
            if (response.status) {
                var $streetSelectDiv = $('.js-pudo-search-street');
                $streetSelectDiv.empty().append(response.template);
                $('select.chosen-select').chosen({inherit_select_classes: true});
                var street = $('select[name="dpd-street"]').val();
                saveSelectedStreet(city, street);
                isPudoPointSelected = true;
            }
        },
        error: function (response) {
            var responseText = JSON.parse(response.responseText);

            if (responseText) {
                DPDdisplayMessage($container, responseText.template);
            }
        }
    });
}

function saveSelectedStreet(city, street) {
    $.ajax(dpdHookAjaxUrl, {
        type: 'POST',
        data: {
            'ajax': 1,
            'city': city,
            'street': street,
            'action': 'saveSelectedStreet',
            'token': typeof prestashop !== 'undefined' ? prestashop.static_token : ''
        },
        success: function (response) {
            response = JSON.parse(response);
            var $parent = $('.dpd-pudo-container');

            if (!response.status) {
                DPDdisplayMessage($parent, response.template);
            }
            if (response.status) {
                DPDremoveMessage($parent);
                var coordinates = response.coordinates;
                var $idReference = $parent.data('id');
                $('.points-container').empty().append(response.template);

                initMap(coordinates, true, response.selectedPudoId, false, $idReference);
                isPudoPointSelected = true;
            }
        },
        error: function (response) {
            var responseText = JSON.parse(response.responseText);

            if (responseText) {
                DPDdisplayMessage($container, responseText.template);
            }
        }
    });
}

function updateParcelBlock(city, street) {
    $.ajax(dpdHookAjaxUrl, {
        type: 'POST',
        data: {
            'ajax': 1,
            'city': city,
            'street': street,
            'action': 'updateParcelBlock',
            'token': typeof prestashop !== 'undefined' ? prestashop.static_token : ''
        },
        success: function (response) {
            response = JSON.parse(response);
            var $parent =  $('.dpd-pudo-container');
            if (!response.status) {
                DPDdisplayMessage($parent, response.template);
            }
            if (response.status) {
                DPDchangePickupPoints($parent, response.template)
            }
        },
        error: function (response) {
            var responseText = JSON.parse(response.responseText);

            if (responseText) {
                DPDdisplayMessage($container, responseText.template);
            }
        }
    });
}

function DPDdisplayMessage(parent, template) {
    var $messageContainer = parent.find('.dpd-message-container');
    $messageContainer.replaceWith(template);
    parent.find('[id^="dpd-pudo-map"] div').removeClass('dpd-hidden');
}

function DPDremoveMessage(parent) {
    var $messageContainer = parent.find('.dpd-message-container');
    $messageContainer.html('');
}
