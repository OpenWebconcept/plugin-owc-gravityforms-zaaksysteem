/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./resources/js/editor/index.js":
/*!**************************************!*\
  !*** ./resources/js/editor/index.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _register_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./register-blocks */ \"./resources/js/editor/register-blocks.js\");\n/* harmony import */ var _register_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_register_blocks__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _mijn_zaken__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./mijn-zaken */ \"./resources/js/editor/mijn-zaken.js\");\n/* harmony import */ var _mijn_zaken__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_mijn_zaken__WEBPACK_IMPORTED_MODULE_1__);\n\n\n\n//# sourceURL=webpack://owc-gravityforms-zaaksysteem/./resources/js/editor/index.js?");

/***/ }),

/***/ "./resources/js/editor/mijn-zaken.js":
/*!*******************************************!*\
  !*** ./resources/js/editor/mijn-zaken.js ***!
  \*******************************************/
/***/ (function() {

eval("var _this = this;\n\nvar registerBlockType = wp.blocks.registerBlockType;\nvar _wp = wp,\n    ServerSideRender = _wp.serverSideRender;\nvar _wp$blockEditor = wp.blockEditor,\n    useBlockProps = _wp$blockEditor.useBlockProps,\n    RichText = _wp$blockEditor.RichText,\n    InspectorControls = _wp$blockEditor.InspectorControls,\n    BlockControls = _wp$blockEditor.BlockControls,\n    AlignmentToolbar = _wp$blockEditor.AlignmentToolbar,\n    ColorPalette = _wp$blockEditor.ColorPalette,\n    MediaUploadCheck = _wp$blockEditor.MediaUploadCheck,\n    MediaUpload = _wp$blockEditor.MediaUpload;\nvar _wp$components = wp.components,\n    Panel = _wp$components.Panel,\n    PanelBody = _wp$components.PanelBody,\n    PanelRow = _wp$components.PanelRow,\n    Button = _wp$components.Button,\n    TextControl = _wp$components.TextControl,\n    IconButton = _wp$components.IconButton,\n    SelectControl = _wp$components.SelectControl;\nvar Fragment = wp.element.Fragment;\nregisterBlockType('owc/mijn-zaken', {\n  apiVersion: 2,\n  title: 'Mijn Zaken',\n  category: 'common',\n  attributes: {\n    zaakClient: {\n      type: 'string',\n      \"default\": 'openzaak'\n    },\n    zaaktypeFilter: {\n      type: 'string',\n      \"default\": '[]'\n    },\n    updateMePlease: {\n      type: 'boolean',\n      \"default\": true\n    }\n  },\n  edit: function edit(_ref) {\n    var attributes = _ref.attributes,\n        setAttributes = _ref.setAttributes;\n    var blockProps = useBlockProps();\n    var zaakClient = attributes.zaakClient,\n        zaaktypeFilter = attributes.zaaktypeFilter,\n        updateMePlease = attributes.updateMePlease;\n    var zaaktypeFilterArr = JSON.parse(zaaktypeFilter);\n\n    var addZTFilter = function addZTFilter() {\n      zaaktypeFilterArr.push('');\n      setAttributes({\n        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),\n        updateMePlease: !updateMePlease\n      });\n    };\n\n    var changeZTFilter = function changeZTFilter(ztUri, index) {\n      zaaktypeFilterArr[index] = ztUri;\n      setAttributes({\n        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),\n        updateMePlease: !updateMePlease\n      });\n    };\n\n    var removeZTFilter = function removeZTFilter(index) {\n      zaaktypeFilterArr.splice(index, 1);\n      setAttributes({\n        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),\n        updateMePlease: !updateMePlease\n      });\n    };\n\n    var zaaktypeFields = zaaktypeFilterArr.map(function (location, index) {\n      return /*#__PURE__*/React.createElement(Fragment, {\n        key: index\n      }, /*#__PURE__*/React.createElement(TextControl, {\n        className: \"ogz-ztfilter_add\",\n        placeholder: \"https://zaaksysteem.nl/catalogi/api/v1/zaaktypen/uri\",\n        value: zaaktypeFilterArr[index],\n        onChange: function onChange(ztUri) {\n          return changeZTFilter(ztUri, index);\n        }\n      }), /*#__PURE__*/React.createElement(IconButton, {\n        className: \"ogz-ztfilter_remove\",\n        icon: \"no-alt\",\n        label: \"Verwijder Zaaktype filter\",\n        onClick: function onClick() {\n          return removeZTFilter(index);\n        }\n      }));\n    });\n    return /*#__PURE__*/React.createElement(\"div\", blockProps, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(Panel, null, /*#__PURE__*/React.createElement(PanelBody, {\n      title: \"Zaaksysteem\",\n      initialOpen: false\n    }, /*#__PURE__*/React.createElement(\"p\", null, \"Selecteer het zaaksysteem waaruit de zaken opgehaald moeten worden.\"), /*#__PURE__*/React.createElement(SelectControl, {\n      label: \"Zaaksysteem\",\n      value: zaakClient,\n      options: [{\n        label: 'OpenZaak',\n        value: 'openzaak'\n      }, {\n        label: 'Decos JOIN',\n        value: 'decosjoin'\n      }],\n      onChange: function onChange(newzaakClient) {\n        return setAttributes({\n          zaakClient: newzaakClient\n        });\n      }\n    })), /*#__PURE__*/React.createElement(PanelBody, {\n      title: \"Zaaktype configuratie\",\n      initialOpen: false\n    }, /*#__PURE__*/React.createElement(PanelRow, null, \"Zaaktypes\"), zaaktypeFields, /*#__PURE__*/React.createElement(Button, {\n      isDefault: true,\n      icon: \"plus\",\n      onClick: addZTFilter.bind(_this)\n    }, \"Voeg een Zaaktype URI toe\")))), /*#__PURE__*/React.createElement(\"p\", null, \"Here be dragons\"));\n  },\n  save: function save(_ref2) {\n    var className = _ref2.className,\n        attributes = _ref2.attributes;\n    return /*#__PURE__*/React.createElement(\"section\", {\n      className: className\n    });\n  }\n});\n\n//# sourceURL=webpack://owc-gravityforms-zaaksysteem/./resources/js/editor/mijn-zaken.js?");

/***/ }),

/***/ "./resources/js/editor/register-blocks.js":
/*!************************************************!*\
  !*** ./resources/js/editor/register-blocks.js ***!
  \************************************************/
/***/ (() => {

eval("var registerBlockType = wp.blocks.registerBlockType;\nvar _wp = wp,\n    ServerSideRender = _wp.serverSideRender;\nregisterBlockType(\"owc/gravityforms-zaaksysteem\", {\n  title: \"Zaken\",\n  category: \"theme\",\n  edit: function edit() {\n    return /*#__PURE__*/React.createElement(ServerSideRender, {\n      block: \"owc/gravityforms-zaaksysteem\"\n    });\n  },\n  save: function save() {\n    return function () {\n      return null;\n    };\n  }\n});\n\n//# sourceURL=webpack://owc-gravityforms-zaaksysteem/./resources/js/editor/register-blocks.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
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
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./resources/js/editor/index.js");
/******/ 	
/******/ })()
;