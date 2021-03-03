$(document).ready(function () {
    $(document).on('click', 'button[name="search-pudo-services"]', searchPudoServicesEvent);
    $(document).on('click', '.dpd-pudo-select', updateSelectedPudo);

    /**
     * searches for pudo services
     */
    function searchPudoServicesEvent() {
        var $this = $(this);
        var $originalText = $this.attr('data-original-text');
        var $loadingText = $this.attr('data-loading-text');

        $this.prop('disabled', true);
        $this.find('span').text($loadingText);

        var $parent = $this.closest('.dpd-pudo-container');
        var $shipmentParent = $this.closest('.js-shipment-block');
        var $idCountry = parseInt($parent.find('select[name="dpd-country"]').find(':selected').val());
        var $cityName = $parent.find('select[name="dpd-city"]').val();
        var $zipCode = $parent.find('input[name="dpd-zip-code"]').val();
        var $street = $parent.find('input[name="dpd-street"]').val();

        var $idService = $shipmentParent.find('.js-service-select option:selected').attr('data-id_unique_select');
        $selectedProduct = $('select[name="product"]').val();
        $selectedCarrier = $('select[name="delivery_option"]').val();


        if (isPageOrderAdd()) {
            var id_cart = sharedIdCart;
        }

        var data = {
            'id_country': $idCountry,
            'city_name': $cityName,
            'zip_code': $zipCode,
            'street': $street,
            'id_cart': id_cart,
            'id_order': id_order,
            'ajax': 1,
            'id_service': $idService,
            'action': 'searchPudoServices',
            'id_product': $selectedProduct,
            'carrier_reference': $selectedCarrier
        };

        if ('AdminOrders' !== currentController) {
            DPDinitAnimationOnMap($parent);
            data.token = typeof prestashop !== 'undefined' ? prestashop.static_token : '';
        }

        if ('AdminOrders' == currentController) {
            $parent.find('.dpd-message-container').addClass('dpd-hidden');
        }

        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: data,
            success: function (response) {
                $this.prop('disabled', false);
                $this.find('span').text($originalText);

                enableSearchButtons($parent);
                response = JSON.parse(response);
                var $idReference = $parent.data('id');

                if (!response.status) {
                    DPDshowError(response.message);
                }
                if (response.status) {
                    DPDchangePickupPoints($parent, response.template);
                    DPDremoveMessage($parent, response.template);
                    $("[data-toggle=popover]").popover();

                    if ('AdminOrders' == currentController &&
                        typeof ignoreAdminController === 'undefined'
                    ) {
                        DPDsetCountriesAndCitiesObject($this);
                        return;
                    }

                    if ('AdminOrders' == currentController) {
                        return;
                    }

                    var coordinates = JSON.parse(response.coordinates);
                    initMap(coordinates, true, false, false, $idReference);
                }
                DPDremoveAnimationFromMap($parent);
            },
            error: function (response) {
                response = JSON.parse(response);

                // var responseText = JSON.parse(response.responseText);

                if (response.message) {
                    // DPDdisplayMessage($parent, responseText.template);
                    DPDshowError(response.message);
                }
                enableSearchButtons($parent);
                $this.prop('disabled', false);
                $this.find('span').text($originalText);
            }
        });

    }

    function updateSelectedPudo() {
        var $selectedButton = $(this);
        var pudoId = $(this).data('id');
        var data = {
            'pudo_id': pudoId,
            'action': 'updatePudoInfo',
        };
        $.ajax(dpdAjaxShipmentsUrl, {
            method: 'POST',
            data: data,
            success: function (response) {
                response = JSON.parse(response);
                if (!response.status) {
                    // DPDdisplayMessage($parent, response.template);
                    DPDshowError(response.message);
                }
                if (response.status) {
                    $('.pudo-info-container').replaceWith(response.template);
                    $('.pudo-info-container').removeClass('col-lg-12').addClass('col-lg-6');
                    $('b.js-parcel-name').text(response.parcel_name);
                    var $selectButtons = $('.dpd-pudo-container .dpd-pudo-select');
                    $selectButtons.text('select');
                    $selectButtons.attr('disabled', false);

                    $selectedButton.text('selected');
                    $selectedButton.attr('disabled', true);
                }
            }
        })
    }

    function DPDchangePickupPoints($parent, template) {
        $parent.find('.dpd-services-block').replaceWith(template);
        $parent.find('.list-inline').on('scroll', dpdHidePopOversEvent);
    }

    function enableSearchButtons($parent) {
        $parent.find('.dpd-pudo-select').attr('disabled', false);
    }

    function DPDremoveMessage(parent, template) {
        var $messageContainer = parent.find('.dpd-message-container');
        $messageContainer.html('');
    }

    function dpdHidePopOversEvent() {
        $('.dpd-more-information').each(function () {
            $(this).popover('hide');
        });
    }

    /**
     *  adds currently selected countries and cities objects
     * @constructor
     */
    function DPDsetCountriesAndCitiesObject($button) {
        //reseting values
        searchCountriesList = [];
        searchCityList = [];

        var $shipment = $button.closest('.js-shipment-block');
        var idDpdShipment = $shipment.attr('data-id-dpd-shipment');
        var countryList = $shipment.find('select[name="dpd-country"] option');
        var cityList = $shipment.find('select[name="dpd-city"] option');
        countryList.each(function () {
            if (parseInt($(this).val())) {
                searchCountriesList.push(
                    {
                        id_dpd_shipment: idDpdShipment,
                        country: {
                            id_country: parseInt($(this).val()),
                            name: $(this).text()
                        }
                    }
                );
            }
        });

        cityList.each(function () {
            if (parseInt($(this).val())) {
                searchCityList.push(
                    {
                        id_dpd_shipment: idDpdShipment,
                        city: {
                            cityId: parseInt($(this).val()),
                            cityName: $(this).text()
                        }
                    }
                );
            }
        });
    }
});

function isPageOrderAdd() {

    if ($('body').find('#order-creation-container').length !==0 || getUrlParameter('addorder')) {

        return true;
    }

    return false;
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};