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
