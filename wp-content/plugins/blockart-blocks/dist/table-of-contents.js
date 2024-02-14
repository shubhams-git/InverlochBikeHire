/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
(function () {
  var _window$blockartUtils = window.blockartUtils,
    $$ = _window$blockartUtils.$$,
    domReady = _window$blockartUtils.domReady,
    find = _window$blockartUtils.find,
    parseHTML = _window$blockartUtils.parseHTML,
    toArray = _window$blockartUtils.toArray,
    each = _window$blockartUtils.each,
    on = _window$blockartUtils.on;
  var SELECTORS = {
    TOC: '.blockart-toc',
    TOC_ANCHOR: '.blockart-toc-anchor',
    SMOOTH_SCROLL: '#ba-smooth-scroll'
  };
  var addSmoothScroll = function addSmoothScroll() {
    if (!find(SELECTORS.SMOOTH_SCROLL)) {
      var style = Object.assign(document.createElement('style'), {
        id: 'ba-smooth-scroll',
        innerHTML: "html {scroll-behavior: smooth;}"
      });
      document.head.appendChild(style);
    }
  };
  var toggleToc = function toggleToc(e) {
    var _e$target;
    e.preventDefault();
    var parent = (_e$target = e.target) === null || _e$target === void 0 ? void 0 : _e$target.closest(SELECTORS.TOC);
    var collapsed = parent.getAttribute('data-collapsed');
    if (collapsed === 'true') {
      parent.setAttribute('data-collapsed', 'false');
    } else {
      parent.setAttribute('data-collapsed', 'true');
    }
  };
  var createAnchors = function createAnchors(headings) {
    if (!(headings !== null && headings !== void 0 && headings.length)) return;
    var _iterator = _createForOfIteratorHelper(headings),
      _step;
    try {
      var _loop = function _loop() {
        var heading = _step.value;
        var headingEl = Array.from($$("h".concat(heading.level))).find(function (h) {
          return h.textContent === heading.content;
        });
        if (!headingEl || !!find(headingEl, "#".concat(heading.id.replace(/\d/g, function (match) {
          return '\\3' + match;
        })))) return 1; // continue
        var anchor = parseHTML("<span id=\"".concat(heading.id, "\" class=\"").concat(SELECTORS.TOC_ANCHOR, "\"></span>"), 1);
        headingEl === null || headingEl === void 0 || headingEl.insertAdjacentElement('afterbegin', anchor);
      };
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        if (_loop()) continue;
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
  };
  var initTOC = function initTOC() {
    var tocs = toArray($$('.blockart-toc'));
    each(tocs, function (toc) {
      if (toc.dataset.toc) {
        var _window;
        createAnchors((_window = window) === null || _window === void 0 ? void 0 : _window[toc.dataset.toc]);
      }
      if (toc.dataset.collapsed) {
        var toggle = find(toc, '.blockart-toc-toggle');
        on('click', toggle, toggleToc);
      }
    });
  };
  domReady(function () {
    addSmoothScroll();
    initTOC();
  });
})();
/******/ })()
;