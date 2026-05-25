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
