// JSON模块 
// https://raw.github.com/facebook/javelin/master/src/lib/JSON.js
define('core/JSON', [], function(require, exports, module) {

return {
	parse : function(data) {
		if (typeof data != 'string'){
			return null;
		}

		if (window.JSON && JSON.parse) {
			var obj;
			try {
				obj = JSON.parse(data);
			} catch (e) {}
			return obj || null;
		}

		return eval('(' + data + ')');
	},

	stringify : function(val) {
		if (window.JSON && JSON.stringify) {
			return JSON.stringify(val);
		}

		var out = [];
		if (
			val === null || val === true || val === false || typeof val == 'number'
		) {
			return '' + val;
		}

		if (val.push && val.pop) {
			var v;
			for (var ii = 0; ii < val.length; ii++) {

				// For consistency with JSON.stringify(), encode undefined array
				// indices as null.
				v = (typeof val[ii] == 'undefined')
					? null
					: val[ii];

				out.push(this.stringify(v));
			}
			return '[' + out.join(',') + ']';
		}

		if (typeof val == 'string') {
			return this._esc(val);
		}

		for (var k in val) {
			out.push(this._esc(k) + ':' + this.stringify(val[k]));
		}
		return '{' + out.join(',') + '}';
	},

	// Lifted more or less directly from Crockford's JSON2.
	_escexp : /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,

	_meta : {
	  '\b' : '\\b',
	  '\t' : '\\t',
	  '\n' : '\\n',
	  '\f' : '\\f',
	  '\r' : '\\r',
	  '"'  : '\\"',
	  '\\' : '\\\\'
	},

	_esc : function(str) {
	  this._escexp.lastIndex = 0;
	  return this._escexp.test(str) ?
		'"' + str.replace(this._escexp, this._replace) + '"' :
		'"' + str + '"';
	},

	_replace : function(m) {
		if (m in this._meta) {
			return this._meta[m];
		}
		return '\\u' + (('0000' + m.charCodeAt(0).toString(16)).slice(-4));
	}

};

});
