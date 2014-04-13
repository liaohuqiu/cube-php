define('core/List', ['core/jQuery', 'core/KeyListener'], function(require) {
    
    var $ = require('core/jQuery'),
        KeyListener = require('core/KeyListener'),
        mix = K.mix,
        idPrefix = 'list',
        genID = 0;
        
    function List(opts){
        
        // List id
        var id = idPrefix+'_'+(genID++);
        this.id = id;
        
        // list对应的头输入框，输入框作为list的事件触发器，负责触发list的所有事件
        this.trigger = null;
        
        this.container = null;
        
        this.itemSelector = '._j_listitem';
        
        this.itemHoverClass = 'on';
        
        // 当List列表项被选择或点击的时候出发事件
        K.CustEvent.createEvents(this, 'outputvalue');
        
        this.container = $(this.container);
        
        // 合并传入参数
        mix(this, opts);
        
        this.mouseon = false;
        
        this.visible = false;
        
        this.init();
    }
    
    List.prototype = {
        
        'init' : function() {
            if(!this.trigger) { return; }
            if(!this.container.length) {
                this.createContainer();
            }
            this.bindEvents();
        },
        
        'bindEvents' : function() {
            KeyListener.listen(this.trigger, 'keydown', 'enter', $.proxy(this.outputValue, this));
            KeyListener.listen(this.trigger, 'keydown', 'space', $.proxy(this.outputValue, this));
            KeyListener.listen(this.trigger, 'keydown', 'up', $.proxy(this.moveUp, this));
            KeyListener.listen(this.trigger, 'keydown', 'down', $.proxy(this.moveDown, this));
            this.container
                .delegate(this.itemSelector, 'mouseenter', $.proxy(this.mouseOverItem, this))
                .delegate(this.itemSelector, 'click', $.proxy(this.clickItem, this))
                .bind('mouseenter', $.proxy(this.mouseOverCnt, this))
                .bind('mouseleave', $.proxy(this.mouseOutCnt, this));
        },
        
        'show' : function(data) {
            this.updateList(data);
            this.container.show();
            this.visible = true;
        },
        
        'hide' : function() {
            this.container.hide();
            this.visible = false;
        },
        
        'outputValue' : function(ev) {
            if(this.visible) {
                this.fire('outputvalue', { 'data' : this.getValue() });
                ev && ev.preventDefault();
            }
        },
        
        'getValue' : $.noop,
        
        'createContainer' : $.noop,
        
        'updateList' : $.noop,
        
        'moveUp' : function(ev) { if(this.visible) {this.moveFocus(-1); ev.preventDefault(); } },

        'moveDown' : function(ev) { if(this.visible) {this.moveFocus(1); ev.preventDefault(); } },
        
        'mouseOverItem' : function(ev) { this.moveFocus(ev); },
        
        'mouseOverCnt' : function() { this.mouseon = true; },
        
        'mouseOutCnt' : function() { this.mouseon = false; },
        
        'clickItem' : function(ev) {
            ev.preventDefault();
            this.outputValue();
        },
        
        'moveFocus' : function(target) {
            var items = this.container.find(this.itemSelector),
                focusItem = items.filter('.' + this.itemHoverClass) || items.first(),
                nextItem = focusItem;
                
            if(target === -1) { // move up
                nextItem = focusItem.prev(this.itemSelector);
                if(!nextItem.length) { nextItem = items.last(); }
            } else if(target === 1) { // move down
                nextItem = focusItem.next(this.itemSelector);
                if(!nextItem.length) { nextItem = items.first(); }
            } else if(target.currentTarget) { // hover
                nextItem = $(target.currentTarget);
            }
            focusItem.removeClass(this.itemHoverClass);
            nextItem.addClass(this.itemHoverClass);
        }
    };
    
    return List;
});
