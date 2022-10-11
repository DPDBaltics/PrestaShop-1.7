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

$(document).ready(function (){
    var tabs = $('.page-head-tabs > a');
    var visible = visibleTabs;
    var seen = {};
    $( tabs ).each(function() {
        //Extracts distinct value from module tab names as ps version < 174 cannot handle lang
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

        //Extracs param value from href link
        var extractedParamValue = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( extractedParamValue );
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
