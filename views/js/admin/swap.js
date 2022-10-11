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

$(document).ready(function(){
    bindSwapButton('add', 'available', 'selected');
    bindSwapButton('remove', 'selected', 'available');

    $('button:submit').click(bindSwapSave);

    function bindSwapSave()
    {
        $('.selectedSwap').each(function(){
            var $selectedSwapOptions = $(this).find('option');

            if ($selectedSwapOptions.length !== 0) {
                $selectedSwapOptions.attr('selected', 'selected');
            } else {
                $(this).closest('.swap-container').find('.availableSwap').find('option').attr('selected', 'selected');
            }
        });
    }

    function bindSwapButton(prefix_button, prefix_select_remove, prefix_select_add)
    {
        $(document).on('click', '.'+prefix_button+'SwapDPD', function(e) {
            e.preventDefault();
            var $removeSwap = $(this).closest('.swap-container').find('.' + prefix_select_remove + 'Swap'),
                $selectSwap = $(this).closest('.swap-container').find('.' + prefix_select_add + 'Swap');

            $removeSwap.find('option:selected').each(function() {
                $selectSwap.append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
                $(this).remove();
            });
            $('.selectedSwap option').prop('selected', true);
        });
    }
});
