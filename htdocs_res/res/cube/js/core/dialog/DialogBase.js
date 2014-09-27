/**
* Dialog Base，基于Layer
*
* 基本属性：水平居中，垂直方向距离顶部有最小距离，上下空间比例2:3，定位切换动画等Dialog基本属性
*/
define('core/dialog/DialogBase', ['core/dialog/Layer', 'core/tool/Browser'], function(require) {

    var Layer = require('core/dialog/Layer'),
    Browser = require('core/tool/Browser'),
    $ = require('core/jQuery');

    isOldIE = (function() {
        return Browser.browser == 'MSIE' && parseInt(Browser.version, 10) < 7;
    } ());

    function DialogBase(options) {

        // options
        this.newLayer = false; // 是否基于新图层（默认基于当前活跃图层）
        this.width = '';
        this.height = '';
        this.fixed = true;
        this.closeWhenESC = true;
        this.visible = false;
        this.isAtBackground = false;
        this.closeWhenMaskClicked = true;

        K.mix(this, options);

        // instance member
        this.destroyed = false;
        this._mask = null;
        this._actionList = [];
        this._dataContainer = {};

        K.CustEvent.createEvents(this, 'beforeshow,aftershow,beforehide,afterhide,beforedestroy,afterdestroy');
        this._createDialog();
        this._bindEvents();
    }

    K.mix(DialogBase, {

        _generateID: function() {
            return K.uniqueId("__dialog__");
        },
    });

    DialogBase.prototype = {

        setData: function() {
            var args = arguments;
            if (args.length == 1) {
                this._dataContainer = args[0];
            }
            this._dataContainer[args[0]] = args[1];
            return this;
        },

        getData: function(key) {
            if (key) {
                return this._dataContainer[key];
            }
            return this._dataContainer;
        },

        _getLayerOptions: function() {
            var options = this.layerOptions || {};
            options.background = options.background || "black";
            options.opacity = options.opacity || "0.6";
            options.zIndex = options.zIndex || 40000;
            options.fixed = this.fixed;
            return options;
        },

        _addToLayer: function() {

            var options = this._getLayerOptions();
            this._layer = Layer.want(options, 'Dialog', this.newLayer);

            this._layer.setFixed(this.fixed);

            this._layer.addContent(this._panel, this.frozenBackground);
            this._layer.pushToContentStack(this);
        },

        _createDialog: function() {
            this._panel = $('<div class="_j_panel"/>');
            this._panel.css({
                'position': 'absolute',
                'opacity': 0,
                'display': 'none',
                'z-index': 0,
                'margin': '0 0 30px 0',
            });

            this._mask = $('<div class="_j_dialog_mask"></div>');
            var maskCss = {
                'position': 'absolute',
                'width': '100%',
                'height': '100%',
                'display': "none",
                'background': 'black',
                'opacity': '0.2',
                'z-index': 30
            };
            this._mask.css(maskCss);
            this._panel.append(this._mask);

            this._addToLayer();

            this.drawDialogContent();
        },

        _bindEvents: function() {
            // 监听window resize事件
            var me = this, timer;
            $(window).resize(function() {
                clearTimeout(timer);
                if (me.visible) {
                    timer = setTimeout(function() {
                        me.setPosition(null, 300);
                    }, 100);
                }
            });

            // 关闭事件
            this._panel.delegate('._j_dialog_close', 'click', function(ev) {
                me.close();
                ev.preventDefault();
            });

            // layer mask点击
            this.maskClickHandler = K.bind(this.onLayerMaskClick, this);
            this._layer.on('mask_clicked', this.maskClickHandler);
        },

        getPanel: function() {
            return this._panel;
        },

        /**
        * 获得遮罩层(显示之前，getMask返回false)
        */
        getMask: function() {
            return this._layer && this._layer.getMask();
        },

        /**
        * 重写此方法来设置Dialog Panel内容
        */
        drawDialogContent: function() {
        },

        _getPanelRect: function(pos) {
            var panelClone = this.getPanel().clone()
            var css_data = K.clone(pos) || {};

            K.mix(css_data, { position: "absolute", visibility: "hidden", display: "block" });
            panelClone.css(css_data).appendTo('body');
            panelHeight = panelClone.outerHeight(),
            panelWidth = panelClone.outerWidth();

            panelClone.remove();
            return { 'height': panelHeight, 'width': panelWidth };
        },

        _getNumric: function(num) {
            num = parseInt(num, 10);
            return isNaN(num) ? 0 : num;
        },

        /**
        * width, height, top, left, keep_top
        */
        setPosition: function(pos_data, ani_time, callback) {
            var action = K.mix(pos_data || {}, {
                ani_time: ani_time,
                callback: callback,
            });
            this._actionList.push(action);
            this.doNextAction();
        },

        doNextAction: function() {
            if (this.isDoing) {
                return;
            }
            if (this._actionList.length == 0) {
                return;
            }
            this.isDoing = true;
            var action = this._actionList.shift();
            var callback = action.callback;

            var panelRect = this._getPanelRect(action),
            winWH = {
                'width': $(window).width(),
                'height': $(window).height()
            };

            // horizontal-center; top:  2/5 * (winWH - height), min 30;
            var w = (this._getNumric(action.width) > 0 ? this._getNumric(action.width) : panelRect.width);
            var h = (this._getNumric(action.height) > 0 ? this._getNumric(action.height) : panelRect.height);

            var defaultLeft = (winWH.width - w) / 2,
            defaultTop = (winWH.height - h) * 2 / 5;

            defaultTop = defaultTop < 0 ? 30 : defaultTop;
            if (isOldIE || !this.fixed) {
                var scrollTop = $(window).scrollTop();
                if (scrollTop > 0) {
                    defaultTop += scrollTop;
                }
            }

            var pos = {};
            pos.top = action.top || defaultTop;
            if (!action.keep_top && action.top) {
                pos.top =  action.top;
            }
            pos.left = action.left || defaultLeft;

            // 显示区域超过浏览器边界处理
            if (!isOldIE && this.fixed) {
                if (winWH.height - panelHeight < pos.top) {
                    this.getPanel().addClass('dialog_overflow');
                    this._layer.setOverFlow(true);
                } else {
                    this.getPanel().removeClass('dialog_overflow');
                    this._layer.setOverFlow(false);
                }
            }

            pos.width = w;
            pos.height = h;

            var that = this;
            var done = function () {
                that.isDoing = false;
                that.doNextAction();
                if (callback) {
                    callback();
                }
            };

            if (action.ani_time > 0) {
                var aniOptions = { duration: action.ani_time, done: done};
                this._panel.animate(pos, aniOptions);
            } else {
                this._panel.css(pos);
                done();
            }
        },

        setFixed: function(fixed) {
            this.fixed = !!fixed;
            this._layer.setFixed(this.fixed);
        },

        show: function() {
            if (this.visible) {
                return;
            }

            this.fire('beforeshow');

            // 切换活动Dialog，显示活动Dialog
            this.visible = true;

            // 显示动作主体
            this._layer.show();
            this.getPanel().css({ 'display': '', 'z-index': 1 });
            this.getPanel().css({ 'opacity': 1 });
            this.fire('aftershow');
        },

        /**
        * Implements Layer content method
        */
        whenESC: function() {
            if (this.closeWhenESC) {
                this.close();
            }
        },

        /**
        * Implements Layer content method
        */
        sendToBack: function() {
            this.isAtBackground = true;
            this._mask.show();
        },

        /**
        * Implements Layer content method
        */
        bringToFront: function() {

            this.isAtBackground = false;
            this._mask.hide();
        },

        onLayerMaskClick: function() {

            if (this.isAtBackground) {
                return;
            }
            if (this.closeWhenMaskClicked) {
                this.close();
            }
        },

        /**
        * 根据当前Dialog是否可被叠加，来确认隐藏还是销毁
        */
        close: function(callback) {
            this.destroy(callback);
        },

        hide: function(callback) {

            // 已经隐藏状态
            if (!this.visible) {
                typeof callback == 'function' && callback();
                return;
            }

            this.fire('beforehide');

            this.visible = false;

            // 隐藏动作主体
            this.getPanel().animate({ 'opacity': 0 }, 200, $.proxy(function() {
                this.getPanel().css({ 'display': 'none', 'z-index': 0 });
                this.fire('afterhide');
                typeof callback == 'function' && callback();
            }, this));
        },

        destroy: function(callback) {
            // 不重复处理
            if (this.destroyed) {
                typeof callback == 'function' && callback();
                return;
            }

            this.fire('beforedestroy');

            // 设置销毁标识
            this.destroyed = true;

            // 销毁动作主体
            this.hide($.proxy(function() {
                if (this._panel.length) {

                    this._layer.un('mask_clicked', this.maskClickHandler);
                    this._panel.undelegate();
                    this._panel.unbind();
                    this._panel.remove();
                    this._panel = null;
                }

                this._layer.removeFromContentStack(this);
                if (this._layer.getTopContent() == null) {
                    this._layer.hide();
                }

                typeof callback == 'function' && callback();
                this.fire('afterdestroy');
            }, this));
        }
    };

    return DialogBase;
});
