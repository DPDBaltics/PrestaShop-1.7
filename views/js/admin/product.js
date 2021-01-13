$(document).ready(function() {
    $(document).on('click', 'button[name="saveProduct"]', saveProduct);
    $(document).on('click', 'a[name="editAvailability"]', saveProduct);
    $(document).on('click', 'button[name="editProduct"]', startProductEdit);

    var languageSelectors = $('select[name="languages"]');

    languageSelectors.on('change', function () {
        toggleDeliveryTime(this);
    });

    function toggleDeliveryTime(languageSelector) {
        var selectedLanguage = $(languageSelector).val();
        var deliveryTimeParent = $(languageSelector).closest("form");
        deliveryTimeParent.find("input[name^='delivery-time-']").hide();
        deliveryTimeParent.find('input[name="delivery-time-' + selectedLanguage + '"]').show();
    }

    function saveProduct(event) {
        toggleSubmitButtons(false);
        event.preventDefault();

        var $form = $(this).closest('form');

        if (!validateFormEvent($form)) {
            toggleSubmitButtons(true);

            return;
        }

        var action = $(this).attr('name');
        updateProduct($form, action);
        if (action === 'editAvailability') {
            window.location = $(this).attr('href');
        }
    }

    function validateFormEvent($form) {
        var errors = [];

        var namesValidation = validateCarrierNames($form);
        if (!namesValidation.status) {
            addFormError(namesValidation.ids, 'input', 'name');
            errors.push(errorMessages.noProductName);
        }

        var zonesValidation = validateZones($form);
        if (!zonesValidation.status) {
            errors.push(errorMessages.noZones);
        }

        var shopsValidation = validateShops($form);
        if (!shopsValidation.status) {
            errors.push(errorMessages.noShops);
        }

        if (errors.length) {
            errors.forEach(function (msg) {
                showErrorMessage(msg);
            });

            return false;
        }

        return true;
    }

    function updateProduct(form, submitAction) {
        var formData = $(form).serialize();
        $.ajax({
            'url': location.href,
            'method': 'POST',
            'data': {
                ajax: 1,
                action: 'updateProduct',
                data: formData
            },
            success: function (response) {
                var decodedResponse = JSON.parse(response);
                toggleSubmitButtons(true);

                if (!decodedResponse.status) {
                    showErrorMessage(errorMessages.productSaveFailed);

                    decodedResponse.errors.forEach(function (item) {
                        showErrorMessage(item);
                    });

                    return;
                }

                showSuccessMessage(messages.productSaveSuccess);
                endProductEdit(form);
            },
            error: function () {
                toggleSubmitButtons(true);
            }
        });
    }

    function toggleSubmitButtons(enable) {
        var $buttons = $('button[name="saveProduct"]');

        if (!enable) {
            $buttons.attr('disabled', true);
            return;
        }

        $buttons.attr('disabled', false);
    }

    function validateCarrierNames($form) {
        var $carrierNameInputs = $form.find('.js-product-name');
        var ids = [];

        $carrierNameInputs.each(function () {
            $(this).closest('div').removeClass('form-error');
            if ('' === $(this).val() && !$(this).prop('disabled')) {
                ids.push($(this).attr('id'));
                $(this).closest('div').addClass('form-error');
            }
        });

        return {
            status:ids.length === 0,
            ids: ids
        };
    }

    function validateZones($form) {
        var status = true;

        var zoneSelectors = $form.find('select[name*="zones_select"]');
        var zoneContainers = zoneSelectors.parent().find('.chosen-choices');

        zoneContainers.each(function () {
            if (!$(this).find('.search-choice').length) {
                $(this).closest('div').addClass('form-error');

                status = false;
            }
        });

        return {status: status};
    }

    function validateShops($form) {
        var status = true;

        if (isMultiShop) {
            var shopSelectors = $form.find('select[name*="shops_select"]');
            var shopContainers = shopSelectors.parent().find('.chosen-choices');

            shopContainers.each(function () {
                if (!$(this).find('.search-choice').length) {
                    $(this).closest('div').addClass('form-error');

                    status = false;
                }
            });
        }

        return {status: status};
    }

    function addFormError(ids, element, selector) {
        ids.forEach(function (item) {
            $(element+'['+selector+'="'+item+'"]').closest('.form-group').addClass('form-error');
        });
    }

    function startProductEdit() {
        event.preventDefault();
        var form = $(this).closest('form');
        form.addClass('edit-action');
        form.find('select').prop('disabled', false);
        form.find('input').prop('disabled', false);
        form.find('select').prop('disabled', false);
        form.find('a[name="editAvailability"]').removeAttr("disabled");
        form.find('button[name="saveProduct"]').show();
        form.find('.help-block').show();
        $(this).hide();
        $('select').trigger("chosen:updated");
    }

    function endProductEdit(form) {
        var formClosest = $(form).closest('form');

        formClosest.removeClass('edit-action');
        formClosest.find('select').prop('disabled', true);
        formClosest.find('input').prop('disabled', true);
        formClosest.find('select').prop('disabled', true);
        formClosest.find('a[name="editAvailability"]').attr('disabled', 'disabled');
        formClosest.find('button[name="editProduct"]').show();
        formClosest.find('button[name="saveProduct"]').hide();
        formClosest.find('.help-block').hide();
        $('select').trigger("chosen:updated");
    }
    
    function startProductAvailabilityEdit() {
        event.preventDefault();
        var form = $(this).closest('form');
        form.addClass('edit-action');
        form.find('select').prop('disabled', false);
        form.find('input').prop('disabled', false);
        form.find('select').prop('disabled', false);
        form.find('button[name="saveProduct"]').show();
        form.find('.help-block').show();
        $(this).hide();
        $('select').trigger("chosen:updated");
    }
});
