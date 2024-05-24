(function (d, w) {
	'use strict';

	// Do not change! It will be replaced with regexp.
	var DOMAIN = 'flamp.ru';

	var DOMAIN_REGEXP = new RegExp(DOMAIN + '/');

	/**
	 * Flamp widget loader
	 * @constructor
	 */
	function FlampWidgetLoader() {}

	/**
	 * flamp widget params
	 * @type {{
	 * 	widgetsOrigin: string,
	 * 	widgetsSrc: string,
	 * 	widgetAttrSelector: string,
	 * 	widgetAttr: string
	 * 	}}
	 * @private
	 */
	FlampWidgetLoader.prototype._params = {
		widgetsOrigin: '//widget.' + DOMAIN,
		widgetsSrc: '/',
		widgetAttrSelector: 'a.flamp-widget',
		widgetAttr: 'data-flamp-widget-',
		widgetClass: 'flamp-widget'
	};

	/**
	 * Size of widgets
	 * @type {{micro: Array, small: Array, medium: Array, big: Array}}
	 * @private
	 */
	FlampWidgetLoader.prototype._size = {
		micro: [31, 31],
		'micro-new': [40, 40],
		small: [88, 31],
		'small-new': [104, 40],
		medium: [148, 81],
		'medium-new': [168, 96],
		big: [[228, 456, 640], [256, 412, 550]]
	};

	/**
	 * iframe for flamp widget
	 * @type {HTMLElement|null}
	 * @private
	 */
	FlampWidgetLoader.prototype._iframe = null;

	/**
	 * Flamp widget params
	 * @type {{id: Number|null, width: string, height: string}}
	 * @private
	 */
	FlampWidgetLoader.prototype._widget = null;

	/**
	 * Create iframe
	 * @returns {HTMLElement}
	 * @private
	 */
	FlampWidgetLoader.prototype._createIFrame = function (params) {
		params = params || {};
		var size = this._solveWidgetSize(params),
			iframe = d.createElement('iframe');
		if (size._width) {
			size._width += 'px';
		}
		if (size._height) {
			size._height += 'px';
		}

		var width = params.width || size._width || '100%';
		if (width === '100%') {
			iframe.setAttribute('scrolling', 'no');
			iframe.style.width = '1px';
			iframe.style.minWidth = '100%';
		} else {
			iframe.setAttribute('scrolling', (params.height)? 'auto' : 'no');
			iframe.style.width = width;
		}

		iframe.setAttribute('frameborder', '0');
		iframe.setAttribute('allowtransparency', 'true');
		iframe.style.height = params.height || size._height || '100%';
		iframe.style.border = 'none';
		iframe.src = this._params.widgetsOrigin + this._params.widgetsSrc +
			'?' + stringify(params);

		return iframe;
	};

	/**
	 * Get size of widget
	 * @param {Object} params
	 * @returns {Object}
	 * @private
	 */
	FlampWidgetLoader.prototype._solveWidgetSize = function (params) {
		var size = {},
			settings = this._size[params.type];
		if (!settings || settings.length !== 2) {
			return size;
		}

		if (params.type !== 'big') {
			size._width = settings[0];
			size._height = settings[1];
			return size;
		}

		if (params.count < 1 || params.count > 3) {
			params.count = 1;
		}

		if (params.orientation === 'landscape') {
			size._width = settings[0][params.count - 1];
			size._height = settings[1][0];
		} else {
			size._width = settings[0][0];
			size._height = settings[1][params.count - 1];
		}
		return size;
	};

	/**
	 * Resize iframe
	 * @param {Object} params
	 * @private
	 */
	FlampWidgetLoader.prototype._resizeIFrame = function (params) {
		if (!params ||
			!params.hasOwnProperty('width') ||
			!params.hasOwnProperty('height')) {
			return;
		}

		if (this._widget.width === null) {
			this._iframe.style.width = params.width + 'px';
		}
		if (this._widget.height === null) {
			this._iframe.style.height = params.height + 'px';
		}
	};

	/**
	 * Init flamp widget
	 */
	/*jshint maxcomplexity:false */
	FlampWidgetLoader.prototype.initWidget = function () {
		var el;
		if (d.querySelector) {
			el = d.querySelector(this._params.widgetAttrSelector);
		}
		if (!el) {
			throw new Error('Element not found');
		}

		if (!(DOMAIN_REGEXP.test(el.getAttribute('href')))) {
			el.parentNode.removeChild(el);
			throw new Error('Wrong href value');
		}

		this._widget = {
			id: null,
			width: null,
			height: null
		};

		var id = el.getAttribute(this._params.widgetAttr + 'id');
		console.log(id);
		if (!(/[0-9]+/.test(id))) {
			throw new Error('Wrong filial ID');
		}

		this._widget.id = id;

		var width = el.getAttribute(this._params.widgetAttr + 'width');
		if (width && width !== 'auto') {
			this._widget.width = width;
		}

		var height = el.getAttribute(this._params.widgetAttr + 'height');
		if (height && height !== 'auto') {
			this._widget.height = height;
		}

		this._widget.type =
			el.getAttribute(this._params.widgetAttr + 'type') || null;
		this._widget.color =
			el.getAttribute(this._params.widgetAttr + 'color') || null;
		this._widget.textColor =
			el.getAttribute(this._params.widgetAttr + 'text-color') || null;
		this._widget.count =
			el.getAttribute(this._params.widgetAttr + 'count') || null;
		this._widget.orientation =
			el.getAttribute(this._params.widgetAttr + 'orientation') ||
			'landscape';

		this._iframe = this._createIFrame(this._widget);

		if (!this._iframe) {
			throw new Error('Can\'t create iframe');
		}

		el.parentNode.appendChild(this._iframe);
		el.parentNode.removeChild(el);
	};

	/**
	 * Bind events
	 */
	FlampWidgetLoader.prototype.bindEvents = function () {
		if (!window.postMessage) {
			this._iframe.setAttribute('scrolling', 'auto');
		}

		var widgetsOrigin = this._params.widgetsOrigin,
			self = this;

		var addEvent =  w.attachEvent || w.addEventListener,
			eventName = w.attachEvent ? 'onmessage' : 'message';

		addEvent(eventName, function (event) {
			try {
				if (event.source !== self._iframe.contentWindow) {
					return;
				}

				if (event.origin !== 'http:' + widgetsOrigin &&
					event.origin !== 'https:' + widgetsOrigin) {
					return;
				}

				var data = parse(event.data);
				

				if (data && data.status === 'rendered') {
					self._resizeIFrame(data);
				}
			} catch (e) {
				// nothing to do
			}
		}, false);
	};

	function stringify (message) {
		var value = String(message);

		if (typeof message === 'object') {
			value = '';
			for (var i in message) {
				if (!message.hasOwnProperty(i)) {
					continue;
				}
				if (message[i] === null) {
					continue;
				}
				if (i === 'width' || i === 'height') {
					continue;
				}
				value += i + '=' + message[i] + '&';
			}
		}
		return value;
	}

	function parse (message) {
		var params = String(message).split('&'),
			value,
			data = {};
		for (var i = 0; i < params.length; i++) {
			value = params[i].split('=');
			if (!value || value.length !== 2) {
				continue;
			}
			data[value[0]] = value[1];
		}
		return data;
	}

	try {
		var loader = new FlampWidgetLoader();
		loader.initWidget();
		loader.bindEvents();
	} catch (e) {
		// nothing to do
	}
})(document, window);