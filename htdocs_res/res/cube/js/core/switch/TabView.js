/*
    Copyright (c) 2011, kaixin001 Inc. All rights reserved.
    http://www.kaixin001.com
    author: minliang@corp.kaixin001.com
*/

/**
 * @class TabView TabView功能封装
 */
define('core/switch/TabView',['core/jQuery'],function( require ){
    var $ = require('core/jQuery'),
        mix = K.mix,
        CustEvent = K.CustEvent;
    /**
     * options中的配置如下
            {Array|String}      tabs        如果是字符串则应该是选择符；否则应该为tab元素数组
            {Array|String}      contents    如果是字符串则应该是选择符；否则应该为content元素数组
            {TabView.Behavior}  behavior    具体进行TabView切换的策略类(默认为ShowHide)
            {Array}             events      触发切换的事件类型
     * @constructor
     */
    function TabView( options ){
        /**
         * @property list   Item列表
         *                  Item包括以下内容
                            {
                                tab:                //头部元素-切换行为的触发器，(HTML元素 选择符等)
                                content:            //内容元素-tab对应的内容(HTML元素 选择符等)
                            }
         * @type    {Array}
         */
        this.list = [];

        /**
         * @property index  当前下标
         * @type    {int}
         */
        this.index = -1;
        
        /**
         * @property events 触发切换的事件
         * @type    {array}
         */
        this.events = ['click'];

        this.TAB_SELECTED_CLASS     =   'selected';
        this.TAB_UNSELECTED_CLASS   =   'unselected';
        this.CT_SELECTED_CLASS      =   'selected';
        this.CT_UNSELECTED_CLASS    =   'unselected';

        this.init( options );
    }
    mix(TabView.prototype,{     
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
            CustEvent.createEvents( this, 'beforeswitch,afterswitch' );

            mix( this, options, true );

            if( !this.behavior ){
                this.behavior = TabView.Behavior.ShowHide;
            }

            //创建item list
            var ins = this;
            if( K.isArray( this.tabs ) && K.isArray( this.contents ) ){
                K.forEach( this.tabs , function( tab, idx ){
                    var ct = ins.contents[ idx ],
                        item = {
                            tab: $( tab ),
                            content: $( content )
                        };
                    ins.add( item );
                    ins._addEvents( item );
                    if( item.tab.hasClass( ins.TAB_SELECTED_CLASS ) ){
                        ins.index = idx;
                    }
                } );
            }
            else if( K.isString( this.tabs ) && K.isString( this.contents ) ){
                var tabs = $( this.tabs ),
                    contents = $( this.contents );
                tabs.each( function( idx ){
                    var item = {
                            tab: $( this ),
                            content: contents.eq( idx )
                        };
                    ins.add( item );
                    ins._addEvents( item );
                    
                    if( item.tab.hasClass( ins.TAB_SELECTED_CLASS ) ){
                        ins.index = idx;
                    }
                });
            }
        },
        _addEvents: function( item ){
            var evts = this.events;
            if( !evts ) return;
            var ins = this;
            K.forEach( evts, function( evt, idx ){
                var itemIdx = ins.indexOf( item );
                item.tab.bind( evt,{index:itemIdx,type: evt}, $.proxy(ins._triggerHandler,ins) );
            });
        },
        _triggerHandler: function( e ){
            e.preventDefault();
            this.to( e.data.index, e.data.evt );
        },
        /**
         * @method insert 插入元素
         * @override
         * @param   {item}  item    元素
         * @param   {int}   index   下标
         * @return  {item}
         */
        insert : function (item, index) {
            index = Math.max(Math.min(this.list.length, index), 0);
            if (index < this.index) {
                ++ this.index;
            }
            this.list.splice(index, 0, item);
            return item;
        },
        /**
         * @method add 添加元素
         * @override
         * @param   {item}  item    元素
         * @return  {item}
         */
        add : function (item) {
            this.insert(item, this.list.length);
            return item;
        },

        /**
         * @method remove 移除元素
         * @override
         * @param   {int}   index   下标
         * @return  {item}
         */
        remove : function (index) {
            if (!this.list.length) return null;
            index = Math.max(Math.min(this.list.length - 1, index), 0);

            var result = this.list[ this.index ];
            if (index < this.index) {
                -- this.index;
            } 
            else if (index == this.index) {
                this.index = -1;
            }
            this.list.splice(index, 1);
            return result;
        },


        /**
         * @method to 切换到
         * @param   {int}       index       下标
         * @param   {string}    evtType     触发切换的事件名称
         * @return  {void}
         */
        to : function (index, evtType) {
            if (!(this.list.length && index < this.list.length)) return;

            this.dispatchBeforeSwitch( this.index, index );
            
            var prev = this.index;
            this.index = index;

            //真正的切换行为交给具体的策略类来处理
            this.behavior.trigger({
                from: prev,
                to: index,
                context: this,
                type: evtType || 'click'
            });

            this.dispatchAfterSwitch( prev, index );
        },

        /**
         * @method indexOf 根据元素查找下标
         * @param   {item}  item    元素
         * @return  {int}
         */
        indexOf : function (item) {
            for (var i = 0, l = this.list.length ; i < l ; ++ i) {
                var it = this.list[i];
                if( it.tab && it.content && it.tab[0] == $(item.tab)[0] && it.content[0] == $(item.content)[0] ){
                    return i;
                }
                if (this.list[i] == item) return i;
            }
            return -1;
        },

        /**
         * @method item 根据下表找到元素
         * @param   {int}   index   下标
         * @return  {item}
         */
        item : function (index) {
            if (this.list.length == 0 || index < 0) return null;
            return this.list[index % this.list.length] || null;
        },

        /**
         * @method getCurrent 获取当前选中元素
         * @return  {item}
         */
        getCurrent : function () {
            return this.item(this.index);
        },

        /**
         * @method getLast 获取最后的元素
         * @return  {item}
         */
        getLast : function () {
            return this.item(this.list.length - 1);
        },

        /**
         * @method getFirst 获取开头的元素
         * @return  {item}
         */
        getFirst : function () {
            return this.item(0);
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
                to : this.item(to)
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
        }
    });

    /*Tabview切换效果的策略类，如果需要扩展切换效果则在策略中增加新的类即可*/
    TabView.Behavior = {};
    
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
            var from    = data.from,
                to      = data.to,
                tv      = data.context,
                evt     = data.type;
            var prevItem = tv.item( from ),
                curItem = tv.item( to );
            if( prevItem ){
                prevItem.tab.removeClass( tv.TAB_SELECTED_CLASS );
                prevItem.tab.addClass( tv.TAB_UNSELECTED_CLASS );
                prevItem.content.removeClass( tv.CT_SELECTED_CLASS );
                prevItem.content.addClass( tv.CT_UNSELECTED_CLASS );
            }
            if( curItem ){
                curItem.tab.addClass( tv.TAB_SELECTED_CLASS );
                curItem.tab.removeClass( tv.TAB_UNSELECTED_CLASS );
                curItem.content.addClass( tv.CT_SELECTED_CLASS );
                curItem.content.removeClass( tv.CT_UNSELECTED_CLASS );
            }
        }
    };

    TabView.Behavior.ShowHide = ShowHide;

    var FadeIn = {
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
            var from    = data.from,
                to      = data.to,
                tv      = data.context,
                evt     = data.type;
            var prevItem = tv.item( from ),
                curItem = tv.item( to );
            if( prevItem ){
                prevItem.tab.removeClass( tv.TAB_SELECTED_CLASS );
                prevItem.tab.addClass( tv.TAB_UNSELECTED_CLASS );
                prevItem.content.hide();
            }
            if( curItem ){
                curItem.tab.addClass( tv.TAB_SELECTED_CLASS );
                curItem.tab.removeClass( tv.TAB_UNSELECTED_CLASS );
                curItem.content.fadeIn();
            }
        }
    };

    TabView.Behavior.FadeIn = FadeIn;

    return TabView;
});