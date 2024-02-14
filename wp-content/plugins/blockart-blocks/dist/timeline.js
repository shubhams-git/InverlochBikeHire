/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
(function () {
  var _window$blockartUtils = window.blockartUtils,
    domReady = _window$blockartUtils.domReady,
    $$ = _window$blockartUtils.$$;
  domReady(function () {
    var Timeline = function Timeline() {
      var timelineLines = Array.from($$('.blockart-timeline ~ .blockart-timeline-line'));
      for (var _i = 0, _timelineLines = timelineLines; _i < _timelineLines.length; _i++) {
        var _markers$item, _markers$item2, _timeLine$getBounding;
        var timelineLine = _timelineLines[_i];
        var timeLine = timelineLine === null || timelineLine === void 0 ? void 0 : timelineLine.previousElementSibling;
        var markers = timeLine === null || timeLine === void 0 ? void 0 : timeLine.querySelectorAll('.blockart-timeline-marker');
        var firstMarker = markers === null || markers === void 0 ? void 0 : markers.item(0);
        var lastMarker = markers === null || markers === void 0 ? void 0 : markers.item(markers.length - 1);
        if (!(firstMarker && lastMarker)) {
          return;
        }
        var firstMarkerOffsetHeight = firstMarker.offsetTop + firstMarker.offsetHeight;
        var lastMarkerOffsetHeight = lastMarker.offsetTop + lastMarker.offsetHeight;
        var lineWidth = timelineLine === null || timelineLine === void 0 ? void 0 : timelineLine.offsetWidth;
        var markerOffsetX = 25 + (markers === null || markers === void 0 || (_markers$item = markers.item(0)) === null || _markers$item === void 0 ? void 0 : _markers$item.offsetWidth) / 2 - lineWidth / 2;
        timelineLine.style.top = firstMarkerOffsetHeight + 'px';
        timelineLine.style.height = lastMarkerOffsetHeight - firstMarkerOffsetHeight + 'px';
        timelineLine.style.left = markerOffsetX + 'px';
        if (markers !== null && markers !== void 0 && (_markers$item2 = markers.item(0)) !== null && _markers$item2 !== void 0 && (_markers$item2 = _markers$item2.getBoundingClientRect()) !== null && _markers$item2 !== void 0 && _markers$item2.left && timeLine !== null && timeLine !== void 0 && (_timeLine$getBounding = timeLine.getBoundingClientRect()) !== null && _timeLine$getBounding !== void 0 && _timeLine$getBounding.left) {
          var _markers$item3, _markers$item4;
          timelineLine.style.left = (markers === null || markers === void 0 || (_markers$item3 = markers.item(0)) === null || _markers$item3 === void 0 || (_markers$item3 = _markers$item3.getBoundingClientRect()) === null || _markers$item3 === void 0 ? void 0 : _markers$item3.left) + (markers === null || markers === void 0 || (_markers$item4 = markers.item(0)) === null || _markers$item4 === void 0 ? void 0 : _markers$item4.offsetWidth) / 2 - lineWidth / 2 + 'px';
        }
      }
      var resizeTimer;
      window.addEventListener('resize', function () {
        if (resizeTimer) {
          clearTimeout(resizeTimer);
        }
        resizeTimer = setTimeout(function () {
          Timeline();
        }, 5);
      });
    };
    Timeline();
  });
})();
/******/ })()
;