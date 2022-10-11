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
