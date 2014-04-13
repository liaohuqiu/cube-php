define('core/ajax/Ajax', ['core/jQuery', 'core/ajax/Request'], function(require) {
	
	var $ = require('core/jQuery'),
		Request = require('core/ajax/Request');
		
	var Ajax = {};
	
	Ajax.get = function() {
		return Ajax._simple.apply(Ajax, ['get'].concat(Array.prototype.slice.call(arguments)));
	};
	
	Ajax.post = function() {
		return Ajax._simple.apply(Ajax, ['post'].concat(Array.prototype.slice.call(arguments)));
	};
	
	Ajax.ajax = function(opts) {
		return Ajax._send(opts);
	};
	
	Ajax._simple = function(method, url, data, success, error) {
		
		if(typeof data === 'function') {
			error = error || success;
			success = data;
			data = undefined;
		}
		
		return Ajax._send({
			'url' : url,
			'type' : method,
			'data' : data,
			'success' : success,
			'error' : error
		});
	};
	
	Ajax._send = function(opts) {
		var req = new Request(opts.url).setData(opts.data);
		
		opts.type && req.setMethod(opts.type);
		opts.dataType && req.setDataType(opts.dataType);
		
		typeof opts.success === 'function' && req.setHandler(opts.success);
		typeof opts.error === 'function' && req.setErrorHandler(opts.error);
		typeof opts.complete === 'function' && req.setFinallyHandler(opts.complete);
		
		return req.send();
	};
	
	return Ajax;
});