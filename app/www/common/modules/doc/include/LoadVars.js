/**
 * Object LoadVars
 * 	Flash MX / MX2004 / 8 LoadVars Object porting for JavaScript
 *      [ with all its methods and events ]
 *
 * @author               Andrea Giammarchi
 * @date                 2005/08/09
 * @lastmod              2006/05/07 09:00 [add support for KDE 3.5]
 * @version              1.1b stable - tested with IE 5.0, 5.5, 6.0, 7 beta2 and FireFox 1.0.6, 1.5.0.2, Opera 8, 8.5 and KDE 3.5
 * @documentation	 http://livedocs.macromedia.com/flash/8/main/00002323.html
 *			 NOTE:  documentation is the same of Flash 8 documentation. Only send method is different, because it
 *				doesn't open a new window, just send data to page (without events ... then for response use sendAndLoad).
 * 				getProgress is an unofficial method, maybe nice for your progress bars :-)
 */
function LoadVars() {

	/**
	 * Public method
         * 	add or modify headers
	 */
	this.addRequestHeader = function() {
		var	rqh = [];
		if(arguments.length == 1 && typeof(arguments[0]) !== "string") {
			for(var a = 0; a < arguments[0].length; a++)
				__push(rqh, arguments[0][a]);
		}
		else if(arguments.length == 2 && typeof(arguments[0]) === "string" && typeof(arguments[1]) === "string") {
			__push(rqh, arguments[0]);
			__push(rqh, arguments[1]);
		};
		if(rqh.length > 0 && (rqh.length % 2) == 0)
			__headers = rqh;
	};

	/**
	 * Public method
         * 	decode a string to internal values
	 */
	this.decode = function(queryString) {
		var	pos, key, value,
			response = queryString.split("&"),
			a = response.length;
		while(a) {
			if(response[--a] !== '') {
				pos = response[a].indexOf("=");
				key = response[a].substr(0, pos);
				value = response[a].substr((pos + 1), (response[a].length - pos));
				this[__decode(key)] = __decode(value);
			};
		};
	};

	/**
	 * Public method
         * 	get loaded bytes (fake value with IE browser)
	 */
	this.getBytesLoaded = function() {
		var	result = 0;
		if(__bridge.readyState > 2)
			result = __checkOnProgress() ? (__bridge.readyState === 3 ? 2 : 4) : __bridge.responseText.length;
		return result;
	};

	/**
	 * Public method
         * 	get total bytes (fake value with IE browser)
	 */
	this.getBytesTotal = function() {
		var	result = 0;
		if(__bridge.readyState === 3)
			result = __checkOnProgress() ? 4 : __bridge.getResponseHeader("Content-Length");
		return result;
	};

	/**
	 * Public unofficial method
         * 	get % progress while loading data (fake value with IE browser)
	 */
	this.getProgress = function() {
		return __progress;
	};

	/**
	 * Public method
         * 	load a server page (remember to use absolute uri when you try on your browser)
	 */
	this.load = function(url) {
		var 	self = this,
			result = this.loaded = false;
		if(__bridge !== null) {
			function __load() {
				__bridge.open("get", url, true);
				__addHeaders(self);
				__onProgress(self);
				__bridge.send(null);
			};
			setTimeout(__load, 0);
			result = true;
		};
		return result;
	};

	/**
	 * Public method
         * 	send something to the server
	 */
	this.send = function(url, target, method) {
		return this.sendAndLoad(url, null, method);
	};

	/**
	 * Public method
         * 	load a server page after sending something
	 */
	this.sendAndLoad = function(url, target, method) {
		var 	self = this,
			result = this.loaded = false,
			topage = "";
		if(__bridge !== null) {
			function __load() {
				topage = self.toString();
				if(typeof(method) === "string" && method.toLowerCase() === "get") {
					__bridge.open("get", url + "?" + topage, true);
					__addHeaders(self);
					if(__is_a(target, LoadVars))
						__onProgress(target);
					__bridge.send(null);
				}
				else {
					__bridge.open("post", url, true);
					__addHeaders(self);
					__bridge.setRequestHeader("Content-Type", self.contentType);
					__bridge.setRequestHeader("Content-Length", topage.length);
					if(__is_a(target, LoadVars))
						__onProgress(target);
					__bridge.send(topage);
				};
			};
			setTimeout(__load, 0);
			result = true;
		}
		return result;
	};

	/**
	 * Public method
         * 	return text rappresentation of this object
	 */
	this.toString = function() {
		var 	a = null,
			toserver = [];
		for(a in this) {
			if(!__in_array(a, __private))
				__push(toserver, __encode(a) + "=" + __encode(this[a]));
		};
		return toserver.join("&");
	};

	/** PUBLIC VARIABLES */

		// internal loaded boolean value
		this.loaded = false;

		// default contentType for POST interaction
		this.contentType = "application/x-www-form-urlencoded";



	/** LIST OF ALL PRIVATE METHODS [ uncommented ] */
	function __addHeaders(self) {
		var	a, b = __headers.length;
		__bridge.setRequestHeader("Connection", "Close");
		for(a = 0; a < b; a+=2) {
			if(__headers[a].toLowerCase() === "content-type")
				self.contentType = __headers[(a+1)];
			else
				__bridge.setRequestHeader(__headers[a], __headers[(a+1)]);
		};
	};
	function __checkOnProgress() {
		return (__ie || typeof(__bridge.responseText) !== "string");
	};
	function __decode(s) {
		return __decodeURIComponent(s);
	};
	function __decodeURIComponent(s) {
		if(__uce === null)
			__uce = (typeof(decodeURIComponent) === "function") ? true : false;
		return __uce ? decodeURIComponent(s) : unescape(s);
	};
	function __encode(v) {
		var 	tmp;
		switch(v.constructor)  {
			case Number:
			case Boolean:
			case String:
				tmp = __encodeURIComponent(v.toString());
				break;
			case Function:
				tmp = "[type Function]";
				break;
			default:
				tmp = "[object Object]";
				break;
		};
		return tmp;
	};
	function __encodeURIComponent(s) {
		if(__uce === null)
			__uce = (typeof(encodeURIComponent) === "function") ? true : false;
		return __uce ? encodeURIComponent(s) : escape(s);
	};
	function __in_array(elm, ar) {
		var 	found = 1,
			a = ar.length;
		while(a) {
			if(ar[--a] === elm)
				found = a = 0;
		};
		return !found;
	};
	function __is_a(obj, func) {
		return obj.constructor === func;
	};
	function __isIE() {
		var	browser = navigator.userAgent.toUpperCase();
		return (browser.indexOf("MSIE") >= 0 && browser.indexOf("OPERA") < 0);
	};
	function __getBridge() {
		var	XHR = null,
			browser = navigator.userAgent.toUpperCase();
		if(typeof(XMLHttpRequest) === "function" || typeof(XMLHttpRequest) === "object")
			XHR = new XMLHttpRequest();
		else if(window.ActiveXObject && browser.indexOf("MSIE 4") < 0) {
			if(browser.indexOf("MSIE 5") < 0)
				XHR = new ActiveXObject("Msxml2.XMLHTTP");
			else
				XHR = new ActiveXObject("Microsoft.XMLHTTP");
		};
		return XHR;
	};
	function __onProgress(self) {
		var	interval = 0;
		function checkProgress() {
			var	p;
			if(__ie)
				p = __m.floor(__progress + ((99 - __progress) * 0.02));
			else
				p = __m.floor((self.getBytesLoaded() / self.getBytesTotal()) * 100);
			__progress = p > 99 ? 99 : p;
		};
		__bridge.onreadystatechange = function() {
			if(__bridge.readyState === 3 && interval === 0)
				interval = setInterval(checkProgress, 50);
			else if(__bridge.readyState === 4) {
				clearInterval(interval);
				__progress = 100;
				if(typeof(self.onHTTPStatus) === "function")
					self.onHTTPStatus(__bridge.status);
				if(typeof(self.onData) === "function") {
					if(typeof(__bridge.responseText) === "string")
						self.onData(__bridge.responseText);
					else
						self.onData();
				};
				if(typeof(self.onLoad) === "function") {
					if(__bridge.status === 200) {
						self.decode(__bridge.responseText);
						self.loaded = true;
					}
					self.onLoad(self.loaded);

				};
			};
		};
		__progress = 0;
	};
	function __push(ar, value) {
		if(__ape === null)
			__ape = (typeof(ar.push) === "function") ? true : false;
		if(__ape)
			ar.push(value);
		else
			ar[ar.length] = value;
	};

	/** LIST OF ALL PRIVATE VARIABLES [ uncommented ] */
	var
		__progress = 0,
		__uce = __ape = null,
		__ie = __isIE(),
		__headers = [],
		__private = [
			"loaded", "contentType",
			"addRequestHeader",
			"decode",
			"getBytesLoaded", "getBytesTotal", "getProgress",
			"load", "send", "sendAndLoad",
			"toString"
		],
		__m = Math,
		__bridge = __getBridge();
};
