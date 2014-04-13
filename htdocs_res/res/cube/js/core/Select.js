/**
 * 美化Select
 */
define('core/Select', ['core/jQuery', 'core/KeyListener', 'core/Toggle'], function(require) {
    
    var $ = require('core/jQuery'),
        KeyListener = require('core/KeyListener'),
        Toggle = require('core/Toggle'),
        
        TPL = {
            'hiddeninput' : '<input type="hidden" name="{{=it.name}}" value="{{=it.value}}" />'
        };
    
    function Select(opts) {
        
        this.container = null; // 没有实际的意义，可以不传该参数，单纯为了意义上的完整性
        this.valueBox = null; // 用来显示选中的值的dom
        this.dropTrigger = null; // 下拉按钮（必须是链接，或是能获得焦点的dom节点）
        this.dropListCnt = null; // 下拉框
        this.optionSelector = '._j_option'; // 每个可选项的selector
        this.selectedClass = '_j_selected'; // 可选项选中时的class
        this.disabledClass = 'disabled';
        
        K.mix(this, opts);
        
        this.container = $(this.container);
        this.valueBox = $(this.valueBox);
        this.dropTrigger = $(this.dropTrigger);
        this.dropListCnt = $(this.dropListCnt); // 需要控制显隐的下拉框
        
        // 内部列表项滚动条所在的container，一般情况下是dropListCnt（select框需要根据用户当前选中的项目，设置scrolltop，以免选中的item不在视线内）
        this.dropListInnerCnt = this.dropListInnerCnt ? $(this.dropListInnerCnt) : this.dropListCnt;
        
        // 保存真实数据input，用以实现表单提交（前提是valueBox拥有name属性）
        this.value = this.valueBox.data('value') || '';
        this.input = $(doT.template(TPL.hiddeninput)({'name':this.valueBox.attr('name'), 'value':this.value})).insertAfter(this.valueBox);
        
        // 如果是默认提示语，那么添加class default
        if(!this.value) {
            this.valueBox.addClass('default');
        } else {
            this.valueBox.removeClass('default');
        }
        
        K.CustEvent.createEvents(this, 'change,show,hide');

        this.init();
    }
    
    Select.prototype = {
        
        'init' : function() {
            this.setFocusItem();
            this.bindEvents();
        },
        
        /**
         * 设置select选中的option
         */
        'setFocusItem' : function() {
            var items = this.dropListCnt.find(this.optionSelector),
                focusItem = items.filter('[data-value="' + this.value + '"]');
            if(!focusItem.length) {
                focusItem = items.first();
            }
            if(!focusItem.hasClass(this.selectedClass)) {
                focusItem.siblings().removeClass(this.selectedClass);
                focusItem.addClass(this.selectedClass);
                this.focusItem = focusItem;
            }
        },
        
        'bindEvents' : function() {
            var _this = this;
            this.dropTrigger.click($.proxy(this.dropClick, this));
            KeyListener.listen(this.dropTrigger, 'keydown', 'up', function(ev) {
                if(!$(ev.currentTarget).hasClass(this.disabledClass)) {
                    _this.moveSelect(-1); 
                }
                ev.preventDefault();
            });
            KeyListener.listen(this.dropTrigger, 'keydown', 'down', function(ev) {
                if(!$(ev.currentTarget).hasClass(this.disabledClass)) {
                    _this.moveSelect(1);
                }
                ev.preventDefault();
            });
            KeyListener.listen(this.dropTrigger, 'keydown', 'enter', function(ev) {
                if(!$(ev.currentTarget).hasClass(this.disabledClass)) {
                    _this.selectItem(_this.focusItem);
                }
                ev.preventDefault();
            });
            this.dropListCnt
                .delegate(this.optionSelector, 'mouseenter', $.proxy(this.hoverItem, this))
                .delegate(this.optionSelector, 'click', function(ev) {
                    _this.selectItem($(ev.currentTarget));
                    ev.preventDefault();
                });
                
            new Toggle({
                'trigger' : this.dropTrigger,
                'board' : this.dropListCnt,
                'handler' : function() {
                    this.board.hide();
                    _this.fire('hide');
                }
            });
        },

        'dropClick' : function(ev) {
            if(!$(ev.currentTarget).hasClass(this.disabledClass)) {
                this.dropTrigger.focus();
                this.drop();
            }
            ev.preventDefault();
        },
        
        'disable' : function() {
        	this.dropTrigger.addClass(this.disabledClass);
        },
        
        'enable' : function() {
        	this.dropTrigger.removeClass(this.disabledClass);
        },
        
        /**
         * 点击或是keydown enter来选择一个option
         */
        'selectItem' : function(target) {
            this.setSelect(target);
            this.dropListCnt.hide();
            this.dropTrigger.focus();
            this.valueBox.removeClass('default');
        },
        
        /**
         * key up down来移动选中的option
         */
        'moveSelect' : function(target) {
            var items = this.dropListCnt.find(this.optionSelector),
                focusItem = this.focusItem || items.first(),
                nextItem = focusItem;
                
            if(target === -1) { // move up
                nextItem = focusItem.prev(this.optionSelector);
                if(!nextItem.length) { nextItem = items.last(); }
            } else if(target === 1) { // move down
                nextItem = focusItem.next(this.optionSelector);
                if(!nextItem.length) { nextItem = items.first(); }
            }
            this.setSelect(nextItem);
        },
        
        /**
         * 设置选中的option为target
         */
        'setSelect' : function(target) {
            var value = target.data('value');
            this.focusItem && this.focusItem.removeClass(this.selectedClass);
            target.addClass(this.selectedClass);
            this.focusItem = target;
            this.value = value; // 更新对象value
            this.input.val(value); // 更新隐藏input value
            this.valueBox.text(target.data('text'));
            this.valueBox.data('value', value); // 更新显示
            this.fire('change', {'data' : {'value' : value}}); // 释放改变事件
            this.viewFocusItem();
        },
        
        /**
         * mouseover某个option（需求不尽相同，如不需要特殊处理）
         */
        'hoverItem' : function(ev) {
            var target = $(ev.currentTarget);
            this.focusItem.removeClass(this.selectedClass);
            target.addClass(this.selectedClass);
            this.focusItem = target;
        },
        
        /**
         * 下拉收起动作
         */
        'drop' : function() {
            if(this.dropListCnt.css('display') === 'none') {
                this.dropListCnt.show();
                this.setFocusItem();
                this.viewFocusItem();
                this.fire('show');
            } else {
                this.dropListCnt.hide();
                this.fire('hide');
            }
        },
        
        /**
         * 当选中的item位于窗口滚动条之下的时候，需要将滚动条下拉，用以显示该item
         */
        'viewFocusItem' : function() {
            
            if(!this.focusItem.length) { return; }
            
            var dropListInnerCnt = this.dropListInnerCnt[0],
                scrollTop = dropListInnerCnt.scrollTop,
                viewPortHeight = dropListInnerCnt.clientHeight,
                focusTop = this.focusItem[0].offsetTop,
                focusBottom = focusTop + this.focusItem.height();
            
            if(focusTop < scrollTop) {
                this.scroll(focusTop);
            } else if(focusBottom > (scrollTop + viewPortHeight)) {
                this.scroll(focusBottom - viewPortHeight);
            }
        },
        
        /**
         * 滚动下拉框滚动条用以将焦点项放到视口
         */
        'scroll' : function(length) {
            this.dropListInnerCnt.scrollTop(length);
        }
    };
    
    return Select;
});
