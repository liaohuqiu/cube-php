define('core/Toggle', ['core/jQuery'], function(require) {
    
    var $ = require('core/jQuery');
    
    function Toggle(opts) {
        this.context = 'body';
        this.trigger = null;
        this.board = null;
        this.handler = $.noop;
        
        //trigger and board click handler
        this.triggerHandler = $.noop;
        this.boardHandler = $.noop;
        
        K.mix(this, opts);
        this.inner = false;
        this.init();
    }
    
    Toggle.prototype = {
        'init' : function() {
            
            // trigger
            if(typeof this.trigger === 'string') {
                $(this.context).delegate(this.trigger, 'click', $.proxy(function(ev) {
                	this.triggerHandler(ev);
                    this.inner = true;
                }, this));
            } else {
                $(this.trigger).click($.proxy(function(ev) {
                	this.triggerHandler(ev);
                    this.inner = true;
                }, this));
            }
            
            // board
            $(this.board).click($.proxy(function(ev) {this.inner = true; }, this));
            
            // body
            $(this.context).click($.proxy(function() {
                if(!this.inner) {
                    this.handler();
                } else {
                    this.inner = false;
                }
            }, this));
        }
    };
    
    return Toggle;
    
});
