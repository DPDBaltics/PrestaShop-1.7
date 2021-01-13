$(document).ready(function() {
    // $("#head_tabs .nav a[href*='AdminDPDGroupShipmentsReturnList&']").append(' (' + totalShipmentReturns +')');

    var resized = false;
    $(window).on('load', function () {
        changeContentMargin();
    });

    $(window).on('resize', function () {
        changeContentMargin();
    });

    function changeContentMargin() {
        var headTabsStandardHeight = 56;
        if ($('#head_tabs').height() > headTabsStandardHeight && !resized) {
            $('#content').animate({
                'marginTop' : "+=" + headTabsStandardHeight + "px" //moves down
            });
            resized = true;
        }
        if ($('#head_tabs').height() <= headTabsStandardHeight && resized) {
            $('#content').animate({
                'marginTop' : "-=" + headTabsStandardHeight + "px" //moves up
            });
            resized = false;
        }
    }
});
