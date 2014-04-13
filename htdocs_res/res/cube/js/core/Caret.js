define('core/Caret', [ 'core/jQuery'], function(require, exports, module) {

var $ = require('core/jQuery');

return {
	_gettingCaretPosition: false,
	getCaretPosition: function(element) {
		element = $(element).get(0);
		var tagName = element.tagName;
		if (tagName != 'TEXTAREA' && tagName != 'INPUT') {
			return {
				start: undefined,
				end: undefined
			};
		}
		if (! document.selection) {
			return {
				start: element.selectionStart,
				end: element.selectionEnd
			};
		}

		if (tagName == 'INPUT') {
			var range = document.selection.createRange();
			return {
				start: -range.moveStrt('character', -element.value.length),
				end: -range.moveEnd('character', -element.value.length)
			};
		} else {
			if (! this._gettingCaretPosition) {
				this._gettingCaretPosition = true;
				element.focus();
				this._gettingCaretPosition = false;
			}
			var range = document.selection.createRange();
			var duplicate = range.duplicate();
			duplicate.moveToElementText(element);
			duplicate.setEndPoint('StartToEnd', range);
			var end = element.value.length - duplicate.text.length;
			duplicate.setEndPoint('StartToStart', range);
			return {
				start: element.value.length - duplicate.text.length,
				end: end
			};
		}
	},

	setCaretPosition: function(element, start, end) {
		element = $(element).get(0);
		if (document.selection) {
			if (element.tagName == "TEXTAREA") {
				var newline = element.value.indexOf("\r", 0);
				while (newline != -1 && newline < end) {
					end--;
					if (newline < start) start--;
					newline = element.value.indexOf("\r", newline + 1);
				}
			}
			var range = element.createTextRange();
			range.collapse(true);
			range.moveStart('character', start);
			if (end != undefined) {
				range.moveEnd('character', end - start);
			}
			range.select();
		} else {
			element.selectionStart = start;
			end = end == undefined ? start : end;
			element.selectionEnd = Math.min(end, element.value.length);
			element.focus();
		}
	},

	'insertContent' : function( obj, content ) {
		obj = $( obj )[ 0 ];

		if ( $.browser.msie ) {
			obj.focus();
			document.selection.createRange().text = content;
		} else {
			var start = obj.selectionStart,
				value = obj.value,
				prePart = value.substring( 0, start ),
				nextPart = value.substring( start );
			
			obj.focus();
			obj.value = prePart + content + nextPart;
			this.setCaretPosition( obj, ( prePart + content ).length );
		}
	}
};

});
