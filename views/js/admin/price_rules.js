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