
function RodzetaFeedbackfields_SortableParameterEdit(arParams) {
  "use strict";

  var arElements = arParams.getElements();
  var jsOptions = JSON.parse(arParams.data);

  console.log(arParams);
  console.log(jsOptions);

  //arParams.oCont.insertAdjacentHTML("beforeend", '<ul id="sortable111"><li>address</li><li>phone</li><li>name</li></ul>');

  var $ul = arParams.oCont.appendChild(BX.create("UL", {
    attrs: {
      style: "padding-left:8px;"
    },
    //html: "<li>address</li><li>phone</li><li>name</li>"
  }));
  for (var i = 0, l = jsOptions.fields.length; i < l; i++) {
    $ul.appendChild(BX.create("LI", {
      attrs: {
        style: "padding-top:10px;padding-bottom:10px;cursor:move;cursor:grab;cursor:-moz-grab;cursor:-webkit-grab;list-style-type:none;"
      },
      html: '<input type="hidden" class="js-custom-sort-data js-custom-sort-data-' + jsOptions.fields[i][0]
          + '" name="' + arParams.propertyID + '[' + jsOptions.fields[i][0]
          + ']" value="' + jsOptions.fields[i][1] + '" '
          + (jsOptions.fields[i][2]? '' : 'disabled') + '>'
        + '<input type="checkbox" class="js-custom-sort-data-checkbox"'
          + ' data-code="' + jsOptions.fields[i][0] + '" '
          + (jsOptions.fields[i][2]? 'checked' : '') + '>'
          + jsOptions.fields[i][1]
        //+ (jsOptions.fields[i][2]? "checked" : "") +
    }));
  }

  var $items = document.querySelectorAll(".js-custom-sort-data-checkbox");
  for (var i = 0, l = $items.length; i < l; i++) {
    BX.bind($items[i], "change", function () {
      var $el = document.querySelector(".js-custom-sort-data-" + this.getAttribute("data-code"));
      $el.disabled = !this.checked;
    });
  }

  function onSubmit($form) {
    /*
    var $items = $form.querySelectorAll(".js-custom-sort-data");
    var params = [];
    for (var i = 0, l = $items.length; i < l; i++) {
      params.push([$items[i].getAttribute("data-code"), $items[i].value, $items[i].checked]);
    }
    BX.ajax.post(jsOptions.baseUrl + "/sortdata/", {
      id: jsOptions.id,
      data: JSON.stringify(params)
    });
    */
  }

  BX.loadScript(jsOptions.baseUrl + ".sortable.js", function () {
    Sortable.create($ul);
    /*
    var $form = document.querySelector('form[name="bx_popup_form"]');
    BX.bind($form, "submit", function () {
      onSubmit($form);
    });
    BX.bind(BX("bx-comp-params-save-button"), "click", function () {
      alert(1);
      onSubmit($form);
    });
    */
  });

  /*
  var obLabel = arParams.oCont.appendChild(BX.create("SPAN", {
    html: arParams.oInput.value
  }));
  var obButton = arParams.oCont.appendChild(BX.create("BUTTON", {
    html: jsOptions["set"]
  }));

  obButton.onclick = BX.delegate(function () {
    BX.calendar({
      node: obButton,
      //value: arParams.oInput.value,
      field: arParams.oInput,
      callback_after: function () {
        obLabel.innerHTML = arParams.oInput.value;
      }
    });
    return false;
  });
  */

}