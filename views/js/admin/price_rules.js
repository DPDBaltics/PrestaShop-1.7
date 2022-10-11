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
    changeCheckedMarkName();
    checkCheckboxes();

    function changeCheckedMarkName() {
        var $inputs = $('.check-box-all-message').next('input[name^="checkme"]');
        $inputs.each(function () {
            var $currentDom = $(this).closest('.panel');
            if ($(this).is(':checked')) {
                changeCheckBoxName($currentDom, uncheckAllMessage);
            }
        });
    }
});

function checkCheckboxes()
{
    $(document).on('click', '.checkme', function () {
        var currentDom = $(this).closest('.panel');
        var checkboxes = currentDom.find('.checkbox-input');
        if (!$(this).is(':checked')) {
            currentDom.find('.help-block').addClass('hidden d-none');
            uncheckAllCheckboxes(checkboxes);
            changeCheckBoxName(currentDom, checkAllMessage);
        } else {
            currentDom.find('.help-block').removeClass('hidden d-none');
            checkAllCheckboxes(checkboxes);
            changeCheckBoxName(currentDom,uncheckAllMessage);
        }
    });

    $(document).on('change', '.checkbox-input', function(){
        var currentDom = $(this).closest('.panel');
        var uncheckedCount = parseInt(currentDom.find('.checkbox-input:not(:checked)').length);
        if (uncheckedCount > 0) {
            currentDom.find('.checkme').attr('checked', false);
            currentDom.find('.help-block').addClass('hidden d-none');
        }
    });
}

function uncheckAllCheckboxes(checkboxes)
{
    checkboxes.attr('checked', false);
}

function checkAllCheckboxes(checkboxes) {
    checkboxes.attr('checked', 'checked');
}

function changeCheckBoxName(currentEl, textToReplace)
{
    currentEl.find('.check-box-all-message').text(textToReplace);
}