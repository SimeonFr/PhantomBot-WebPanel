//noinspection JSUnusedGlobalSymbols
/**
 * switch-toggle.js
 * Created with PhpStorm
 * User: Robin | Juraji
 * Date: 14 okt 2015
 * Time: 05:58
 */

function switchToggleCallback(callback, callbackParams) {
  if (callback) {
    var that = this;
    var t = setTimeout(function () {
      callback.apply(that, callbackParams);
      clearTimeout(t);
    }, 300);
  }
}