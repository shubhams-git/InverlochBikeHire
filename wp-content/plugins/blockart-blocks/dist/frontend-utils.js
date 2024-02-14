(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["blockartUtils"] = factory();
	else
		root["blockartUtils"] = factory();
})(self, () => {
return /******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $: () => (/* binding */ $),
/* harmony export */   $$: () => (/* binding */ $$),
/* harmony export */   domReady: () => (/* binding */ domReady),
/* harmony export */   each: () => (/* binding */ each),
/* harmony export */   find: () => (/* binding */ find),
/* harmony export */   findAll: () => (/* binding */ findAll),
/* harmony export */   getCookie: () => (/* binding */ getCookie),
/* harmony export */   observeElementInView: () => (/* binding */ observeElementInView),
/* harmony export */   on: () => (/* binding */ on),
/* harmony export */   parseHTML: () => (/* binding */ parseHTML),
/* harmony export */   setCookie: () => (/* binding */ setCookie),
/* harmony export */   siblings: () => (/* binding */ siblings),
/* harmony export */   toArray: () => (/* binding */ toArray)
/* harmony export */ });
/**
 * Selects the first element that matches the given CSS selector.
 *
 * @function $
 * @param {string} selector - A CSS selector string used to select an element.
 * @returns {Element | null} - The first element that matches the selector, or null if no matches are found.
 * @throws {DOMException} Will throw a DOMException if the provided selector is invalid.
 *
 * @example
 * // Select the first paragraph on the page
 * const firstParagraph = $('.paragraph');
 *
 * // Select the first anchor tag within a specific div
 * const firstAnchorInDiv = $('#myDiv a');
 */
var $ = document.querySelector.bind(document);

/**
 * Selects and returns a list of elements that match the given CSS selector.
 *
 * @function $$
 * @param {string} selector - A CSS selector string used to select elements.
 * @returns {NodeList} - A NodeList containing the selected elements.
 * @throws {DOMException} Will throw a DOMException if the provided selector is invalid.
 *
 * @example
 * // Select all paragraphs on the page
 * const paragraphs = $$('.paragraph');
 *
 * // Select all anchor tags within a specific div
 * const anchorsInDiv = $$('#myDiv a');
 */
var $$ = document.querySelectorAll.bind(document);

/**
 * Executes a callback function when the DOM is ready.
 *
 * @function domReady
 * @param {() => void} callback - The callback function to be executed when the DOM is ready.
 *
 * @example
 * // Usage example:
 * domReady(() => {
 *   // Your code that depends on the DOM being ready goes here
 *   console.log('DOM is ready!');
 * });
 */
var domReady = function domReady(callback) {
  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    callback();
  } else {
    document.addEventListener('DOMContentLoaded', callback);
  }
};

/**
 * Returns an array of sibling elements of the given HTML element.
 *
 * @function siblings
 * @param {HTMLElement|string} elementOrSelector - The HTML element or selector whose siblings are to be retrieved.
 * @returns {HTMLElement[]} - An array containing the sibling elements.
 *
 * @example
 * // Usage example:
 * const myElement = document.getElementById('myElement');
 * const siblingElements = siblings(myElement);
 * console.log(siblingElements); // Array of sibling elements
 */
var siblings = function siblings(elementOrSelector) {
  var _el$parentElement$chi, _el$parentElement;
  var el = typeof elementOrSelector === 'string' ? $(elementOrSelector) : elementOrSelector;
  return Array.from((_el$parentElement$chi = el === null || el === void 0 || (_el$parentElement = el.parentElement) === null || _el$parentElement === void 0 ? void 0 : _el$parentElement.children) !== null && _el$parentElement$chi !== void 0 ? _el$parentElement$chi : []).filter(function (sibling) {
    return sibling !== el;
  });
};

/**
 * Observes an element to detect when it becomes visible in the viewport and invokes a callback.
 *
 * @function observeElementInView
 * @param {Element} element - The HTML element to observe for visibility changes.
 * @param {(...args: any[]) => void} onElementInView - The callback function to be invoked when the element is in view.
 *   @param {...any} args - Additional arguments to be passed to the callback function.
 *
 * @example
 * // Usage example:
 * const myElement = document.getElementById('myElement');
 * observeElementInView(myElement, (targetElement, originalElement) => {
 *   // Your code to handle the visibility of the element goes here
 *   console.log('Element is in view:', targetElement);
 *   console.log('Original element:', originalElement);
 * });
 */
var observeElementInView = function observeElementInView(element, onElementInView) {
  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        onElementInView(entry.target, element);
        observer.disconnect();
      }
    });
  }, {
    root: document,
    // Use the viewport as the root
    threshold: 0.5 // 50% of the element is in view
  });
  observer.observe(element);
};

/**
 * Parses a string of HTML and returns the resulting HTML element or document fragment.
 *
 * @function parseHTML
 * @param {string} val - The string containing the HTML to be parsed.
 * @param {number} [depth=0] - The depth of the node to retrieve from the parsed HTML. Default is 0.
 * @returns {HTMLElement | DocumentFragment} - The HTML element or document fragment parsed from the input string.
 *
 * @example
 * // Usage example:
 * const htmlString = '<div><p>Hello, <strong>world!</strong></p></div>';
 * const parsedElement = parseHTML(htmlString);
 * document.body.appendChild(parsedElement); // Append the parsed element to the document body
 */
var parseHTML = function parseHTML(val) {
  var depth = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  var parser = new DOMParser();
  var doc = parser.parseFromString(val, 'text/html');
  var node = doc.body;
  while (depth > 0) {
    depth--;
    // @ts-ignore
    node = node.firstChild;
  }
  if (node === null) {
    // @ts-ignore
    node = document.createDocumentFragment();
  }
  return node;
};

/**
 * Finds and returns the first matching element within the specified context.
 *
 * @function find
 * @param {Element | Document | string} eltOrSelector - The context within which to search for the element, or a CSS selector if no context is provided.
 * @param {string} [selector] - The CSS selector to match elements. If provided, the search is limited to the elements matching this selector.
 * @returns {Element | null} - The first matching element or null if no match is found.
 *
 * @example
 * // Usage example:
 * const myElement = find('#myId');
 * console.log(myElement); // The first element with the ID 'myId'
 */
var find = function find(eltOrSelector, selector) {
  if (selector) {
    if (typeof eltOrSelector === 'string') {
      var _document$querySelect, _document$querySelect2;
      return (_document$querySelect = (_document$querySelect2 = document.querySelector(eltOrSelector)) === null || _document$querySelect2 === void 0 ? void 0 : _document$querySelect2.querySelector(selector)) !== null && _document$querySelect !== void 0 ? _document$querySelect : null;
    } else {
      return eltOrSelector.querySelector(selector);
    }
  } else {
    return find(document, eltOrSelector);
  }
};

/**
 * Finds and returns all elements matching the specified selector within the specified context.
 *
 * @function findAll
 * @param {Document | Element | string} eltOrSelector - The context within which to search for the elements, or a CSS selector if no context is provided.
 * @param {string} [selector] - The CSS selector to match elements. If provided, the search is limited to the elements matching this selector.
 * @returns {NodeListOf<Element>} - A NodeList containing all matching elements.
 *
 * @example
 * // Usage example:
 * const allParagraphs = findAll('p');
 * console.log(allParagraphs); // NodeList of all <p> elements in the document
 */
var findAll = function findAll(eltOrSelector, selector) {
  if (selector) {
    if (typeof eltOrSelector === 'string') {
      return document.querySelectorAll(eltOrSelector);
    } else {
      return eltOrSelector.querySelectorAll(selector);
    }
  } else {
    return findAll(document, eltOrSelector);
  }
};

/**
 * Converts an array-like object to a real array.
 *
 * @param arrayLike - The array-like object to convert.
 * @returns An array containing the elements of the array-like object.
 * @template T - The type of elements in the array.
 * @example
 * const nodeList = document.querySelectorAll('.some-elements');
 * const elementArray = toArray(nodeList);
 */
var toArray = function toArray(arrayLike) {
  return Array.prototype.slice.call(arrayLike);
};
var each = function each(collection, callbackfn) {
  if (Array.isArray(collection)) {
    for (var i = 0; i < collection.length; i++) {
      callbackfn(collection[i], i);
    }
  } else {
    for (var _key in collection) {
      callbackfn(collection[_key], _key);
    }
  }
};
/**
 * Adds an event listener to one or more HTML elements.
 *
 * @param event - The type of event to listen for.
 * @param elementOrCollectionOfElement - The HTML element or an array of HTML elements to attach the event listener to.
 * @param callback - The callback function or event listener object to be executed when the event occurs.
 * @param options - An optional parameter to specify options for the event listener.
 *                  For example, `{ capture: true }`.
 *
 * @template K - The type parameter representing the valid event types.
 *
 * @remarks
 * If `elementOrCollectionOfElement` is an array, the event listener will be added to each element in the array.
 *
 * @see {@link https://developer.mozilla.org/en-US/docs/Web/Events|List of DOM events}
 *
 * @example
 * // Single element
 * const myButton = document.getElementById('myButton') as HTMLElement;
 * if (myButton) {
 *   on('click', myButton, (event) => {
 *     // Handle click event on a single element
 *   });
 * }
 *
 * // Multiple elements
 * const buttons = document.querySelectorAll('.button') as NodeListOf<HTMLElement>;
 * on('click', buttons, (event) => {
 *   // Handle click event on each element in the collection
 * });
 */
var on = function on(event, elementOrCollectionOfElement, callback, options) {
  if (!elementOrCollectionOfElement) return;
  if (Array.isArray(elementOrCollectionOfElement)) {
    each(elementOrCollectionOfElement, function (element) {
      return element.addEventListener(event, callback, options);
    });
    return;
  }
  elementOrCollectionOfElement.addEventListener(event, callback, options);
};

/**
 * Retrieves the value of a cookie by its name.
 *
 * @param name - The name of the cookie to retrieve.
 * @returns The value of the cookie if found, or `null` if the cookie is not present.
 *
 * @remarks
 * This function searches for the specified cookie name in the `document.cookie` string.
 *
 * @example
 * const username = getCookie('username');
 * if (username) {
 *   console.log(`Welcome back, ${username}!`);
 * } else {
 *   console.log('Cookie not found.');
 * }
 */
var getCookie = function getCookie(name) {
  var cookies = document.cookie.split(';');
  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i].trim();
    if (cookie.startsWith(name + '=')) {
      return cookie.substring(name.length + 1);
    }
  }
  return null;
};

/**
 * Sets a cookie with the specified name, value, and expiration days.
 *
 * @param name - The name of the cookie to set.
 * @param value - The value to assign to the cookie.
 * @param expirationDays - The number of days until the cookie expires.
 *
 * @remarks
 * This function sets a cookie in the browser with the provided name, value, and expiration days.
 *
 * @example
 * setCookie('username', 'JohnDoe', 7);
 * // Sets a cookie named 'username' with the value 'JohnDoe' that expires in 7 days.
 */
var setCookie = function setCookie(name, value, expirationDays) {
  var expires = new Date();
  expires.setTime(expires.getTime() + expirationDays * 24 * 60 * 60 * 1000);
  document.cookie = "".concat(name, "=").concat(value, ";expires=").concat(expires.toUTCString(), ";path=/");
};
/******/ 	return __webpack_exports__;
/******/ })()
;
});