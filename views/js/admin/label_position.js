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