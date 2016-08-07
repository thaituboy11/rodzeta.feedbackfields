
var SiteApp = (function (app) {
	"use strict";

  var $ = app.jQuery = jQuery;
  var yandexCounter = null;

	// custom error message
  app.showAlert = function () {
    //$(".error-message-container").addClass("reveal");
    //$("#error-message").addClass("reveal");
  };
  //$(".error-message-container, #error-message .btn").click(function () {
    //$(this).toggleClass("reveal");
    //$("#error-message").toggleClass("reveal");
  //});

  app.showPopup = function (popupId) {
  	$.fancybox({
      href: "#" + popupId,
      autoDimensions:false,
      padding: 0
    });
  };

  app.validateFields = function ($form) {
  	/*
  	if ($.trim($form.find(".form-input-name").val()) == "") {
      return false;
    }
    if ($.trim($form.find(".form-input-phone").val()) == "") {
      return false;
    }
    if ($.trim($form.find(".form-input-email").val()) == "") {
      return false;
    }
    */
    return true;
  };

  app.initForm = function ($form) {
  	$form.find(".form-input-phone").mask("+7 (999) 999-9999");

    $form.ajaxForm({
	    success: function (data) {
	      if ($form.attr("data-popup")) {
	      	app.showPopup($form.attr("data-popup"));
	      } else {
	        var result = JSON.parse(data);
	        if (result.error != "") {
	          var $formError = $form.find(".form-result-error");
	          $formError.html(result.error); // comment this line for use default message
	          $formError.show();
	        } else {
	          var $formSuccess = $form.find(".form-result-success").clone();
	          $form.html($formSuccess.html());
	        }
	      }
	    },
	    beforeSubmit: function (formData, jqForm, options) {
	      // clear error message
	      var $formError = $form.find(".form-result-error");
	      $formError.html("");
	      if (!app.validateFields(jqForm)) {
	        app.showAlert();
	        return false;
	      }

	      // targets
        var yandexTarget = jqForm.attr("data-yandex-target")?
          jqForm.attr("data-yandex-target") : "Otpravit";
        //console.log(yandexTarget);
        if (yandexCounter) {
          yandexCounter.reachGoal(yandexTarget);
        }

	      return true;
	    }
	  });
  };

	$(function ($) {

		$(".js-form").each(function () {
      app.initForm($(this));
    });

    // custom init
    $(".btn.popup-form").click(function () {
      var $form = $($(this).attr("href")).find("form");
    	var $object = $form.find(".form-input-object");
    	var $idform = $form.find(".form-input-idform");
    	$idform.val("");
      $object.val("");

    	// targets
      if (yandexCounter) {
        yandexCounter.reachGoal("Zayavka");
      }

      /*
    	var $specialOffer = $(this).closest(".special-offer-info");
    	if ($specialOffer.length) {
    			$object.val($.trim($specialOffer.find(".special-offer-descr h3").text()));
    			$idform.val($.trim($("#special-offers h2:first").text()));
    		return;
    	}

    	if ($(this).hasClass("btn-uchastok")) {
    		$idform.val("Участки, прилегающие к лесопарковой зоне");
    		return;
    	}

    	var $houseOrder = $(this).closest(".house");
    	if ($houseOrder.length) {
    		$object.val($.trim($houseOrder.find(".house-descr h3").text()));
        $idform.val($.trim($("#houses-list h2").text()));
    		return;
    	}
      */
    });


	});

  return app;
})(typeof SiteApp != "undefined"? SiteApp : {});
