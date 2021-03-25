
$(document).ready(function (){
    var tabs = $('.page-head-tabs > a');
    var visible = visibleTabs;
    var seen = {};
    $( tabs ).each(function() {
        var txt = $(this).text();
        if (seen[txt]) {
            $(this).remove();
        } else {
            seen[txt] = true;
        }

        var href = $( this ).attr('href');
        var controller = getUrlParam('controller', href);
        if (!inArray(controller, visible)) {
           $(this).remove();
        }
    });

    function getUrlParam( name, url ) {

        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( url );
        return results == null ? null : results[1];
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle){
                return true;
            }
        }
        return false;
    }
});
