/**
 * 美化滚动条
 */
define('core/ScrollBar', ['core/jQuery'], function(require) {

    var $ = require('core/jQuery'),
        barMinLength = 15,
        scrollUnit = 30;

    /**
     * ScrollBar
     * @param {Object}  opts    初始化的参数
     *  {
     *      {string|object} wrap            包裹container，并放置滚动条的dom元素（selector/jquery 对象/dom对象）
     *      {string|object} container       需要滚动的dom对象（必填）
     *      {number}        dir             滚动方向（0：水平，1：垂直）
     *      {string}        barTPL          滚动条html代码
     *      {number}        maxHeight       container最大高度
     *  }
     */
    function ScrollBar(opts) {

        this.wrap = null;
        this.container = null;
        this.dir = 1;
        this.barTPL = '<div style="position:absolute; top:0; right:0; padding:1px"><div style="width:7px; height:100%; background:#bbb"></div></div>';
        this.maxHeight = 0;
        K.mix(this, opts);

        this.container = $(this.container);
        this.wrap = $(this.wrap);

        this.scrollBar = null;
        K.CustEvent.createEvents(this, 'contentchange,scrolltobottom');

        this.init();
    }

    ScrollBar.prototype = {

        'init' : function() {

        	if(!this.container.length) { // no container
        		K.log('ScrollBar init failed for no scroll container.');
        		return false;
        	}

            this.posDir = this.dir === 0 ? 'left' : 'top'; // 初始化css 方向
            this.lengthDir = this.dir === 0 ? 'width' : 'height'; // 初始化length 方向
            this.scrollDir = this.dir === 0 ? 'scrollLeft' : 'scrollTop'; // 初始化scroll 方向
            this.initWrap();
            this.createScrollBar();
            this.bindEvents();
            this.setted = false; // 初始化时并不立即设置滚动条，以免wrap是隐藏的不能有效计算dimension信息
        },

        /**
         * 设置整个wrap和container的css及html结构
         */
        'initWrap' : function() {
            this.container.css({'position':'relative', 'overflow':'hidden'});
            if(this.maxHeight && !isNaN(this.maxHeight)) {
                this.container.css('max-'+this.lengthDir, this.maxHeight).addClass('maxh');
            }
            if(!this.wrap.length) { // 如果没有wrap，则程序生成，用以放置scrollbar
                this.container.wrap('<div style="position:relative;height:100%"></div>');
                this.wrap = this.container.parent();
            }
            // this.wrap.css('padding-right')
        },

        /**
         * 创建滚动条（初始opacity为0， mouseenter时显示）
         */
        'createScrollBar' : function() {
            this.container[0][this.scrollDir] = 0;
            this.scrollBar = $(this.barTPL).css('opacity', 0).appendTo(this.wrap);
        },

        /**
         * 事件：进入滚动区域、离开滚动区域、hold scrollbar、contentchange
         */
        'bindEvents' : function() {
            this.wrap
                .bind('mouseenter', $.proxy(this.enterWrap, this))
                .bind('mouseleave', $.proxy(this.leaveWrap, this))
                .mousewheel($.proxy(this.rollWrap, this));
            this.scrollBar.mousedown($.proxy(this.holdBar, this));
            this.on('contentchange', $.proxy(this.checkShowScrollBar, this));
        },

        /**
         * 设置scrollbar
         */
        'setScrollBar' : function() {
            this.checkShowScrollBar();
        },

        /**
         * 获得wrap尺寸，为后来滚动高度提供计算依据
         */
        'setDimension' : function() {
            this.wrapStart = this.wrap.offset()[this.posDir];
        },

        /**
         * 检测是否需要显示滚动条，更新滚动属性
         */
        'checkShowScrollBar' : function() {
            var containerDOM = this.container[0];

            this.wrapLength = this.dir === 0 ? this.wrap.width() : this.wrap.height();
            this.scrollLength = this.dir === 0 ? containerDOM.scrollWidth : containerDOM.scrollHeight;
            this.clientLength = this.dir === 0 ? containerDOM.clientWidth : containerDOM.clientHeight;
            if(this.scrollLength > this.clientLength) {
                this.updateScrollBarLength();
                this.scrollBar.show();
                this.scroll(0);
            } else {
                this.scrollBar.hide();
            }
        },

        /**
         * 更新滚动条高度，按照overflow部分与显示部分的比例动态调整
         */
        'updateScrollBarLength' : function() {
            this.barLength = this.wrapLength * (this.clientLength/this.scrollLength);
            this.barLength = this.barLength < barMinLength ? barMinLength : this.barLength;
            this.scrollBar.css((this.dir === 0 ? 'width' : 'height'), this.barLength);
            this.barLength = this.dir === 0 ? this.scrollBar.outerWidth() : this.scrollBar.outerHeight();
        },

        'enterWrap' : function() {
            if(!this.setted) {
                this.setScrollBar();
                this.setted = true;
            }
            this.on = true;
            this.setDimension();
            this.scrollBar.stop(true, true).animate({'opacity' : 0.8}, 300);
        },

        'leaveWrap' : function() {
            this.on = false;
            if(!this.holded) {
                this.scrollBar.stop(true, true).animate({'opacity' : 0}, 300);
            }
        },

        /**
         * 鼠标滚轮事件处理
         */
        'rollWrap' : function(ev, dir) {
            var scrollLength = scrollUnit * (this.wrapLength - this.barLength)/this.scrollLength;
            scrollLength = scrollLength < 1 ? 1 : scrollLength;
            this.moveScrollBar(-dir * scrollLength);
            ev.preventDefault();
            ev.stopPropagation();
        },

        /**
         * 在scrollbar上按下鼠标
         */
        'holdBar' : function(ev) {
            this.holded = true;
            this.cursorPosition = this.dir === 0 ? ev.clientX : ev.clientY;
            this.startCursorOffset = this.cursorPosition - this.scrollBar.offset()[this.posDir];
            this.listenMouseMove();
            ev.preventDefault();
        },

        /**
         * 鼠标移动监听（hold scrollbar才会监听）
         */
        'listenMouseMove' : function() {
            $(document)
                .bind('mousemove', $.proxy(this.moveCursor, this))
                .bind('mouseup', $.proxy(this.releaseCursor, this));
        },

        /**
         * 取消鼠标移动监听（hold scrollbar release后触发）
         */
        'stopListenMouseMove' : function() {
            $(document)
                .unbind('mousemove', $.proxy(this.moveCursor, this))
                .unbind('mouseup', $.proxy(this.releaseCursor, this));
        },

        /**
         * hold scrollbar之后移动鼠标，响应移动scrollbar
         */
        'moveCursor' : function(ev) {
            if(this.holded) {
                var nowCursorPosition = this.dir === 0 ? ev.clientX : ev.clientY,
                    moveLength = nowCursorPosition - this.cursorPosition,

                    preStartOffset = this.cursorPosition - this.startCursorOffset - this.wrapStart,
                    realStartOffset = nowCursorPosition - this.startCursorOffset - this.wrapStart,
                    preEndOffset = this.cursorPosition + (this.barLength - this.startCursorOffset) - this.wrapStart - this.wrapLength,
                    realEndOffset = nowCursorPosition + (this.barLength - this.startCursorOffset) - this.wrapStart - this.wrapLength;

                if(realStartOffset > 0 && realEndOffset < 0 && moveLength !== 0) {
                    this.cursorPosition = nowCursorPosition;
                    this.moveScrollBar(moveLength);
                } else if(realStartOffset <= 0 && preStartOffset > 0 && moveLength !== 0) {
                    moveLength = -preStartOffset;
                    this.cursorPosition = this.cursorPosition + moveLength;
                    this.moveScrollBar(moveLength);
                } else if(realEndOffset >= 0 && preEndOffset < 0 && moveLength !== 0) {
                    moveLength = -preEndOffset;
                    this.cursorPosition = this.cursorPosition + moveLength;
                    this.moveScrollBar(moveLength);
                }
            }
            ev.preventDefault();
        },

        /**
         * 移动滚动条一段距离（相对于当前scrollbar位置所做的移动）
         */
        'moveScrollBar' : function(moveLength) {
            var preOffset = parseInt(this.scrollBar.css(this.posDir), 10),
                nextOffset = this.wrapLength - this.barLength - preOffset;

            if(moveLength < 0  && moveLength > -preOffset || moveLength > 0 && moveLength < nextOffset) {
                preOffset = preOffset + moveLength;
            } else if(moveLength < 0) {
                preOffset = 0;
            } else if(moveLength > 0) {
                preOffset = preOffset + nextOffset;
            }
            this.scrollBar.css(this.posDir, preOffset);
            this.container[0][this.scrollDir] = (preOffset/(this.wrapLength - this.barLength))*(this.scrollLength - this.clientLength);
        },

        /**
         * 传入scrollLength，移动滚动条到对应的滚动位置（绝对的移动）
         */
        'scroll' : function(scrollLength) {
            this.scrollBar.css(this.posDir, scrollLength * (this.wrapLength - this.barLength)/(this.scrollLength - this.clientLength));
            this.container[0][this.scrollDir] = scrollLength;
        },

        /**
         * 获取移动的绝对距离
         */
        'scrollLength' : function() {
            return this.container[0][this.scrollDir];
        },

        /**
         * 释放鼠标
         */
        'releaseCursor' : function() {
            this.holded = false;
            this.stopListenMouseMove();
            if(!this.on) {
                this.scrollBar.stop(true, true).animate({'opacity' : 0}, 300);
            }
        }
    };

    return ScrollBar;

});
