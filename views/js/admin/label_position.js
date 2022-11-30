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
    var $labelFormat = $('#DPD_DEFAULT_LABEL_FORMAT');
    if ($labelFormat.length === 0) {
        return;
    }
    $(document).on('change', $labelFormat, function () {
        hideLabelOptions($labelFormat);
    });
    hideLabelOptions($labelFormat);
    /**
     * Hide label option if A6 page is selected and set label position input to 1
     */
    function hideLabelOptions($labelFormat) {
        var printFormat = $labelFormat.val();
        if (printFormat.indexOf('A4') === -1) {
            $('.DPD_DEFAULT_LABEL_POSITION').css('visibility','hidden');
            return;
        }
        $('.DPD_DEFAULT_LABEL_POSITION').css('visibility','visible');

    }
});