/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 855:
/***/ (function() {

var _this = this;

var registerBlockType = wp.blocks.registerBlockType;
var _wp = wp,
    ServerSideRender = _wp.serverSideRender;
var _wp$blockEditor = wp.blockEditor,
    useBlockProps = _wp$blockEditor.useBlockProps,
    RichText = _wp$blockEditor.RichText,
    InspectorControls = _wp$blockEditor.InspectorControls,
    BlockControls = _wp$blockEditor.BlockControls,
    AlignmentToolbar = _wp$blockEditor.AlignmentToolbar,
    ColorPalette = _wp$blockEditor.ColorPalette,
    MediaUploadCheck = _wp$blockEditor.MediaUploadCheck,
    MediaUpload = _wp$blockEditor.MediaUpload;
var _wp$components = wp.components,
    Panel = _wp$components.Panel,
    PanelBody = _wp$components.PanelBody,
    PanelRow = _wp$components.PanelRow,
    Button = _wp$components.Button,
    TextControl = _wp$components.TextControl,
    IconButton = _wp$components.IconButton;
var Fragment = wp.element.Fragment;
registerBlockType('owc/mijn-zaken', {
  apiVersion: 2,
  title: 'Mijn Zaken',
  category: 'common',
  attributes: {
    zaaktypeFilter: {
      type: 'string',
      "default": '[]'
    },
    updateMePlease: {
      type: 'boolean',
      "default": true
    }
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
        setAttributes = _ref.setAttributes;
    var blockProps = useBlockProps();
    var zaaktypeFilter = attributes.zaaktypeFilter,
        updateMePlease = attributes.updateMePlease;
    var zaaktypeFilterArr = JSON.parse(zaaktypeFilter);

    var addZTFilter = function addZTFilter() {
      zaaktypeFilterArr.push('');
      setAttributes({
        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),
        updateMePlease: !updateMePlease
      });
    };

    var changeZTFilter = function changeZTFilter(ztUri, index) {
      zaaktypeFilterArr[index] = ztUri;
      setAttributes({
        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),
        updateMePlease: !updateMePlease
      });
    };

    var removeZTFilter = function removeZTFilter(index) {
      zaaktypeFilterArr.splice(index, 1);
      setAttributes({
        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),
        updateMePlease: !updateMePlease
      });
    };

    var zaaktypeFields = zaaktypeFilterArr.map(function (location, index) {
      return /*#__PURE__*/React.createElement(Fragment, {
        key: index
      }, /*#__PURE__*/React.createElement(TextControl, {
        className: "ogz-ztfilter_add",
        placeholder: "https://zaaksysteem.nl/catalogi/api/v1/zaaktypen/uri",
        value: zaaktypeFilterArr[index],
        onChange: function onChange(ztUri) {
          return changeZTFilter(ztUri, index);
        }
      }), /*#__PURE__*/React.createElement(IconButton, {
        className: "ogz-ztfilter_remove",
        icon: "no-alt",
        label: "Verwijder Zaaktype filter",
        onClick: function onClick() {
          return removeZTFilter(index);
        }
      }));
    });
    return /*#__PURE__*/React.createElement("div", blockProps, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(Panel, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: "Zaaktype configuratie",
      initialOpen: false
    }, /*#__PURE__*/React.createElement(PanelRow, null, "Zaaktypes"), zaaktypeFields, /*#__PURE__*/React.createElement(Button, {
      isDefault: true,
      icon: "plus",
      onClick: addZTFilter.bind(_this)
    }, "Voeg een Zaaktype URI toe")))), /*#__PURE__*/React.createElement("p", null, "Here be dragons"));
  },
  save: function save(_ref2) {
    var className = _ref2.className,
        attributes = _ref2.attributes;
    return /*#__PURE__*/React.createElement("section", {
      className: className
    });
  }
});

/***/ }),

/***/ 9:
/***/ (() => {

var registerBlockType = wp.blocks.registerBlockType;
var _wp = wp,
    ServerSideRender = _wp.serverSideRender;
registerBlockType("owc/gravityforms-zaaksysteem", {
  title: "Zaken",
  category: "theme",
  edit: function edit() {
    return /*#__PURE__*/React.createElement(ServerSideRender, {
      block: "owc/gravityforms-zaaksysteem"
    });
  },
  save: function save() {
    return function () {
      return null;
    };
  }
});

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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/* harmony import */ var _register_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(9);
/* harmony import */ var _register_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_register_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _mijn_zaken__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(855);
/* harmony import */ var _mijn_zaken__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_mijn_zaken__WEBPACK_IMPORTED_MODULE_1__);


})();

/******/ })()
;