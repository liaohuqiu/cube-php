define('core/InputListener', ['core/jQuery', 'core/Caret'], function(require) {
    
    var $ = require('core/jQuery'),
        Caret = require('core/Caret'),
        
        TPL = {
            'areaShadow' : '<div class="_j_areashadow" style="position:absolute;word-wrap:break-word;overflow-y:auto;overflow-x:hidden;visibility:hidden;background:#fff;display:none;z-index:-10;" />'
        };
    
    function InputListener(opts) {
        this.area = null;
        this.listenLetter = [];
        this.atMode = false; // atMode：像@那样监听(只支持一个listenLetter)，其他方式只监听当前光标前的一个字符是否命中
                
        K.mix(this, opts);
        this.area = $(this.area);
        this.listenLetter = [].concat(this.listenLetter);
        this.hit = false; // 当前是否处于命中状态
        this.selected = false; // 当前listener被认为选中（只起作用一次）
        this.keyString = '';
        
        K.CustEvent.createEvents(this, 'hit,unhit');
        
        this.init();
    }
    
    InputListener.prototype = {
        
        'init' : function() {
            if(this.area.length && this.listenLetter.length) {
                this.bindEvents();
            }
        },
        
        'bindEvents' : function() {
            var listenHandler = this.atMode ? $.proxy(this.atListen, this) : $.proxy(this.normalListen, this);
            this.area.bind('focus click keyup', listenHandler);
            this.area.listenInput(listenHandler);
        },
        
        'normalListen' : function(ev) {
            var inputVal = this.area.val(),
                caretStart = Caret.getCaretPosition(this.area).start,
                caretChar = inputVal.slice(caretStart - 1, caretStart);
            
            // 如果是focus时，将清除this.hit状态
            //if(ev.type === 'focus') {
            //    this.hit = false;
            //}
            
            for(var i=0, len=this.listenLetter.length; i<len; i++) {
                if(caretChar === this.listenLetter[i]) {
                    var charOffset = this.getCharOffset(caretStart, caretChar),
                        beforeCaretString = inputVal.slice(0, caretStart);
                    this.hitLetter({'letter' : caretChar, 'offset' : charOffset, 'position':caretStart - 1, 'caretStart':caretStart, 'follow':'', 'keyString' : beforeCaretString});
                    return;
                }
            }
            this.unHitLetter();
        },
        
        'atListen' : function(ev) {
            var atMatch = null, validAtString = null, targetAtPosition = null,
                inputVal = this.area.val(),
                caretStart = Caret.getCaretPosition(this.area).start,
                atLetter = this.listenLetter[0],
                atReg = new RegExp('\\'+atLetter+'([^\\'+atLetter+'\\s]*)$'),
                beforeCaretString = inputVal.slice(0, caretStart);
                
            // 如果是focus时，将清除this.hit状态
            if(ev.type === 'focus') {
                this.hit = false;
            }
            
            if(atMatch = beforeCaretString.match(atReg)) {
                targetAtPosition = beforeCaretString.lastIndexOf(atLetter);
                validAtString = atMatch[1];
            }

            if(validAtString !== null) {
                var charOffset = this.getCharOffset(targetAtPosition + 1, atLetter);
                this.hitLetter({'letter' : atLetter, 'offset' : charOffset, 'position':targetAtPosition, 'caretStart':caretStart, 'follow':validAtString, 'keyString' : beforeCaretString});
            } else {
                this.unHitLetter();
            }
            
        },
        
        'hitLetter' : function(data) {
            var keyString = data.keyString;
                
            if((keyString !== this.keyString || !this.hit) && !this.selected) {
                this.keyString = keyString;
                this.hit = true;
                //this.area.trigger('hitletter', data);
                this.fire('hit', {'data':data});
            } else {
                this.selected = false;
            }
        },
        
        'unHitLetter' : function() {
            if(this.hit) {
                this.hit = false;
                //this.area.trigger('unhitletter');
                this.fire('unhit');
            }
        },
        
        'getCharOffset' : function(charPosition, caretChar) {
            if(!this.areaShadow) {
                this.createAreaShadow(); // 为textarea创建一个shadow（可以定位元素位置的div，大小位置和area一模一样），
            }
            
            this.updateAreaShadow(charPosition, caretChar);
            
            var targetChar = this.areaShadow.children('._j_targetchar'),
                charOffset = targetChar.offset();

            return {
                'top' : charOffset.top + targetChar.height() - this.area.scrollTop(),
                'left' : charOffset.left
            }
        },
        
        'createAreaShadow' : function() {
            var offset = this.area.offset(),
                areaShadow = $(TPL.areaShadow);
            areaShadow.css({
                'display' : 'block',
                'border' : '1px solid #fff',
                'font-family' : this.area.css('font-family'),
                'font-size'   : this.area.css('font-size'),
                'line-height' : this.area.css('line-height'),
                'padding-left'  : this.area.css('padding-left'),
                'padding-top'   : this.area.css('padding-top'),
                'padding-right' : this.area.css('padding-right'),
                'padding-bottom': this.area.css('padding-bottom'),
                'left' : offset.left,
                'top' : offset.top ,
                'width' : this.area.width(),
                'height' : this.area.height()
            });
            areaShadow.appendTo('body');
            this.areaShadow = areaShadow;
        },
        
        'updateAreaShadow' : function(charPosition, caretChar) {
            var areaString = this.area.val(),
            	offset = this.area.offset(),
                beforeCaretString = areaString.slice(0, charPosition-1),
                afterCaretString = areaString.slice(charPosition);
                
			this.areaShadow.css({
				'left' : offset.left,
                'top' : offset.top
			});
			
            this.areaShadow.html('<span>' + this.htmlEntify(beforeCaretString) + '</span>'
                + '<span class="_j_targetchar">'+caretChar+'</span>' 
                + '<span>' + this.htmlEntify(afterCaretString) + '</span>');
        },
        
        'htmlEntify' : function(str) {
            return str.replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\n/g, '<br/>')
                    .replace(/(\s+)/g, '<span style="white-space:pre-wrap;">$1</span>');
        }
        
    };
    
    return InputListener;
    
});
