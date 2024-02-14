/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
(function () {
  var _window$blockartUtils = window.blockartUtils,
    $$ = _window$blockartUtils.$$,
    domReady = _window$blockartUtils.domReady,
    toArray = _window$blockartUtils.toArray,
    each = _window$blockartUtils.each,
    find = _window$blockartUtils.find;
  var formatTimeUnit = function formatTimeUnit(date) {
    return String(date).padStart(2, '0');
  };
  var calculateTime = function calculateTime(timestamp) {
    var currentDate = new Date();
    var diff = timestamp - currentDate.getTime();
    var result = {
      days: '00',
      hours: '00',
      minutes: '00',
      seconds: '00'
    };
    if (diff < 0) return result;
    result.days = formatTimeUnit(Math.floor(diff / (1000 * 60 * 60 * 24)));
    result.hours = formatTimeUnit(Math.floor(diff % (1000 * 60 * 60 * 24) / (1000 * 60 * 60)));
    result.minutes = formatTimeUnit(Math.floor(diff % (1000 * 60 * 60) / (1000 * 60)));
    result.seconds = formatTimeUnit(Math.floor(diff % (1000 * 60) / 1000));
    return result;
  };
  var initCountdown = function initCountdown() {
    var countdowns = toArray($$('.blockart-countdown'));
    if (!countdowns.length) return;
    each(countdowns, function (countdown) {
      var _countdown$dataset;
      var timestamp = (_countdown$dataset = countdown.dataset) === null || _countdown$dataset === void 0 ? void 0 : _countdown$dataset.expiryTimestamp;
      if (!timestamp) return;
      var time = calculateTime(parseInt(timestamp));
      if (Object.values(time).every(function (value) {
        return value === '00';
      })) return;
      var interval = setInterval(function () {
        time = calculateTime(parseInt(timestamp));
        if (Object.values(time).every(function (value) {
          return value === '00';
        })) {
          clearInterval(interval);
        }
        for (var t in time) {
          var num = find(countdown, ".blockart-countdown-number-".concat(t));
          if (num && num.innerHTML !== time[t]) {
            num.innerHTML = time[t];
          }
        }
      }, 1000);
    });
  };
  domReady(initCountdown);
})();
/******/ })()
;