/**
 * install.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 12:34
 */
$(document).ready(function () {
  $('form').submit(function (event) {
    var currentField;
    $('div.text-danger').remove();
    for (var i = 0; i < event.target.length - 2; i++) {
      currentField = $(event.target[i]);
      currentField.val(currentField.val().trim());
      if (currentField.attr('type') == 'text') {
        if (currentField.val().match(/[!@#$%^&*()+\-=\[\]{};'"\\|,<>\/?\s]/) != null || currentField.val() == '') {
          formError(currentField);
          event.preventDefault();
          return;
        }
      } else if (currentField.attr('type') == 'number') {
        currentField.val(currentField.val().trim());
        if (currentField.val().match(/[a-z!@#$%^&*()_+\-=\[\]{};'"\\|,.<>\/?\s]/i) != null || currentField.val() == '') {
          formError(currentField);
          event.preventDefault();
          return;
        }
      } else if (currentField.attr('type') == 'password') {
        if (currentField.val().match(/[()=\[\]\^{};:'"\\|,.<>\/\s]/) != null || currentField.val() == '') {
          formError(currentField);
          event.preventDefault();
          return;
        }
        //noinspection JSCheckFunctionSignatures,JSUnresolvedVariable
        currentField.val(SparkMD5.hash(currentField.val()));
      }
    }
  });
});

function formError(field) {
  field.parent('div').addClass('has-error').prepend('<div class="text-danger">Something is wrong in this field</div>');
}