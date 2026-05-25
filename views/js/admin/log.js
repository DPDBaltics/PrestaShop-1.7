/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */
$(document).ready(function () {
    $(document).on('click', '.log-modal-overlay, .js-log-modal-close', function (event) {
        $('.modal.open').removeClass('open');
        event.preventDefault();
    });

    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            $('.modal.open').removeClass('open');
        }
    });

    $('.js-log-button').on('click', function (event) {
        var logId = $(this).data('log-id');
        var informationType = $(this).data('information-type');

        $('#' + $(this).data('target')).addClass('open');

        var $contentData = $('#log-modal-' + logId + '-' + informationType + ' .log-modal-content-data');

        if (!$contentData.hasClass('hidden')) {
            return;
        }

        var $spinner = $('#log-modal-' + logId + '-' + informationType + ' .log-modal-content-spinner');
        $spinner.removeClass('hidden');

        $.ajax({
            type: 'POST',
            url: dpdbaltics.logsUrl,
            data: {
                ajax: true,
                action: 'getLog',
                log_id: logId
            }
        })
            .then(function (response) { return jQuery.parseJSON(response); })
            .then(function (data) {
                $spinner.addClass('hidden');

                if (data.error) {
                    $contentData.removeClass('hidden').text(data.message || '');
                    return;
                }

                $('#log-modal-' + logId + '-request .log-modal-content-data').removeClass('hidden').text(formatLogPayload(data.log.request));
                $('#log-modal-' + logId + '-response .log-modal-content-data').removeClass('hidden').text(formatLogPayload(data.log.response));
            });
    });
});

function formatLogPayload(payload) {
    if (payload === null || typeof payload === 'undefined' || payload === '') {
        return '';
    }

    try {
        return JSON.stringify(JSON.parse(payload), null, 2);
    } catch (e) {
        // not JSON
    }

    var queryIndex = payload.indexOf('?');
    if (queryIndex !== -1 && payload.indexOf('&') !== -1) {
        var base = payload.substring(0, queryIndex);
        var query = payload.substring(queryIndex + 1);
        var parts = query.split('&').map(function (part) {
            var eq = part.indexOf('=');
            if (eq === -1) {
                return decodeURIComponentSafe(part);
            }
            return decodeURIComponentSafe(part.substring(0, eq)) + ' = ' + decodeURIComponentSafe(part.substring(eq + 1));
        });
        return base + '\n\n' + parts.join('\n');
    }

    return payload;
}

function decodeURIComponentSafe(value) {
    try {
        return decodeURIComponent(value.replace(/\+/g, ' '));
    } catch (e) {
        return value;
    }
}
