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
    var dpdCarriers = $('.custom-radio.float-xs-left > input');
    var dpdCarriersTextBox;

    if ($('.carrier').length) {
        dpdCarriersTextBox = $('.carrier');
    } else {
        //NOTE: seems like in 2019 Nov 22 carrier class was added, prior to that there were just rows in the classic theme.
        dpdCarriersTextBox = $('.delivery-option').find('.col-sm-5.col-xs-12').find('.row');
    }

    dpdCarriers.each(function (index, input) {
        let carrierId = parseInt($(input).attr('value'));
        let isDpdCarrier = dpd_carrier_ids.includes(carrierId)

        if(isDpdCarrier) {
            var sustainableBox = document.createElement("div");
            $(sustainableBox).attr("id", "sustainable-box");

            var sustainableImg = document.createElement("img");
            $(sustainableImg).attr("src", lapinas_img);

            var sustainableText = document.createElement("span");
            sustainableText.innerHTML = lapinas_text;

            sustainableBox.append(sustainableImg);
            sustainableBox.append(sustainableText)

            dpdCarriersTextBox[index].append(sustainableBox)
        }
    })
});
