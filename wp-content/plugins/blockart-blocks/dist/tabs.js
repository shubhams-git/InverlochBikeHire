/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;
  for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
  return arr2;
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return _arrayLikeToArray(arr);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return _arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
}
;// CONCATENATED MODULE: ./src/frontend/blocks/tabs.ts

(function () {
  var _window$blockartUtils = window.blockartUtils,
    $$ = _window$blockartUtils.$$,
    domReady = _window$blockartUtils.domReady,
    toArray = _window$blockartUtils.toArray,
    each = _window$blockartUtils.each,
    on = _window$blockartUtils.on;
  var initTabs = function initTabs() {
    var tabs = toArray($$('.blockart-tabs'));
    if (!tabs.length) return;
    each(tabs, function (tab) {
      on('click', tab, function (e) {
        var _target, _target2;
        e.preventDefault();
        var target = e.target;
        target = (_target = target) !== null && _target !== void 0 && _target.classList.contains('blockart-tabs-trigger') ? target : (_target2 = target) === null || _target2 === void 0 ? void 0 : _target2.closest('.blockart-tabs-trigger');
        var id = target.dataset.tab;
        var contents = _toConsumableArray(tab.children).filter(function (c) {
          return c.classList.contains('blockart-tab');
        });
        var newActiveContent = contents.find(function (c) {
          return c.classList.contains("blockart-tab-".concat(id));
        });
        if (newActiveContent) {
          var activeContent = contents.find(function (c) {
            return c.classList.contains('is-active');
          });
          var activeTabTitle = tab.querySelector('.is-active');
          if (activeContent && activeTabTitle) {
            activeContent.classList.remove('is-active');
            activeTabTitle.classList.remove('is-active');
          }
          target.classList.add('is-active');
          newActiveContent.classList.add('is-active');
        }
      });
    });
  };
  domReady(initTabs);
})();
/******/ })()
;