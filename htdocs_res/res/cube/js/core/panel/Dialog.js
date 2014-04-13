/**
* @class Dialog 继承于Panel，实现开心最常用的Dialog功能
*/
define('core/panel/Dialog', ['core/jQuery', 'core/panel/Panel', 'core/panel/Mask'], function( require ){
    var $ = require('core/jQuery'),
    Panel = require('core/panel/Panel'),
    Mask = require('core/panel/Mask'),
    mix = K.mix,
    extend = K.extend,
    UA = K.Browser,
    CustEvent = K.CustEvent;

    var Dialog = extend( function( opts ){
        this._initDialog( opts );
    }, Panel );

    mix( Dialog.prototype, {
        /*
        可用的选项包括
        {
        top:        //坐标                    (默认为0)
        left:       //坐标                    (默认为0)
        height:     //高度                    (默认为0)
        width:      //宽度                    (默认为0)
        atCenter:   //是否居中              (默认为true)
        useIframe:  //是否使用iframe遮挡  (默认自动判断)
        useMask:    //是否使用遮罩            (默认使用)
        title:      //Dialog标题          (默认为空)
        className:  //Dialog样式名         (默认为panel-t1)
        content:    //Dialog内容          (默认为空)
        foot:       //Dialog底栏内容        (默认为空)
        closeClass: //Dialog关闭方式 _j_close - hide(隐藏-默认), _j_dispose(移除)
        closeTPL:   //Dialog关闭按钮html，方便配置某些特殊的浮层关闭按钮
        }
        */
        _initDialog: function( opts ){      
            this.useMask = true;
            this.title = undefined;
            this.content = '';
            this.foot = '';
            this.className = 'dialog _j_dialog';

            //阴影宽度(TOP与BOTTOM采用阴影,Left和Right为Border)
            this.sdBorderWidthTB = 0;
            this.sdBorderWidthLR = 0;
            this.hdHeight = undefined;
            this.closeClass = '_j_close'; // 设定关闭方式
            this.closeTPL = '<a class="dialog_close ' + this.closeClass + '" href="#"></a>';

            mix( this, opts ,true);

            Dialog.$super.call(this,opts);  

            this.addClass( this.className );

            this._createDialog();

            if( this.useMask ){
                this._createMask();
            }

            this._bindEvents();

            //if( this.title ){
            this.setTitle( this.title );
            //}

            if( this.content ){
                this.setContent( this.content );
            }

            if( this.foot ){
                this.setFooter( this.foot );
            }

            this.layout();
        },
        /*
        * 创建Dialog结构
        */
        /*
        <div class="dialog">
        <a title="关闭" class="spr_icons i_close_big abs" href="#"></a>
        <div class="dialog_title">
        <h2>$title</h2>
        </div>
        <div class="dialog_bd fix">
        $content
        </div>
        </div>
        */
        _createDialog: function(){
            var panel = this.getPanel(); 

            // 创建close
            if(this.closeTPL) {
                this._close = $(this.closeTPL);
                this.appendToPanel(this._close);
            }

            //创建内容区域
            this._hd = $('<div class="dialog_title"></div>');
            this.appendToContent( this._hd );
            if(!isNaN(this.hdHeight)) {
                this._hd.css('height', this.hdHeight);
            }
            this._bd = $('<div class="dialog_bd fix">');
            this._ft = $('<div/>');

            this.appendToContent( this._bd );
            this.appendToContent( this._ft );

        },
        /*
        * 创建Mask
        */
        _createMask: function(){
            this._mark = new Mask({'MASK_CLASS' : this.MASK_CLASS});
            this._mark.follow( this );
        },
        /*
        * 绑定事件
        */
        _bindEvents: function(){
            var ins = this;

            // hide
            this.getPanel().delegate('._j_close', 'click', function(ev) {
                ins.hide();
                ev.preventDefault();
            });

            // dispose
            this.getPanel().delegate('._j_dispose', 'click', function(ev) {
                ins.dispose();
                ev.preventDefault();
            });
        },
        /**
        * 设置Dialog的坐标和尺寸
        *
        * @method setRect
        * @param  {number} left    坐标
        * @param  {number} top     坐标
        * @param  {number} width   宽度
        * @param  {number} height  高度
        * @return {Dialog object}
        */
        setRect: function( rect ){
            var w = rect.width  ,
            h = rect.height ;

            this._panel.offset(rect);
            if( typeof( w ) != 'undefined' ){
                rect.width = this.fixWidth( w );
            }
            if( typeof( h ) != 'undefined' ){
                rect.height = this.fixHeight( h );
            }

            Dialog.$super.prototype.setRect.call( this, rect );

            return this;
        },
        /**
        * 设置Dialog的尺寸
        *
        * @method setSize
        * @param  {number} w宽度
        * @param  {number} h高度
        * @return {Dialog object}
        */
        setSize: function( w, h ){
            w = this.fixWidth( w );
            h = this.fixHeight( h );
            Dialog.$super.prototype.setSize.call( this, w, h );
            this.layout();
            return this;
        },
        setFullscreen: function(){
            Dialog.$super.prototype.setFullscreen.call( this );
            this.layout();
            return this;
        },
        /**
        * 设置Dialog标题
        * @param {String} title Dialog标题
        */
        setTitle: function( title ){
            if(K.isUndefined(title)) {
                this._hd.addClass('dn');
            } else {
                this._hd.html('<h3>' + title + '</h3>').removeClass('dn');
            }
            return this;
        },      
        /**
        * 设置Dialog内容
        * @param {String|Element} content Dialog内容;如果是字符串则直接当做内容，如果是元素则直接append。
        */
        setContent: function( content ){
            if( $.type( content ) == 'string' ){
                this._bd.html( content );
            }
            else{
                this._bd.empty();
                this._bd.append( $(content) );
            }
        },      

        /**
        * 获取body/content的元素
        *
        */
        getBody: function() {
            return this._bd;
        },

        /**
        * 设置底栏内容
        * @param {String} footer Dialog底栏内容
        */
        setFooter: function( content ){
            this._ft.html( content );
        },
        /**
        * 计算高度等操作
        */
        layout: function(){
            //设置bd的高度
            var rect = this.getRect();

            /*
            this._bd.css('height', 
            rect.height 
            - this._hd.height() 
            - this._ft.height() 
            //- this._sd.find('img').eq(0).height() 
            //- this._sd.find('img').eq(1).height()
            //-3
            );
            */
            //调整阴影高度(与样式相关，目前没有找到好的纯样式解决方案，待替换)
            //var imgHeight = this._sd.find('img').eq(0).height() + this._sd.find('img').eq(1).height();
            //this._sd.find('.rect').css('height',rect.height - imgHeight);

            if( this.atCenter ){
                this.center();
            }
        },

        /**
        * 设置Panel为居中状态(覆盖Panel js中的设置)
        *
        * @method contains
        */
        center: function(){
            var panelRect = this.getRect(),
            winRect = this.getWinRect(),
            w = panelRect.width,
            h = panelRect.height;

            var x = parseInt( ( winRect.width - w ) / 2 );
            var y = parseInt( ( winRect.height - h ) * 2 / 5 );

            if ( x < 0 ) x = 0;
            if ( y < 40 ) y = 40;

            /* if position is 'fixed', x and y coordinate not include bounds coords */
            var position = this._panel.css('position');
            if ( 'fixed' != position ) {
                x = x + winRect.scrollLeft;
                y = y + winRect.scrollTop;
                if ( x <= winRect.scrollLeft ) x = winRect.scrollLeft;
                if ( y <= winRect.scrollTop + 40 ) y = winRect.scrollTop + 40;
            }


            this.setXY( x, y );

            return this;
        },

        //传入内容的宽度，返回的是Dialog的宽度
        fixWidth: function( w ){
            return w + this.sdBorderWidthLR * 2; //+ 2;
        },
        //传入内容的高度，返回的是Dialog的高度
        fixHeight: function( h ){
            return h + this.sdBorderWidthTB * 2; //+ 3;
        },
        getMask: function(){
            return this._mark;
        }
    } );

    return Dialog;
});
