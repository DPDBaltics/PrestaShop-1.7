<input name="label_position" class="label-position-input hidden d-none" value="{$selectedOptionId}">
<div class="btn-group">
    {foreach $dpdSelectOptions as $option}
        {if $option.value == $selectedOptionId}
            <a class="btn dropdown-toggle js-printout-position-select printout-position-select fixed-width-xxl"
               data-selected-id="{$option.value}"
               data-toggle="dropdown"
               href="#">
                <img src="{$option.image}">
                {$option.name}
                <span class="caret"></span>
            </a>
        {/if}
    {/foreach}
    <ul class="dropdown-menu">
        {foreach $dpdSelectOptions as $option}
            <li><a data-select-id="{$option.value}" href="javascript:void(0);">
                    <img src="{$option.image}"/> {$option.name}</a>
            </li>
        {/foreach}
    </ul>
</div>
