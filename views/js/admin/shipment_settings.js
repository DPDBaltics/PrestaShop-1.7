$(document).ready(function() {
    toggleGoogleMapsKey($('input[name="DPD_PICKUP_MAP"]').val());
    $(document).on('click', 'input[name="DPD_PICKUP_MAP"]', function () {
        toggleGoogleMapsKey($(this).val());
    });

    function toggleGoogleMapsKey(showMap) {
        $('input[name="DPD_GOOGLE_API_KEY"]').closest('div.form-group').toggleClass('hidden', showMap)
    }
});