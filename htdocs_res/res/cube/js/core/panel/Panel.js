/*
    Copyright (c) 2011, kaixin001 Inc. All rights reserved.
    http://www.kaixin001.com
    author: minliang@corp.kaixin001.com
*/

/**
 * @class Panel 最基础的浮动层功能封装
 * @singleton
 */
define('core/panel/Panel', ['core/jQuery'], function( require ){
    var $ = require('core/jQuery'),
        mix = K.mix,
        UA = K.Browser,
        CustEvent = K.CustEvent,
        zIndex = 503;

    function Panel(opts){
        this.PANEL_CLASS = 'panel';
        this.IFRAME_CLASS = 'panel-iframe';
        this.INNER_CLASS = '_j_dialogcontent';
        this.atCenter = true;
        //延迟显示的时间(毫秒)
        this.delay = 300;

        mix( this, opts, true );

        if( typeof( this.useIframe ) == 'undefined' ){
            this.useIframe = this._needIframe();
        }
        this.width = this.width;
        this.height = this.height || 'auto';
        this.top = this.top || 0;
        this.left = this.left || 0;

        CustEvent.createEvents(this, 'beforeinit,afterinit,beforeshow,aftershow,beforehide,afterhide,beforedispose,afterdispose');

        this.fire('beforeinit');
        this._init();
        this.fire('afterinit');
    }
    mix(Panel.prototype,{
        _init: function(){
            this._createPanelBox();
            this.visible = this.isPanelShow();
        },
        /**
         * 创建一个容器元素
         *
         * @method
         * @protected
         * @param void
         * @return {Panel object}
         */
        _createPanelBox: function() {
            this._panel = $('<div style="position:absolute" class="' + this.PANEL_CLASS + '"></div>');
            this._content = $('<div class="' + this.INNER_CLASS + '"></div>');
            this._panel.append(this._content);

            if ( this.useIframe ){
                this._createIframe();
            }
            this._panel.appendTo($('body'));

            this.setRect({
                left:   this.left,
                top:    this.top,
                width:  this.width,
                height: this.height
            });
            this._panel.hide();

        },
        _createIframe: function(){
            this._iframe = $('<iframe class="' + this.IFRAME_CLASS + '" frameborder="0" src="about:blank"></iframe>');
            this._iframe.css('width','100%').css('height','100%');
            this._panel.append(this._iframe);
        },
        /**
         * 检测自动生成iframe条件
         *
         * @method
         * @protected
         * @param void
         * @return {bool}
         */
        _needIframe: function () {
            var useIframe = !!window.ActiveXObject
                            && ((UA.ie && UA.ie < 7
                            && document.getElementsByTagName('select').length)
                            || document.getElementsByTagName('object').length);
            return useIframe;
        },
        /**
         * 检测使用position fixed的条件
         *
         * @method
         * @protected
         * @param void
         * @protected
         * @return {bool}
         */
        _canbeFixed: function () {
            return !UA.ie || (UA.ie && UA.ie > 6);
        },
        /**
         * 显示Panel控件
         *
         * @method show
         * @param {number} top 坐标
         * @param {number} left 坐标
         * @param {number} width 宽度
         * @param {number} height 高度
         * @param {HTMLElement} el 参考元素(如果未提供参考元素则上述坐标信息参考document,否则参考该元素)
         */
        show: function( rect , el ){
            //只提供el，则Panel的位置与el的左上角对齐
            if( arguments.length == 1 && !$.isPlainObject( rect ) ){
                el = rect;
                rect = {
                    top: 0,
                    left: 0
                };
            }
            if( el ){
                el = $(el);
                var elOffset = el.offset();
                rect.top    += elOffset.top;
                rect.left   += elOffset.left;
            }
            if( rect ){
                this.setRect( rect );
            }

            this.fire('beforeshow');
            this._panel.css('z-index', zIndex++);
            
            if(this._newDlg) {
                this._panel.fadeIn(300);
            } else {
                this._panel.show();
            }
            

            if( this.atCenter ){
                this.center();
            }

            this.visible = true;
            this.fire('aftershow');
            return this;
        },
        hide: function(){
            this.fire('beforehide');
            this._panel.hide();
            this._clearTimer();
            this.visible = false;
            this.fire('afterhide');
            return this;
        },
        /**
         * 延迟一定时间显示Panel
         */
        delayShow: function(){
            var ins = this,
                args = arguments;
            this._clearTimer();
            this._delayTimer = setTimeout( function(){
                ins._delayTimer = null;
                ins.show( args );
            }, this.delay );
        },
        _clearTimer: function(){
            if( this._delayTimer ){
                clearTimeout( this._delayTimer );
            }
        },
        /**
         * 设置Panel的坐标和尺寸
         *
         * @method setRect
         * @param  {number} left    坐标
         * @param  {number} top     坐标
         * @param  {number} width   宽度
         * @param  {number} height  高度
         */
        setRect: function( rect ){
            var w = rect.width  ,
                h = rect.height ;

            this._panel.offset(rect);
            if( typeof( w ) != 'undefined' ){
                this._panel.width( w );
            }
            if( typeof( h ) != 'undefined' ){
                this._panel.height( h );
            }
            return this;
        },
        /**
         * 设置Panel的尺寸
         *
         * @method setSize
         * @param  {number} w宽度
         * @param  {number} h高度
         * @return {Panel object}
         */
        setSize: function( w, h ){
            this._panel.width( w ).height( h );
            return this;
        },
        /**
         * 设置Panel的坐标
         *
         * @method setXY
         * @param  {number} w宽度
         * @param  {number} h高度
         * @return {Panel object}
         */
        setXY: function( x, y ){
            this._panel.offset({
                top: y,
                left: x
            });
            return this;
        },
        /**
         * 设置Panel为居中状态
         *
         * @method contains
         */
        center: function(){
            var panelRect = this.getRect(),
                winRect = this.getWinRect(),
                w = panelRect.width,
                h = panelRect.height;
            var x = parseInt( ( winRect.width - w ) / 2 );
            var y = parseInt( ( winRect.height - h ) / 2 );

            if ( x < 0 ) x = 0;
            if ( y < 0 ) y = 0;

            /* if position is 'fixed', x and y coordinate not include bounds coords */
            var position = this._panel.css('position');
            if ( 'fixed' != position ) {
                x = x + winRect.scrollLeft;
                y = y + winRect.scrollTop;
                if ( x <= winRect.scrollLeft ) x = winRect.scrollLeft;
                if ( y <= winRect.scrollTop ) y = winRect.scrollTop;
            }


            this.setXY( x, y );

            return this;
        },
        /**
         * 判断元素知否包含在Panel之中
         *
         * @method contains
         * @param  {HTMLElement} el
         * @return {Boolean}
         */
        contains: function(el) {
            return $.contains( this._panel, el );
        },
        /**
         * 获取Panel的坐标和大小
         *
         * @method getRect
         * @return {Object}
         */
        getRect: function(){
            var rlt = this._panel.offset();
            mix( rlt, {
                width: this._panel.width(),
                height: this._panel.height()
            });
            return rlt;
        },
        appendToPanel: function( el ){
            this._panel.append( el );
            return this;
        },
        appendToContent: function( el ){
            this._content.append( el );
            return this;
        },
        getPanel: function(){
            return this._panel;
        },
        getIframe: function(){
            return this._iframe;
        },
        getZIndex: function(){
            return this._panel.css('z-index');
        },
        /**
         * 全屏Panel
         *
         * @method setPanelFullscreen
         * @return {Panel object}
         */
        setFullscreen: function () {
            var rect = {
                top:    0,
                left:   0,
                height: $(document).height(),
                width:  $(document).width()
            };
            this.setRect( rect );
            return this;
        },
        addClass: function( className ){
            this._panel.addClass( className );
            if( this._iframe )
                this._iframe.addClass( className );
            return this;
        },
        removeClass: function( className ){
            this._panel.removeClass( className );
            if( this._iframe )
                this._iframe.removeClass( className );
            return this;
        },
        setStyle: function( name , value ){
            this._panel.css( name, value );
            if( this._iframe )
                this._iframe.css( name, value );
            return this;
        },
        isPanelShow: function(){
            return this._panel.css('visibility') != 'hidden' && this._panel.css('display') != 'none';
        },
        getWinRect: function(){
            return {
                scrollLeft: $(window).scrollLeft(),
                scrollTop:  $(window).scrollTop(),
                width:      $(window).width(),
                height:     $(window).height()
            }
        },
        dispose: function(){
            this.fire( 'beforedispose' );
            this._iframe && this._iframe.remove();
            this._panel && this._panel.remove();
            this.fire( 'afterdispose' );
        }
    });
    return Panel;
});
