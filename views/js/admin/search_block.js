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
    var $searchBox = $('.searchable-multiselect');
    
    $searchBox.removeClass('hidden d-none');
    $('.service-configuration-container').find('> div').removeAttr('class');
    dpdInitSearchBoxPlugin($searchBox);
});

function dpdInitSearchBoxPlugin($searchBox) {
    $searchBox.chosen({
        placeholder_text_multiple: chosenPlaceholder
    }).change(selectChangeEvent);

    function selectChangeEvent(event, obj) {
        if (0 == obj.selected) {
            // if "All" is selected - remove other selections
            $(event.currentTarget).val(0).trigger('chosen:updated');
        } else {
            // if other than "All" is selected - remove "All" selection
            $(event.currentTarget).find('option[value=0]').prop('selected', false).trigger('chosen:updated');
        }
    }
}
