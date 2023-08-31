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

/**
 * global zoom level of google maps.
 * @type {number}
 */
var zoomLevel = 12;
/**
 * global markers holder. used to display and to remove markers on the map
 * @type {Array}
 */
var googleMarkers = [];
var dpdMap = {};
var infoWindow;
var isPudoPointSelected = false;

$(document).ready(function () {


    if (typeof google !== 'undefined') {
        infoWindow = new google.maps.InfoWindow();
    }

    $(".dpd-input-wrapper .dpd-input-placeholder").on("click", function () {
        $(this).closest(".dpd-input-wrapper").find("input").focus();
    });

    $(document).on('change', 'input[name^="delivery_option"]', function () {
        searchPudoServicesEvent($('select[name="dpd-city"]'));
        var savedPudoId = $('input[name="saved_pudo_id"]').val();

        if (savedPudoId !== undefined) {
            initMap(false, true, savedPudoId, true);
        }
    });

    $(document).on('change', 'input[name="dpd-phone"]', function () {
        var value = $(this).val();
        var name = $(this).attr('name');

        phoneInput = $('input[name="dpd-phone"]');
        if (value) {
            $('[name=' + name + ']').closest(".dpd-input-wrapper").addClass("hasValue");
        } else {
            $('[name=' + name + ']').closest(".dpd-input-wrapper").removeClass("hasValue");
        }
        phoneInput.val($(this).val());
    });

    $(document).on('change', 'select[name="dpd-city"]', function () {
        phoneInput = $('select[name="dpd-city"]');
        phoneInput.val($(this).val());
        $('select').trigger("chosen:updated");
    });

    $(document).on('change', 'select[name="dpd-street"]', function () {
        phoneInput = $('select[name="dpd-street"]');
        phoneInput.val($(this).val());
        $('select').trigger("chosen:updated");
    });

    $(document).on('keyup', 'input[name="dpd-street"]', function () {
        phoneInput = $('input[name="dpd-street"]');
        phoneInput.val($(this).val());
    });

    $(document).on('change', 'select[name="dpd-delivery-time"]', function () {
        phoneInput = $('select[name="dpd-delivery-time"]');
        phoneInput.val($(this).val());
    });

    $(".dpd-input-wrapper input").on("keyup", function () {
        var value = $.trim($(this).val());
        var name = $(this).attr('name');
        if (value) {
            $('[name=' + name + ']').closest(".dpd-input-wrapper").addClass("hasValue");
        } else {
            $('[name=' + name + ']').closest(".dpd-input-wrapper").removeClass("hasValue");
        }
    });

    toggleInputWrapper($('.dpd-input-wrapper input'));
    $(".dpd-input-wrapper input").on("change", function () {
        toggleInputWrapper($(this));
    });

    if (typeof prestashop !== 'undefined') {
        prestashop.on('updatedDeliveryForm', function (params) {
            if (typeof params.deliveryOption === 'undefined') {
                return;
            }

            //Js to open extra content failed from theme, doing it manually.
            if (params.deliveryOption.length > 0 && !$(params.deliveryOption).next('.carrier-extra-content').is(':visible')) {
                $('.carrier-extra-content').hide();
                $(params.deliveryOption).next('.carrier-extra-content').slideDown();
            }

            var deliveryOption = params.deliveryOption;
            var $idCarrier = parseInt(deliveryOption.find('input[name^="delivery_option"]').val());
            var $pudoContainer = $('[data-pudo-id-carrier="' + $idCarrier + '"]');
            var $idReference = $pudoContainer.attr('data-id');

            var carriers = JSON.parse(pudoCarriers);
            if (typeof carriers === 'undefined') {
                return;
            }

            if (!parseInt(carriers[$idReference])) {
                return;
            }

            var $pickUpMap = $('.pickup-map-' + $idReference);
            var $pickUpMapParent = $pickUpMap.closest('.carrier-extra-content');

            if (!dpdMap.hasOwnProperty($idReference)) {
                return;
            }

            if ($pickUpMapParent.is(':visible')) {
                google.maps.event.trigger(dpdMap[$idReference], 'resize');
                return;
            }

            $pickUpMapParent.slideDown();
            google.maps.event.trigger(dpdMap[$idReference], 'resize');
        });
    }

    var savedPudoId = $('input[name="saved_pudo_id"]').val();

    initMap(false, true, savedPudoId, true);

    reselectDataPopover();

    $(document).on('click', '#checkout-delivery-step', resizeMapEvent);
    $('.dpd-services-block  .list-inline').on('scroll', dpdHidePopOversEvent);

    if ('AdminOrders' !== currentController) {
        $(document).on('click', '.dpd-pudo-select', selectPickupPointEvent);
    }

    // $(document).on('change', 'select[name="dpd-city"]',function (){
    //     searchPudoServicesEvent($(this));
    // });

    function resizeMapEvent(e) {
        for (var idReference in dpdMap) {
            if (!dpdMap.hasOwnProperty(idReference)) {
                continue;
            }
            google.maps.event.trigger(dpdMap[idReference], 'resize');
        }
    }

    /**
     * searches for pudo services
     */
    function searchPudoServicesEvent($selectedCity) {

        var $originalText = $selectedCity.attr('data-original-text');
        var $loadingText = $selectedCity.attr('data-loading-text');

        $selectedCity.prop('disabled', true);
        $selectedCity.find('span').text($loadingText);

        var $parent = $selectedCity.closest('.dpd-pudo-container');
        var $shipmentParent = $selectedCity.closest('.js-shipment-block');
        var $idCountry = parseInt($parent.find('select[name="dpd-country"]').find(':selected').val());
        var $cityName = $parent.find('select[name="dpd-city"]').val();

        var $idCarrier = $parent.attr('data-id');
        var $idService = $shipmentParent.find('.js-service-select option:selected').attr('data-id_unique_select');

        var data = {
            'id_country': $idCountry,
            'city_name': $cityName,
            'ajax': 1,
            'currentController': currentController,
            'id_service': $idService,
            'action': 'searchPudoServices',
            'id_carrier': $idCarrier
        };

        if ('AdminOrders' !== currentController) {
            DPDinitAnimationOnMap($parent);
            data.token = typeof prestashop !== 'undefined' ? prestashop.static_token : '';
        }

        if ('AdminOrders' == currentController) {
            $parent.find('.dpd-message-container').addClass('dpd-hidden');
        }

        $.ajax(dpdHookAjaxUrl, {
            method: 'POST',
            data: data,
            success: function (response) {
                $selectedCity.prop('disabled', false);
                $selectedCity.find('span').text($originalText);

                enableSearchButtons($parent);
                response = JSON.parse(response);
                var $idReference = $parent.data('id');

                if (!response.status) {
                    DPDdisplayMessage($parent, response.template);
                    if ('AdminOrders' !== currentController) {
                        google.maps.event.trigger(dpdMap[$idReference], 'resize');
                    }
                }
                if (response.status) {
                    DPDchangePickupPoints($parent, response.template);
                    DPDremoveMessage($parent);
                    if ('AdminOrders' == currentController &&
                        typeof ignoreAdminController === 'undefined'
                    ) {
                        DPDsetCountriesAndCitiesObject($selectedCity);
                        return;
                    }

                    if ('AdminOrders' == currentController) {
                        return;
                    }

                    var coordinates = JSON.parse(response.coordinates);
                    initMap(coordinates, true, false, false, $idReference);
                }
                DPDremoveAnimationFromMap($parent);
                $("[data-toggle=popover]").popover();
            },
            error: function (response) {
                var responseText = JSON.parse(response.responseText);

                if (responseText) {
                    DPDdisplayMessage($parent, responseText.template);
                }

                enableSearchButtons($parent);
                $selectedCity.prop('disabled', false);
                $selectedCity.find('span').text($originalText);
            }
        });
    }

    function enableSearchButtons($parent) {
        $parent.find('.dpd-pudo-select').attr('disabled', false);
    }

    /**
     * selects the pickup point from the given list or directly from the map
     */
    function selectPickupPointEvent() {

        var $this = $(this);
        var $pudoId = $this.data('id');
        var $container = $this.closest('.dpd-pudo-container');
        var $submitInput = $container.find('input[name="dpd-pudo-id"]');
        var $idReference = $container.data('id');

        $submitInput.attr('value', $pudoId);

        $.ajax(dpdHookAjaxUrl, {
            type: 'POST',
            data: {
                'ajax': 1,
                'id_pudo': $pudoId,
                'action': 'savePudoPickupPoint',
                'token': typeof prestashop !== 'undefined' ? prestashop.static_token : ''
            },
            success: function (response) {
                response = JSON.parse(response);
                if (!response.status) {
                    var $parent = $('.dpd-pudo-container');
                    DPDdisplayMessage($parent, response.template);
                }
                if (response.status) {
                    initAfterPickupSelect($idReference, $this, $pudoId, $container);
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

    function initAfterPickupSelect($idReference, $this, $pudoId, $container) {
        $('button[name="processCarrier"]').attr('disabled', false);

        var $pudoButtons = $container.find('[data-id="' + $pudoId + '"]');
        //changes buttons design
        $pudoButtons.each(function () {
            var $button = $(this);
            markButton($button);
        });

        //changes class of the panel
        $container.find('.panel')
            .removeClass('panel-default')
            .addClass('panel-success');

        //changes name of the panel
        var $listGroupItem = $this.parent().parent();
        var $pickupName = $listGroupItem.find('.list-group-item-heading').text();
        var $panelHeading = $container.find('.panel-heading p');
        var $panelHeadingText = $panelHeading.data('name');
        $panelHeading.text(pudoSelectSuccess);
        //todo: fix this part to change text in checkout when selecting parcel.
        // $panelHeading.text($panelHeadingText + $pickupName);

        //unchecks previously selected buttons
        $container.find('.dpd-pudo-select').each(function () {
            if ($(this).hasClass('button-medium') && $(this).data('id') !== $pudoId) {
                $(this)
                    .removeClass('button-medium')
                    .addClass('button-small')
                    .attr('disabled', false);
                $(this).find('span').text($(this).data('select'));
            }
        });
        isPudoPointSelected = true;
        if (typeof dpdMap[$idReference] !== 'undefined') {
            DPDinitMarkers(dpdMap[$idReference], infoWindow, false, false, $pudoId, true, false);
        }
    }

    /**
     * changes button appearance
     * @param {object} btn
     */
    function markButton(btn) {
        btn.find('span').text(btn.data('selected'));
        btn
            .removeClass('button-small')
            .addClass('button-medium')
            .attr('disabled', true);
    }

    function toggleInputWrapper($input) {
        var value = $input.val();
        var name = $input.attr('name');

        if (value) {
            $('[name=' + name + ']').closest(".dpd-input-wrapper").addClass("hasValue");
        } else {
            $('[name=' + name + ']').closest(".dpd-input-wrapper").removeClass("hasValue");
        }
    }
});

/**
 * main function for getting google map
 * @param coordinates
 * @param {boolean} loadMarkers
 * @param {string|boolean} pudoId
 * @returns {{map: google.maps.Map, infoWindow: google.maps.InfoWindow}}
 * @constructor
 */
function initMap(coordinates, loadMarkers, selectedPudoId, firstLoad, referenceId) {
    var carriers = JSON.parse(pudoCarriers);

    if (typeof carriers === 'undefined') {
        return;
    }

    for (var idReference in carriers) {
        if (typeof referenceId !== 'undefined' && referenceId != idReference) {
            continue;
        }
        if (!carriers.hasOwnProperty(idReference)) {
            continue;
        }

        var element = document.getElementById('dpd-pudo-map-' + idReference);

        if (null === element) {
            continue;
        }

        dpdMap[idReference] = new google.maps.Map(
            element,
            {
                zoom: zoomLevel
            }
        );

        if (!coordinates) {
            coordinates = DPDgetDefaultCoordinates($('.pickup-map-' + idReference));
        }

        dpdMap[idReference].setCenter(new google.maps.LatLng(parseFloat(coordinates.lat), parseFloat(coordinates.lng)));

        google.maps.event.trigger(dpdMap[idReference], 'resize');

        if (!navigator.geolocation) {
            $('.dpd-my-location').remove();
        }

        DPDgetCurrentLocation();
        googleMarkers = [];
        DPDinitMarkers(dpdMap[idReference], infoWindow, coordinates, false, selectedPudoId, true, firstLoad);
    }
}

/**
 *  inits markers on the map
 * @param map
 * @param infoWindow
 * @param {*|boolean} coordinates - default coordinates calculates by google Api service
 * @param {boolean} enableBounding - sets the zoom level according to points
 * @param {string|boolean} selectedPudoId
 * @param {boolean} removeMarkers
 * @param firstLoad
 */
function DPDinitMarkers(map, infoWindow, coordinates, enableBounding, selectedPudoId, removeMarkers, firstLoad) {
    var $parent = map.getDiv().closest('.panel-body');
    setMapOnAll(null);
    var $dataPopovers = $('[data-toggle="popover"]');
    if ($dataPopovers.popover() !== 'undefined' && 'AdminOrders' !== currentController) {
        $dataPopovers.popover();
    }

    google.maps.event.addListener(infoWindow, "closeclick", function () {
        $('#checkout-delivery-step').off('click');
    });

    var $pudoServices = $($parent).find('.dpd-services-block .list-group-item');
    var bounds = new google.maps.LatLngBounds();

    $pudoServices.each(function () {
        var extraInfoHtml = getListGroupItemExtraInfoHtml($(this), selectedPudoId);
        var latitude = parseFloat($(this).find('input[name="pudo-lat"]').val());
        var longitude = parseFloat($(this).find('input[name="pudo-lng"]').val());
        var type = $(this).find('input[name="pudo-type"]').val();
        if (latitude || longitude) {
            var latlng = new google.maps.LatLng(
                latitude,
                longitude
            );
            var pudoId = $(this).data('listid');
            DPDcreateMarker(map, infoWindow, latlng, extraInfoHtml, pudoId, selectedPudoId, type);
            if (enableBounding) {
                bounds.extend(latlng);
            }
        }
    });

    if (enableBounding) {
        map.fitBounds(bounds);
        if (coordinates && !firstLoad) {
            var latlng = new google.maps.LatLng(
                parseFloat(coordinates.lat),
                parseFloat(coordinates.lng)
            );
            map.setCenter(latlng);
        }
        map.setZoom(zoomLevel);
    }
}

function getListGroupItemExtraInfoHtml($listGroupItem, selectedPudoId) {
    var extraInfo = $listGroupItem.clone();
    extraInfo.find('.dpd-more-information').remove();
    extraInfo.find('.dpd-hidden').removeClass('dpd-hidden');
    var $button = extraInfo.find('.dpd-pudo-select');

    if (typeof selectedPudoId !== 'undefined' && selectedPudoId == $button.attr('data-id')) {
        $button.find('span').text($button.attr('data-selected'));
        $button.attr('disabled', true);
    } else {
        $button.find('span').text($button.attr('data-select'));
        $button.attr('disabled', false);
    }

    return extraInfo.html();
}

/**
 *  creates the marker and infoWindow for it
 * @param map
 * @param infoWindow
 * @param latlng
 * @param {html} content
 * @param {string} pudoId
 * @param {string|boolean} selectedPudoId
 * @param type
 */
function DPDcreateMarker(map, infoWindow, latlng, content, pudoId, selectedPudoId, type) {
    if (type === 'locker') {
        var icon = dpdLockerMarkerPath;
    }

    if (type === 'parcel_shop') {
        var icon = dpdPickupMarkerPath;
    }
    var marker = new google.maps.Marker({
        map: map,
        icon: icon,
        position: latlng,
        id: pudoId
    });

    google.maps.event.addListener(marker, 'click', function () {
        infoWindow.setContent(content);
        infoWindow.open(map, marker);
        map.setCenter(this.getPosition());
    });

    if (selectedPudoId == marker.id) {
        infoWindow.setContent(content);
        infoWindow.open(map, marker);
        map.setCenter(marker.getPosition());
    }

    for (var idReference in dpdMap) {
        if (!dpdMap.hasOwnProperty(idReference)) {
            continue;
        }

        if (dpdMap[idReference] === map) {
            googleMarkers.push(marker);
            dpdMap[idReference]['markers'] = googleMarkers;
        }
    }
}

function setMapOnAll(value) {
    for (var i = 0; i < googleMarkers.length; i++) {
        googleMarkers[i].setMap(value);
    }

    googleMarkers = [];
}

/**
 * gets current location
 * @constructor
 */
function DPDgetCurrentLocation() {
    $(document).on('click', '.dpd-my-location', function () {
        DPDsetCoordinatesByBrowser($(this));
    });
}

/**
 *  returns default coordinates if it exists
 * @returns {*}
 * @constructor
 */
function DPDgetDefaultCoordinates($parent) {
    var lat = parseFloat($parent.find('input[name="default-lat"]').val());
    var lng = parseFloat($parent.find('input[name="default-lng"]').val());
    if (!lat || !lng) {
        return false;
    }

    return {lat: lat, lng: lng};
}

/**
 * sets coordinates by the browser.
 * @param map
 * @constructor
 */
function DPDsetCoordinatesByBrowser($button) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                DPDprocessAjaxGetPickupPointsByCoordinates(pos, $button);
            }, function (e) {
                $('.dpd-my-location').remove();
            }, {enableHighAccuracy: true}
        );

    } else {
        $('.dpd-my-location').remove();
    }
}

/**
 *  gets pickup points by google maps coordinates1
 * @param {*} position - {lat, lng}
 * @constructor
 */
function DPDprocessAjaxGetPickupPointsByCoordinates(position, $button) {

    var $parent = $button.closest('.dpd-pudo-container');
    var $idReference = parseInt($parent.data('id'));

    if (typeof dpdMap[$idReference] === 'undefined') {
        return;
    }

    DPDinitAnimationOnMap($parent);

    $.ajax(dpdHookAjaxUrl, {
        method: 'POST',
        data: {
            'id_language': id_language,
            'id_shop': id_shop,
            'lat': position.lat,
            'lng': position.lng,
            'ajax': 1,
            'action': 'getPickupPointsByCoordinates',
            'token': typeof prestashop !== 'undefined' ? prestashop.static_token : ''
        },
        success: function (response) {
            response = JSON.parse(response);
            if (!response.status) {
                DPDdisplayMessage($parent, response.template);
            }

            if (response.status) {
                DPDchangePickupPoints($parent, response.template);
                DPDinitMarkers(
                    dpdMap[$idReference],
                    infoWindow,
                    position,
                    true,
                    false,
                    true
                );
            }
            DPDremoveAnimationFromMap($parent);
        },
        error: function (response) {
            var responseText = JSON.parse(response.responseText);

            if (responseText) {
                DPDdisplayMessage($parent, responseText.template);
            }
            DPDremoveAnimationFromMap($parent);
        }
    });
}

function DPDinitAnimationOnMap($parent) {
    $parent.find('[id^="dpd-pudo-map"] div').addClass('dpd-hidden');
    $parent.find('.dpd-message-container').addClass('dpd-hidden');
    $parent.find('[id^="dpd-pudo-map"]').css(
        'background',
        'transparent url(' + dpdAjaxLoaderPath + ') no-repeat center center'
    );
    $parent.find('.dpd-action-button').attr('disabled', true);
}

function DPDremoveAnimationFromMap($parent) {
    $parent.find('[id^="dpd-pudo-map"] div').removeClass('dpd-hidden');
    $parent.find('.dpd-message-container').removeClass('dpd-hidden');
    $parent.find('[id^="dpd-pudo-map"]').css(
        'background',
        ''
    );
    $parent.find('.dpd-action-button').attr('disabled', false);
}

function DPDchangePickupPoints($parent, template) {
    $parent.find('.dpd-services-block').replaceWith(template);
    $parent.find('.list-inline').on('scroll', dpdHidePopOversEvent);
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


function dpdHidePopOversEvent() {
    $('.dpd-more-information').each(function () {
        $(this).popover('hide');
    });
}

/**
 * expands working hour information
 */
function expandExtraInformationEvent() {
    var $parent = $(this).closest('.list-group-item');
    var $extraInfoBlock = $parent.find('.extra-info-working-hours');

    if ($(this).hasClass('expand')) {
        $(this)
            .removeClass('expand')
            .addClass('dpd-collapse')
            .text($(this).data('collapse'));
        $extraInfoBlock.removeClass('dpd-hidden');
    }
}

function collapseExtraInformationEvent() {
    var $parent = $(this).closest('.list-group-item');
    var $extraInfoBlock = $parent.find('.extra-info-working-hours');

    if ($(this).hasClass('dpd-collapse')) {
        $(this)
            .removeClass('dpd-collapse')
            .addClass('expand')
            .text($(this).data('expand'));
        $extraInfoBlock.addClass('dpd-hidden');
    }
}

function reselectDataPopover() {
    var $dataPopovers = $('[data-toggle="popover"]');
    if ($dataPopovers.popover() !== 'undefined' && 'AdminOrders' !== currentController) {
        $dataPopovers.popover();
    } else {
        $(document).on('click', '.dpd-more-information.expand', expandExtraInformationEvent);
        $(document).on('click', '.dpd-more-information.dpd-collapse', collapseExtraInformationEvent);
    }
}
