/*
    Copyright (c) 2011, kaixin001 Inc. All rights reserved.
    http://www.kaixin001.com
    author: minliang@corp.kaixin001.com
*/

/**
 * @class Scroll Scroll功能封装
 */
define('core/switch/Scroll',['core/jQuery'],function( require ){
    var $ = require('core/jQuery'),
        mix = K.mix,
        CustEvent = K.CustEvent;
    /**
     * options中的配置如下
            {Element}           prev        “前一个按钮”
            {Element}           next        “后一个按钮”
            {Array|String}      contents    滚动的内容
            {Element}           host        内容所在的容器
            {Scroll.Behavior}   behavior    具体进行Scroll切换的策略类(默认为ShowHide)
            {Array}             events      触发切换的事件类型(默认为click)
            {Boolean}           vertical    是否垂直滚动(默认为水平)
     * @constructor
     */
    function Scroll( options ){
        /**
         * @property contents 具体Scroll的内容
         */
        this.contents = [];

        /**
         * @property host 内容所在的容器
         */
        this.host = null;

        /**
         * @property prev “前一个按钮”
         */
        this.prev = null;

        /**
         * @property next “后一个按钮”
         */
        this.next = null;

        /**
         * @property vertical 是否垂直滚动
         */
        this.vertical = false;

        /**
         * @property index 当前所处位置
         */
        this.index = 0;

        /**
         * @property events 触发切换的事件
         * @type    {array}
         */
        this.events = ['click'];

        this.PREV_DISABLED_CLASS = 'prev-btn-disabled';

        this.NEXT_DISABLED_CLASS = 'next-btn-disabled';

        this.inSwitching = false;

        this.init( options );
    }
    mix(Scroll.prototype,{
        init: function( options ){
            /**
             * @event beforeswitch  切换前
             * @param   {CustEvent} e               事件实例<br>
                                    e.from          切换前元素<br>
                                    e.to            切换后元素
             */

            /**
             * @event afterswitch   切换后
             * @param   {CustEvent} e               事件实例<br>
                                    e.from          切换前元素<br>
                                    e.to            切换后元素
             */

            /**
             * @event arrivefirst   到达头部
             * @param   {CustEvent} e               事件实例<br>
                                    e.from          切换前元素<br>
                                    e.to            切换后元素
             */
            /**
             * @event arrivelast    到达尾部
             * @param   {CustEvent} e               事件实例<br>
                                    e.from          切换前元素<br>
                                    e.to            切换后元素
             */
            CustEvent.createEvents( this, 'beforeswitch,afterswitch,arrivefirst,arrivelast' );

            mix( this, options, true );

            if( !this.behavior ){
                this.behavior = Scroll.Behavior.ShowHide;
            }

            this.prev = $(this.prev);
            this.next = $(this.next);
            this.host = $(this.host);

            if( K.isString( this.contents ) ){
                this.contents = $( this.contents );
            }

            this._setBtn();

            this._addEvents();
        },
        _addEvents: function(){
            var evts = this.events;
            if( !evts ) return;
            var ins = this;
            K.forEach( evts, function( evt, idx ){
                ins.prev.bind( evt, $.proxy( ins._prevTriggerHandler, ins ) );
                ins.next.bind( evt, $.proxy( ins._nextTriggerHandler, ins ) );
            });
        },
        _prevTriggerHandler: function( e ){
            e.preventDefault();
            this.toPrev();
        },
        _nextTriggerHandler: function( e ){
            e.preventDefault();
            this.toNext();
        },

        /**
         * @method  to 切换到
         * @param   {int}       index       下标
         * @return  {void}
         */
        to : function ( index ) {
            if (!(this.contents.length && index < this.contents.length && index >= 0 && !this.inSwitching)) return;

            this.dispatchBeforeSwitch( this.index, index );

            var prev = this.index;
            this.index = index;

            this.inSwitching = true;
            //真正的切换行为交给具体的策略类来处理
            this.behavior.trigger({
                from: prev,
                to: index,
                context: this
            });

            this.dispatchAfterSwitch( prev, index );

            this._setBtn();

            if( this.index == 0 ){
                this.dispatchArriveFirst( prev, index );
            }
            else if( this.index == (this.contents.length - 1) ){
                this.dispatchArriveLast( prev, index );
            }

        },
        toPrev: function(){
            this.to( this.index - 1 );
        },
        toNext: function(){
            this.to( this.index + 1 );
        },
        _setBtn: function(){
            if( this.index == 0 ){
                this.prev.addClass( this.PREV_DISABLED_CLASS );
            }
            else if( this.index == (this.contents.length - 1) ){
                this.next.addClass( this.NEXT_DISABLED_CLASS );
            }

            if( this.prev.hasClass( this.PREV_DISABLED_CLASS ) && ( this.index > 0 ) ){
                this.prev.removeClass( this.PREV_DISABLED_CLASS );
            }
            if( this.next.hasClass( this.NEXT_DISABLED_CLASS ) && (this.index < (this.contents.length - 1)) ){
                this.next.removeClass( this.NEXT_DISABLED_CLASS );
            }
        },
        /**
         * @method item 根据下表找到元素
         * @param   {int}   index   下标
         * @return  {item}
         */
        item : function (index) {
            if (this.contents.length == 0 || index < 0) return null;
            return this.contents.eq(index % this.contents.length) || null;
        },
        /**
         * @method _dispatch 派发事件
         * @private
         * @param   {string}    type    事件名
         * @param   {int}       from    上次选中的下标
         * @param   {int}       to      当前下标
         * @return  {bool}  事件执行结果
         */
        _dispatch : function (type, from, to) {
            var _e = new CustEvent(this, type);

            mix(_e, {
                from : this.item(from),
                to : this.item(to),
                fromIndex: from,
                toIndex: to
            });

            return this.fire(_e);
        },

        /**
         * @method dispatchBeforeSwitch 派发切换前事件
         * @param   {int}   from        上次选中的下标
         * @param   {int}   to          当前下标
         * @return  {bool}  事件执行结果
         */
        dispatchBeforeSwitch : function (from, to) {
            return this._dispatch('beforeswitch', from, to);
        },

        /**
         * @method dispatchAfterSwitch 派发切换后事件
         * @param   {int}   from        上次选中的下标
         * @param   {int}   to          当前下标
         * @return  {bool}  事件执行结果
         */
        dispatchAfterSwitch : function (from, to) {
            return this._dispatch('afterswitch', from, to);
        },
        dispatchArriveFirst : function (from, to) {
            return this._dispatch('arrivefirst', from, to);
        },
        dispatchArriveLast : function (from, to) {
            return this._dispatch('arrivelast', from, to);
        }
    });

    /*Scroll切换效果的策略类，如果需要扩展切换效果则在策略中增加新的类即可*/
    Scroll.Behavior = {};

    var ShowHide = {
        /*
         *  @param {Object} data 数据
            {
                from:       prev,
                to:         index,
                context:    this,
                type:       evtType
            }
        */
        trigger: function( data ){
            var from        = data.from,
                to          = data.to,
                ctx         = data.context,
                host        = ctx.host,
                prevItem    = ctx.item( from ),
                width       = prevItem.width(),
                height      = prevItem.height(),
                disIndex    = to - from,
                disWidth    = disIndex * width,
                disHeight   = disIndex * height;
            if( !ctx.vertical ){
                var left = parseInt(host.css('left'));
                if( isNaN( left ) ) left = 0;

                host.css( {'left' : left - disWidth} );
            }
            else{
                var top = parseInt(host.css('top'));
                if( isNaN( top ) ) top = 0;
                host.css( {'top' : top - disHeight} );
            }

            ctx.inSwitching = false;
        }
    };

    Scroll.Behavior.ShowHide = ShowHide;

    var FadeInOut = {
        /*
         *  @param {Object} data 数据
            {
                from:       prev,
                to:         index,
                context:    this,
                type:       evtType
            }
        */
        trigger: function( data ){
            var from        = data.from,
                to          = data.to,
                ctx         = data.context,
                host        = ctx.host,
                prevItem    = ctx.item( from ),
                width       = prevItem.width(),
                height      = prevItem.height(),
                disIndex    = to - from,
                disWidth    = disIndex * width,
                disHeight   = disIndex * height;

            host.css( {'left' :0, 'top': 0} );
            ctx.contents.hide();
            ctx.item( to ).fadeIn();
            ctx.inSwitching = false;
        }
    };

    Scroll.Behavior.FadeInOut = FadeInOut;

    var BScroll = {
        /*
         *  @param {Object} data 数据
            {
                from:       prev,
                to:         index,
                context:    this,
                type:       evtType
            }
        */
        trigger: function( data ){
            var from        = data.from,
                to          = data.to,
                ctx         = data.context,
                host        = ctx.host,
                prevItem    = ctx.item( from ),
                width       = prevItem.width(),
                height      = prevItem.height(),
                disIndex    = to - from,
                disWidth    = disIndex * width,
                disHeight   = disIndex * height;
            if( !ctx.vertical ){
                var left = parseInt(host.css('left'));
                if( isNaN( left ) ) left = 0;
                host.animate({
                    left: left - disWidth
                },300,function(){ctx.inSwitching = false;});
            }
            else{
                var top = parseInt(host.css('top'));
                if( isNaN( top ) ) top = 0;
                host.animate({
                    top: top - disHeight
                },300,function(){ctx.inSwitching = false;});
            }
        }
    };

    Scroll.Behavior.Scroll = BScroll;

    return Scroll;
});
