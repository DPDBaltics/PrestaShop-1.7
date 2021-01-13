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

$(document).ready(function () {
    var $steps = getStepsNames();
    var $onBoardSection = $('.dpd-on-board-section');
    var $body = $('body');

    makeStepAction(onBoard.currentStep);

    $('#dpd-on-board').draggable();

    $onBoardSection.on('click', '.js-dpd-stop-on-board', stopOnBoard);
    $onBoardSection.on('click', '.js-dpd-pause-on-board', pauseOnBoard);
    $onBoardSection.on('click', '.js-dpd-hide-on-board', disableOnBoard);
    $onBoardSection.on('click', '.js-dpd-invisible-on-board', hideOnBoard);
    $onBoardSection.on('click', '.js-dpd-next-step', nextOnBoardStep);
    $onBoardSection.on('click', '.js-hide-markings', hideMarkings);
    $onBoardSection.on('click', '.js-dpd-auto-zones-import', autoZonesImport);

    $body.on('click', '#import-zones-close-button', showOnBoard);
    $body.on('click', '.js-close-zone-import', showOnBoard);

    $body.on('click', '.js-product-service .js-edit-product', function() {
        nextOnBoardStep(event, $steps.MANUAL_PRODUCTS_3_STEP);
    });

    $body.on('click', '.js-save-product', function() {
        if (onBoard.currentStep === $steps.MANUAL_PRODUCTS_8_STEP) {
            nextOnBoardStep(event, $steps.MANUAL_PRODUCTS_9_STEP);
        } else {
            nextOnBoardStep(event, $steps.MANUAL_PRODUCTS_2_STEP);
        }
    });

    $body.on('DOMSubtreeModified', '#zoneRangeValues', function () {
        if (onBoard.currentStep === $steps.MANUAL_ZONES_4_STEP) {
            nextOnBoardStep(event, $steps.MANUAL_ZONES_5_STEP);
        }
    });

    $body.on('DOMSubtreeModified', '.service-configuration-container', function () {
        if (onBoard.currentStep === $steps.MANUAL_PRODUCTS_3_STEP) {
            makeStepAction();
        }
    });

    $body.on('click', '.service-select-container .add-service-button', function () {
        nextOnBoardStep(event, $steps.MANUAL_PRODUCTS_3_STEP);
    });

    $body.on('click', 'button[id="submitDpdConnection"]', validateOnBoardLogin);
    $body.on('click', 'button[name="submitProcessImport"]', validateImportExport);
    $body.on('animationend', $onBoardSection, removeAnimationClass);

    function autoZonesImport() {
        $("#import-modal-confirm").modal();
    }

    function nextOnBoardStep(event, nextStep) {
        nextStep = nextStep || false;

        if ($(this).data('validate') !== undefined) {
            var $validateField = $(this).data('validate');
            var $elementsToValidate = $('body').find($validateField);
            var $stopNextStepEvent = false;

            $elementsToValidate.each(function () {
                if (!$(this).is(':visible')) {
                    return true;
                }

                if ($(this).is('input')) {
                    if (!$(this).val()) {
                        event.preventDefault();
                        stopActionAndAddShakeEffect();
                        $stopNextStepEvent = true;

                        return false;
                    }
                } else {
                    if ($(this).has( '.search-choice' ).length === 0) {
                        event.preventDefault();
                        stopActionAndAddShakeEffect();
                        $stopNextStepEvent = true;

                        return false;
                    }
                }
            });
        }

        if ($stopNextStepEvent === true) {
            return false;
        }

        if (!nextStep) {
            nextStep = $(this).data('next-step');
        }

        $.ajax(onBoard.ajaxUrl, {
            method: 'POST',
            data: {
                action: 'nextOnBoardStep',
                ajax: 1,
                nextStep: nextStep
            },
            success: function (response) {
                response = JSON.parse(response);

                if (!response.status) {
                    return false;
                }

                if (response.stepTemplate) {
                    replaceOnBoardTemplate(response.stepTemplate);
                }

                onBoard.currentStep = response.currentStep;

                hideMarkings();

                makeStepAction(response.currentStep);
            }
        });
    }

    function stopOnBoard(event, disableOnlyInBackend) {
        disableOnlyInBackend = disableOnlyInBackend || false;

        if (!disableOnlyInBackend) {
            if(!confirm(onBoard.stopWarning)) {
                return false;
            }

            disableOnBoard();
        }

        $.ajax(onBoard.ajaxUrl, {
            method: 'POST',
            data: {
                action: 'stopOnBoard',
                ajax: 1
            },
            success: function (response) {
            }
        });
    }

    function pauseOnBoard(event) {
        if(!confirm(onBoard.pauseWarning)) {
            return false;
        }

        disableOnBoard();

        $.ajax(onBoard.ajaxUrl, {
            method: 'POST',
            data: {
                action: 'stopOnBoard',
                pauseOnBoard: true,
                ajax: 1
            },
            success: function (response) {
                response = JSON.parse(response);

                if (response.status) {
                    location.reload();
                }
            }
        });
    }

    function replaceOnBoardTemplate(stepTemplate) {
        $onBoardSection.find('#dpd-on-board').html(stepTemplate.onBoardTemplate);

        if(typeof stepTemplate.progressBarTemplate !== 'undefined') {
            if ($onBoardSection.find('.dpd-on-board-bottom-progress-container').length) {
                $onBoardSection.find('.dpd-on-board-bottom-progress-container').replaceWith(stepTemplate.progressBarTemplate);
            } else {
                $onBoardSection.append(stepTemplate.progressBarTemplate);
            }
        }
    }

    function markOnBoardElement($elementSelector, $markClass) {
        $($elementSelector).addClass($markClass);
    }

    function scrollToElement($element) {
        $('html, body').animate({
            scrollTop: $($element).first().offset().top - 280
        }, 500);
    }

    function scrollToTop($element) {
        $(window).scrollTop(0);
    }

    function disableOnBoard() {
        $onBoardSection.remove();

        hideMarkings();
    }

    function showOnBoard() {
        $onBoardSection.removeClass('hidden d-none');

        makeStepAction(onBoard.currentStep);
    }

    function hideOnBoard() {
        $onBoardSection.addClass('hidden d-none');

        hideMarkings();
    }

    function validateOnBoardLogin(event) {
        if (!$('input[name="DPD_WEB_SERVICE_USERNAME"]').val() ||
            !$('input[name="DPD_WEB_SERVICE_PASSWORD"]').val()
        ) {
            event.preventDefault();
            stopActionAndAddShakeEffect();
        }
    }

    function validateImportExport(event) {
        if (!$('input[name="DPD_IMPORT_FILE"]').val()) {
            event.preventDefault();
            stopActionAndAddShakeEffect();
        }
    }

    function removeAnimationClass() {
        $onBoardSection.find('#dpd-on-board').removeClass('on-board-shake');
    }

    function stopActionAndAddShakeEffect() {
        $onBoardSection.find('#dpd-on-board').addClass('on-board-shake');
    }

    function hideMarkings() {
        $('.on-board-border-primary-color').removeClass('on-board-border-primary-color');
        $('.on-board-border-secondary-color').removeClass('on-board-border-secondary-color');
        $('.on-board-border-radius').removeClass('on-board-border-radius');
        $('.on-board-import-export-mod').removeClass('on-board-import-export-mod');
        $('.on-board-after-primary-color').removeClass('on-board-after-primary-color');
    }

    function makeStepAction(currentStep) {
        switch (currentStep) {
            case $steps.MAIN_2_STEP:
                $onBoardSection.find('#dpd-on-board').removeClass('center-top').addClass('right-top');
                markOnBoardElement(
                    'input[name="DPD_WEB_SERVICE_USERNAME"], input[name="DPD_WEB_SERVICE_PASSWORD"]',
                    'on-board-border-primary-color'
                );
                markOnBoardElement(
                    '#conf_id_DPD_WEB_SERVICE_COUNTRY .radio label',
                    'on-board-border-primary-color'
                );
                markOnBoardElement(
                    'button[id="submitDpdConnection"]',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_ZONES_0_STEP:
            case $steps.MANUAL_ZONES_1_STEP:
                markOnBoardElement(
                    '#subtab-AdminDPDBalticsZones',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_ZONES_2_STEP:
                markOnBoardElement(
                    '#desc-dpd_zone-new i',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_ZONES_3_STEP:
                markOnBoardElement(
                    '.js-dpd-zone-name',
                    'on-board-border-primary-color'
                );
                break;
            case $steps.MANUAL_ZONES_4_STEP:
                $onBoardSection.find('#dpd-on-board').addClass('zone');
                markOnBoardElement(
                    '#zoneRangeForm',
                    'on-board-border-primary-color'
                );
                markOnBoardElement(
                    '.table.table-bordered',
                    'border-collapse'
                );
                break;
            case $steps.MANUAL_ZONES_5_STEP:
                markOnBoardElement(
                    'button[name="processSaveZoneRanges"]',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_0_STEP:
            case $steps.MANUAL_ZONES_6_STEP:
                markOnBoardElement(
                    '#subtab-AdminDPDBalticsProducts',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_2_STEP:
                $onBoardSection.find('#dpd-on-board').removeClass('right-top').addClass('center-top');
                markOnBoardElement(
                    '.js-product-form .js-edit-product',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_3_STEP:
                $onBoardSection.find('#dpd-on-board').removeClass('center-top').addClass('right-top product');
                markOnBoardElement(
                    '.js-product-form.edit-action .js-product-name',
                    'on-board-border-primary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_4_STEP:
                markOnBoardElement(
                    '.js-product-form.edit-action .js-product-delivery-time',
                    'on-board-border-primary-color'
                );
                markOnBoardElement(
                    '.js-product-form.edit-action .js-product-delivery-time-lang .chosen-single',
                    'on-board-border-primary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_5_STEP:
                markOnBoardElement(
                    '.js-product-form.edit-action .js-product-zones .chosen-choices',
                    'on-board-border-primary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_5_SHOP_STEP:
                markOnBoardElement(
                    '.js-product-form.edit-action .js-product-shop .chosen-choices',
                    'on-board-border-primary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_6_STEP:
                $onBoardSection.find('#dpd-on-board').removeClass('right-top').addClass('left-top');
                markOnBoardElement(
                    '.js-product-form.edit-action .js-product-cod-switch',
                    'on-board-border-primary-color on-board-border-radius'
                );
                break;
            case $steps.MANUAL_PRODUCTS_7_STEP:
                markOnBoardElement(
                    '.js-product-form.edit-action .js-product-active-switch',
                    'on-board-border-primary-color on-board-border-radius'
                );
                break;
            case $steps.MANUAL_PRODUCTS_8_STEP:
                markOnBoardElement(
                    '.js-product-form.edit-action .js-save-product',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_PRODUCTS_9_STEP:
            case $steps.MANUAL_PRICE_RULES_0_STEP:
                $onBoardSection.find('#dpd-on-board').removeClass('left-top').addClass('right-top');
                markOnBoardElement(
                    '#subtab-AdminDPDBalticsPriceRules',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_PRICE_RULES_1_STEP:
                markOnBoardElement(
                    '#desc-dpd_price_rule-new i',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.MANUAL_PRICE_RULES_2_STEP:
                markOnBoardElement(
                    '.dpd-price-rule-customer-types .radio',
                    'on-board-after-primary-color'
                );
                scrollToTop();

                break;
            case $steps.MANUAL_PRICE_RULES_3_STEP:
                markOnBoardElement(
                    '.dpd-price-rule-name #name',
                    'on-board-border-primary-color'
                );
                scrollToElement('.dpd-price-rule-name #name');

                break;
            case $steps.MANUAL_PRICE_RULES_4_STEP:
                markOnBoardElement(
                    '.dpd-price-rule-ranges .ranges-input',
                    'on-board-border-primary-color on-board-border-radius'
                );
                scrollToElement('.dpd-price-rule-ranges .ranges-input');

                break;
            case $steps.MANUAL_PRICE_RULES_5_STEP:
                markOnBoardElement(
                    '.dpd-price-rule-carriers .panel',
                    'on-board-border-primary-color'
                );
                scrollToElement('.dpd-price-rule-carriers .panel');

                break;
            case $steps.MANUAL_PRICE_RULES_6_STEP:
                markOnBoardElement(
                    '.dpd-price-rule-zones .search-block-wrapper .chosen-choices',
                    'on-board-border-primary-color'
                );
                scrollToElement('.dpd-price-rule-zones .search-block-wrapper .chosen-choices');

                break;
            case $steps.MANUAL_PRICE_RULES_7_STEP:
                markOnBoardElement(
                    '.dpd-price-rule-shipping-price .input-group',
                    'on-board-border-primary-color on-board-border-radius'
                );
                markOnBoardElement(
                    '.dpd-price-rule-payment-methods .panel',
                    'on-board-border-primary-color'
                );
                scrollToElement('.dpd-price-rule-shipping-price .input-group');

                break;
            case $steps.MANUAL_PRICE_RULES_8_STEP:
                markOnBoardElement(
                    '#dpd_price_rule_form_submit_btn_1',
                    'on-board-border-secondary-color'
                );
                scrollToElement('#dpd_price_rule_form_submit_btn_1');

                break;
            case $steps.IMPORT_1_STEP:
                markOnBoardElement(
                    'a[id$="-import_export"]',
                    'on-board-border-secondary-color on-board-import-export-mod'
                );
                break;
            case $steps.IMPORT_2_STEP:
                markOnBoardElement(
                    '#conf_id_DPD_IMPORT_FILE .dummyfile.input-group',
                    'on-board-border-primary-color on-board-border-radius'
                );
                markOnBoardElement(
                    'button[name="submitProcessImport"]',
                    'on-board-border-secondary-color'
                );
                break;
            case $steps.IMPORT_FINISH_STEP:
            case $steps.MANUAL_CONFIG_FINISH_STEP:
                stopOnBoard(event, true);
                break;
        }
    }

    function getStepsNames() {
        return {
            'MAIN_2_STEP' : 'StepMain2',
            'MAIN_3_STEP' : 'StepMain3',
            'MANUAL_ZONES_0_STEP' : 'StepManualZones0',
            'MANUAL_ZONES_1_STEP' : 'StepManualZones1',
            'MANUAL_ZONES_2_STEP' : 'StepManualZones2',
            'MANUAL_ZONES_3_STEP' : 'StepManualZones3',
            'MANUAL_ZONES_4_STEP' : 'StepManualZones4',
            'MANUAL_ZONES_5_STEP' : 'StepManualZones5',
            'MANUAL_ZONES_6_STEP' : 'StepManualZones6',
            'MANUAL_PRODUCTS_0_STEP' : 'StepManualProducts0',
            'MANUAL_PRODUCTS_1_STEP' : 'StepManualProducts1',
            'MANUAL_PRODUCTS_2_STEP' : 'StepManualProducts2',
            'MANUAL_PRODUCTS_3_STEP' : 'StepManualProducts3',
            'MANUAL_PRODUCTS_4_STEP' : 'StepManualProducts4',
            'MANUAL_PRODUCTS_5_STEP' : 'StepManualProducts5',
            'MANUAL_PRODUCTS_5_SHOP_STEP' : 'StepManualProducts5Shop',
            'MANUAL_PRODUCTS_6_STEP' : 'StepManualProducts6',
            'MANUAL_PRODUCTS_7_STEP' : 'StepManualProducts7',
            'MANUAL_PRODUCTS_8_STEP' : 'StepManualProducts8',
            'MANUAL_PRODUCTS_9_STEP' : 'StepManualProducts9',
            'MANUAL_PRICE_RULES_0_STEP' : 'StepManualPriceRules0',
            'MANUAL_PRICE_RULES_1_STEP' : 'StepManualPriceRules1',
            'MANUAL_PRICE_RULES_2_STEP' : 'StepManualPriceRules2',
            'MANUAL_PRICE_RULES_3_STEP' : 'StepManualPriceRules3',
            'MANUAL_PRICE_RULES_4_STEP' : 'StepManualPriceRules4',
            'MANUAL_PRICE_RULES_5_STEP' : 'StepManualPriceRules5',
            'MANUAL_PRICE_RULES_6_STEP' : 'StepManualPriceRules6',
            'MANUAL_PRICE_RULES_7_STEP' : 'StepManualPriceRules7',
            'MANUAL_PRICE_RULES_8_STEP' : 'StepManualPriceRules8',
            'IMPORT_1_STEP' : 'StepImport1',
            'IMPORT_2_STEP' : 'StepImport2',
            'IMPORT_FINISH_STEP' : 'StepImportFinish',
            'MANUAL_CONFIG_FINISH_STEP' : 'StepManualConfigFinish',
        }
    }
});
