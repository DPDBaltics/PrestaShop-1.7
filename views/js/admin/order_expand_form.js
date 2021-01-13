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
    toggleDpdForm();
});

function toggleDpdForm()
{
    $(document).on('click', '.dpd-extra-expand', function () {
        $('.extra-shipment-container').toggle('slow');
        if ($(this).hasClass('form-hidden')) {
            $(this).removeClass('form-hidden').addClass('form-displayed');
            $(this).text(collapseText);
        } else if ($(this).hasClass('form-displayed')) {
            $(this).removeClass('form-displayed').addClass('form-hidden');
            $(this).text(expandText);
        }
    });
}
