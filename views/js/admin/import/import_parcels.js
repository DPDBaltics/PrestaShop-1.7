$(window).load(function () {
  if (typeof onBoard === 'undefined') {
    $("#import-modal-confirm-parcels").modal();
  }

  $('.import-parcels-button').click(function () {
    $.ajax(dpdAjaxUrl, {
      method: 'POST',
      data: {
        ajax: 1,
        countryId: countryId,
        action: 'importParcels'
      },
      beforeSend: function () {
        // Show image container
        $(".import-modal-confirm").modal('hide');
        $("#import-parcels-modal").modal();
        loadImport();
      },
      success: function(response) {
        response = JSON.parse(response);
        if (response.success) {
          showSuccessMessage(response.success_message);
        } else {
          showErrorMessage(response.error);
        }
      },
      complete: function () {
        // Hide image container
        $("#import-modal-confirm").modal('hide');
        $("#import-parcels-modal").modal('hide');
        stopLoadImport();

        if (typeof onBoard !== 'undefined') {
          nextOnBoardStep('DPD_MANUAL_PARCELS_AFTER_IMPORT_STEP');
        }
      }
    });
  });

  function loadImport() {
    toggleInterval = setInterval(function () {
      $('#js-loading-parcels').toggle();
      $('#js-importing-parcels').toggle();
    }, 2000);
  }

  function stopLoadImport() {
    clearInterval(toggleInterval);
  }

  function nextOnBoardStep(nextStep) {
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
          $onBoardSection = $('.dpd-on-board-section');

          $onBoardSection.find('#dpd-on-board').html(response.stepTemplate.onBoardTemplate);

          if (typeof response.stepTemplate.progressBarTemplate !== 'undefined') {
            if ($onBoardSection.find('.dpd-on-board-bottom-progress-container').length) {
              $onBoardSection.find('.dpd-on-board-bottom-progress-container').replaceWith(response.stepTemplate.progressBarTemplate);
            } else {
              $onBoardSection.append(response.stepTemplate.progressBarTemplate);
            }
          }

          $onBoardSection.removeClass('hidden d-none');
        }
      }
    });
  }
});