/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
(function () {
  var _window$blockartUtils = window.blockartUtils,
    domReady = _window$blockartUtils.domReady,
    $$ = _window$blockartUtils.$$,
    toArray = _window$blockartUtils.toArray,
    on = _window$blockartUtils.on,
    each = _window$blockartUtils.each,
    getCookie = _window$blockartUtils.getCookie,
    setCookie = _window$blockartUtils.setCookie;
  var initNotice = function initNotice() {
    var dismissIcons = toArray($$('.blockart-icon.dismiss'));
    if (!dismissIcons.length) return;
    each(dismissIcons, function (dismissIcon) {
      var _parent$dataset;
      var parent = dismissIcon.closest('.blockart-notice');
      var noticeId = parent === null || parent === void 0 || (_parent$dataset = parent.dataset) === null || _parent$dataset === void 0 ? void 0 : _parent$dataset.id;
      on('click', dismissIcon, function (e) {
        var _target, _target2, _target3, _target4;
        var target = e.target;
        if ('path' === ((_target = target) === null || _target === void 0 ? void 0 : _target.tagName)) {
          target = target.closest('svg');
        }
        parent.style.display = 'none';
        if (!((_target2 = target) !== null && _target2 !== void 0 && (_target2 = _target2.dataset) !== null && _target2 !== void 0 && _target2.hide)) return;
        var expirationDays = (_target3 = target) !== null && _target3 !== void 0 && (_target3 = _target3.dataset) !== null && _target3 !== void 0 && _target3.hide && '-1' !== ((_target4 = target) === null || _target4 === void 0 || (_target4 = _target4.dataset) === null || _target4 === void 0 ? void 0 : _target4.hide) ? parseFloat(target.dataset.hide) : 9999;
        if (expirationDays && noticeId) {
          setCookie('notice_' + noticeId, noticeId, expirationDays);
        }
      });
      var cookie = getCookie('notice_' + noticeId);
      if (!cookie) {
        parent.style.display = 'block';
      }
    });
  };
  domReady(initNotice);
})();
/******/ })()
;