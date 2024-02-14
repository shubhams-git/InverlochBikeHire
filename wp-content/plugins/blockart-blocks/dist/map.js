/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
function _typeof(o) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, _typeof(o);
}
;// CONCATENATED MODULE: ./src/frontend/blocks/map.ts

function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
(function () {
  var _window$blockartUtils = window.blockartUtils,
    $$ = _window$blockartUtils.$$,
    domReady = _window$blockartUtils.domReady,
    parseHTML = _window$blockartUtils.parseHTML,
    toArray = _window$blockartUtils.toArray;
  var SELECTORS = {
    CANVAS: '.blockart-map[data-map]'
  };
  var svgToDataUri = function svgToDataUri(svg) {
    var svgEl = parseHTML(svg, 1);
    var serialized = new XMLSerializer().serializeToString(svgEl);
    return "data:image/svg+xml;base64,".concat(btoa(serialized));
  };
  var initMap = function initMap() {
    var canvas = toArray($$(SELECTORS.CANVAS));
    if (!canvas.length || (typeof google === "undefined" ? "undefined" : _typeof(google)) !== 'object' || _typeof(google.maps) !== 'object') return;
    var _iterator = _createForOfIteratorHelper(canvas),
      _step;
    try {
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        var _window, _canvas$dataset;
        var _canvas = _step.value;
        var options = (_window = window) === null || _window === void 0 ? void 0 : _window[(_canvas$dataset = _canvas.dataset) === null || _canvas$dataset === void 0 ? void 0 : _canvas$dataset.map];
        var map = new google.maps.Map(_canvas, options.map);
        if (options.marker) {
          options.marker.map = map;
          var marker = new google.maps.Marker(options.marker);
          if (options.markerIcon) {
            marker.setIcon(svgToDataUri(options.markerIcon));
          }
        }
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
  };
  domReady(initMap);
})();
/******/ })()
;