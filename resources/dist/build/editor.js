/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 412:
/***/ (() => {

var registerBlockType = wp.blocks.registerBlockType;
var _wp = wp,
  ServerSideRender = _wp.serverSideRender;
var _wp$blockEditor = wp.blockEditor,
  useBlockProps = _wp$blockEditor.useBlockProps,
  InspectorControls = _wp$blockEditor.InspectorControls;
var _wp$components = wp.components,
  Panel = _wp$components.Panel,
  PanelBody = _wp$components.PanelBody,
  SelectControl = _wp$components.SelectControl,
  RangeControl = _wp$components.RangeControl;
var Fragment = wp.element.Fragment;
registerBlockType('owc/mijn-taken', {
  apiVersion: 2,
  title: 'Mijn Taken',
  category: 'common',
  attributes: {
    zaakClient: {
      type: 'string',
      "default": 'openzaak'
    },
    view: {
      type: 'string',
      "default": 'default'
    },
    numberOfItems: {
      type: 'number',
      "default": 2
    }
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes;
    var blockProps = useBlockProps();
    var zaakClient = attributes.zaakClient;
    return /*#__PURE__*/React.createElement("div", blockProps, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(Panel, null, /*#__PURE__*/React.createElement(PanelBody, {
      title: "Zaaksysteem",
      initialOpen: false
    }, /*#__PURE__*/React.createElement("p", null, "Selecteer het zaaksysteem waaruit de taken opgehaald moeten worden."), /*#__PURE__*/React.createElement(SelectControl, {
      label: "Zaaksysteem",
      value: zaakClient,
      options: [{
        label: 'OpenZaak',
        value: 'openzaak'
      }, {
        label: 'Decos JOIN',
        value: 'decos-join'
      }, {
        label: 'Rx.Mission',
        value: 'rx-mission'
      }, {
        label: 'xxllnc',
        value: 'xxllnc'
      }, {
        label: 'Procura',
        value: 'procura'
      }],
      onChange: function onChange(newzaakClient) {
        return setAttributes({
          zaakClient: newzaakClient
        });
      }
    })), attributes.view === 'current' && /*#__PURE__*/React.createElement(PanelBody, null, /*#__PURE__*/React.createElement(RangeControl, {
      min: 1,
      max: 20,
      label: "Aantal",
      help: "Het aantal taken dat getoond moeten worden.",
      value: attributes.numberOfItems,
      onChange: function onChange(value) {
        return setAttributes({
          numberOfItems: value
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: "Weergave",
      initialOpen: false
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: "Selecteer de weergave van de taken",
      value: attributes.view,
      options: [{
        label: 'Standaard',
        value: 'default'
      }, {
        label: 'Lopende Zaken',
        value: 'current'
      }],
      onChange: function onChange(newView) {
        return setAttributes({
          view: newView
        });
      }
    })))), attributes.view === 'default' ? /*#__PURE__*/React.createElement("p", null, "Standaardweergave") : /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("ul", {
      className: "zaak-tabs | nav nav-tabs",
      id: "zaak-tabs",
      role: "tablist"
    }, /*#__PURE__*/React.createElement("li", {
      className: "nav-item",
      role: "presentation"
    }, /*#__PURE__*/React.createElement("button", {
      className: "zaak-tabs-link | nav-link active"
    }, "Lopende taken")), /*#__PURE__*/React.createElement("li", {
      className: "nav-item",
      role: "presentation"
    }, /*#__PURE__*/React.createElement("button", {
      className: "zaak-tabs-link | nav-link"
    }, "Afgeronde taken"))), /*#__PURE__*/React.createElement("div", {
      className: "tab-content",
      id: "myTabContent"
    }, /*#__PURE__*/React.createElement("div", {
      className: "tab-pane fade show active"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-wrapper"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanvragen uittreksel BRP"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "12 december 2023"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-tag"
    }, "Dummy content"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanmelden straatfeest"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "15 oktober 2023"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanvraag rijbewijs"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "20 januari 2023"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanvragen leefbaarheidsbudget"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "11 januari 2023"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))))))));
  },
  save: function save(_ref2) {
    var className = _ref2.className;
    return /*#__PURE__*/React.createElement("section", {
      className: className
    });
  }
});

/***/ }),

/***/ 682:
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
  IconButton = _wp$components.IconButton,
  SelectControl = _wp$components.SelectControl,
  CheckboxControl = _wp$components.CheckboxControl,
  RangeControl = _wp$components.RangeControl;
var Fragment = wp.element.Fragment;
registerBlockType('owc/mijn-zaken', {
  apiVersion: 2,
  title: 'Mijn Zaken',
  category: 'common',
  attributes: {
    zaakClient: {
      type: 'string',
      "default": 'openzaak'
    },
    zaaktypeFilter: {
      type: 'string',
      "default": '[]'
    },
    updateMePlease: {
      type: 'boolean',
      "default": true
    },
    combinedClients: {
      type: 'boolean',
      "default": false
    },
    byBSN: {
      type: 'boolean',
      "default": true
    },
    view: {
      type: 'string',
      "default": 'default'
    },
    numberOfItems: {
      type: 'number',
      "default": 2
    },
    orderBy: {
      type: 'string',
      "default": 'startdatum'
    }
  },
  edit: function edit(_ref) {
    var attributes = _ref.attributes,
      setAttributes = _ref.setAttributes;
    var blockProps = useBlockProps();
    var zaakClient = attributes.zaakClient,
      zaaktypeFilter = attributes.zaaktypeFilter,
      updateMePlease = attributes.updateMePlease,
      combinedClients = attributes.combinedClients,
      byBSN = attributes.byBSN,
      orderBy = attributes.orderBy;
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
        placeholder: "B1026",
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
      title: "Zaaksysteem",
      initialOpen: false
    }, /*#__PURE__*/React.createElement("p", null, "Selecteer het zaaksysteem waaruit de zaken opgehaald moeten worden."), /*#__PURE__*/React.createElement(SelectControl, {
      label: "Zaaksysteem",
      value: zaakClient,
      options: [{
        label: 'OpenZaak',
        value: 'openzaak'
      }, {
        label: 'Decos JOIN',
        value: 'decos-join'
      }, {
        label: 'Rx.Mission',
        value: 'rx-mission'
      }, {
        label: 'xxllnc',
        value: 'xxllnc'
      }, {
        label: 'Procura',
        value: 'procura'
      }],
      onChange: function onChange(newzaakClient) {
        return setAttributes({
          zaakClient: newzaakClient
        });
      }
    }), /*#__PURE__*/React.createElement(CheckboxControl, {
      label: "Gecombineerde zaaksystemen",
      help: "Toon zaken uit gecombineerde zaaksystemen.",
      checked: combinedClients,
      onChange: function onChange(combinedClients) {
        return setAttributes({
          combinedClients: combinedClients
        });
      }
    }), /*#__PURE__*/React.createElement(CheckboxControl, {
      label: "Filter op BSN",
      help: "Filter zaken die aangemaakt zijn door de ingelogde gebruiker op basis van het BSN nummer.",
      checked: byBSN,
      onChange: function onChange(byBSN) {
        return setAttributes({
          byBSN: byBSN
        });
      }
    }), /*#__PURE__*/React.createElement(SelectControl, {
      label: "Sorteer op",
      value: orderBy,
      options: [{
        label: 'Startdatum',
        value: 'startdatum'
      }, {
        label: 'Einddatum',
        value: 'einddatum'
      }, {
        label: 'Publicatiedatum',
        value: 'publicatiedatum'
      }, {
        label: 'Archiefactiedatum',
        value: 'archiefactiedatum'
      }, {
        label: 'Registratiedatum',
        value: 'registratiedatum'
      }, {
        label: 'Identificatie',
        value: 'identificatie'
      }],
      onChange: function onChange(neworderBy) {
        return setAttributes({
          orderBy: neworderBy
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: "Zaaktype configuratie",
      initialOpen: false
    }, /*#__PURE__*/React.createElement(PanelRow, null, "Zaaktypes"), zaaktypeFields, /*#__PURE__*/React.createElement(Button, {
      isDefault: true,
      icon: "plus",
      onClick: addZTFilter.bind(_this)
    }, "Voeg een Zaaktype identificatie toe")), attributes.view === 'current' && /*#__PURE__*/React.createElement(PanelBody, null, /*#__PURE__*/React.createElement(RangeControl, {
      min: 1,
      max: 20,
      label: "Aantal",
      help: "Het aantal zaken dat getoond moeten worden.",
      value: attributes.numberOfItems,
      onChange: function onChange(value) {
        return setAttributes({
          numberOfItems: value
        });
      }
    })), /*#__PURE__*/React.createElement(PanelBody, {
      title: "Weergave",
      initialOpen: false
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: "Selecteer de weergave van de zaken",
      value: attributes.view,
      options: [{
        label: 'Standaard',
        value: 'default'
      }, {
        label: 'Tabbladen',
        value: 'tabs'
      }, {
        label: 'Lopende Zaken',
        value: 'current'
      }],
      onChange: function onChange(newView) {
        return setAttributes({
          view: newView
        });
      }
    })))), attributes.view === 'default' ? /*#__PURE__*/React.createElement("p", null, "Standaardweergave") : /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("ul", {
      className: "zaak-tabs | nav nav-tabs",
      id: "zaak-tabs",
      role: "tablist"
    }, /*#__PURE__*/React.createElement("li", {
      className: "nav-item",
      role: "presentation"
    }, /*#__PURE__*/React.createElement("button", {
      className: "zaak-tabs-link | nav-link active"
    }, "Lopende zaken")), /*#__PURE__*/React.createElement("li", {
      className: "nav-item",
      role: "presentation"
    }, /*#__PURE__*/React.createElement("button", {
      className: "zaak-tabs-link | nav-link"
    }, "Afgeronde zaken"))), /*#__PURE__*/React.createElement("div", {
      className: "tab-content",
      id: "myTabContent"
    }, /*#__PURE__*/React.createElement("div", {
      className: "tab-pane fade show active"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-wrapper"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanvragen uittreksel BRP"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "12 december 2023"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-tag"
    }, "Dummy content"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanmelden straatfeest"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "15 oktober 2023"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanvraag rijbewijs"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "20 januari 2023"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card"
    }, /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-svg",
      width: "385",
      height: "200",
      viewBox: "0 0 385 200",
      fill: "#F1F1F1",
      xmlns: "http://www.w3.org/2000/svg",
      preserveAspectRatio: "none"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z"
    })), /*#__PURE__*/React.createElement("h2", {
      className: "zaak-card-title"
    }, "Aanvragen leefbaarheidsbudget"), /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-footer"
    }, /*#__PURE__*/React.createElement("div", {
      className: "zaak-card-date"
    }, "11 januari 2023"), /*#__PURE__*/React.createElement("svg", {
      className: "zaak-card-arrow",
      width: "24",
      height: "24",
      viewBox: "0 0 24 24",
      xmlns: "http://www.w3.org/2000/svg"
    }, /*#__PURE__*/React.createElement("path", {
      d: "M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z"
    })))))))));
  },
  save: function save(_ref2) {
    var className = _ref2.className;
    return /*#__PURE__*/React.createElement("section", {
      className: className
    });
  }
});

/***/ }),

/***/ 788:
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
<<<<<<< HEAD
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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
=======
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
>>>>>>> 4659ae1 ((refactor): set mode to 'production' in webpack config)
(() => {
"use strict";
/* harmony import */ var _register_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(788);
/* harmony import */ var _register_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_register_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _mijn_zaken__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(682);
/* harmony import */ var _mijn_zaken__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_mijn_zaken__WEBPACK_IMPORTED_MODULE_1__);
<<<<<<< HEAD
=======
/* harmony import */ var _mijn_taken__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(412);
/* harmony import */ var _mijn_taken__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_mijn_taken__WEBPACK_IMPORTED_MODULE_2__);

>>>>>>> 4659ae1 ((refactor): set mode to 'production' in webpack config)


})();

/******/ })()
;
