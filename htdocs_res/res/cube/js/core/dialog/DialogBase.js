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

        this.newLayer = false; // 是否基于新图层（默认基于当前活跃图层）
        this.width = '';
        this.height = '';
        this.fixed = true;
        this.impl = 'Dialog';
        this.closeWhenESC = true;

        K.mix(this, options);

        K.CustEvent.createEvents(this, 'beforeshow,aftershow,beforehide,afterhide,beforedestroy,afterdestroy');

        this.visible = false;
        this.destroyed = false;

        this.init();
    }

    K.mix(DialogBase, {

        _dialogStack: [],

        _generateID: function() {
            return K.uniqueId("__dialog__");
        },

        pushToStack: function(dialog) {
            var current = this.getCurrent();
            if (undefined != current) {
                current.sendToBack();
            }
            this._dialogStack.push(dialog);
        },

        getCurrent: function() {
            return K.last(this._dialogStack);
        },

        closeCurrent: function() {
            var current = this.getCurrent();
            if (null != current) {
                current.close();
            }
        },

        popThis: function(dialog) {
            var current = this.getCurrent();
            if (current == dialog) {
                this._dialogStack.pop();
            }
            current = this.getCurrent();
            if (undefined != current) {
                current.bringToFront();
            }
            else {
                dialog.getLayer().hide();
            }
        }
    });

    DialogBase.prototype = {

        isAtBackground: false,

        frozenBackground: true,

        closeWhenMaskClicked: true,

        stackId: null,

        _mask: null,

        _dataContainer: {},

        'setData': function() {
            var args = arguments;
            if (args.length == 1) {
                this._dataContainer = args[0];
            }
            this._dataContainer[args[0]] = args[1];
            return this;
        },

        'getData': function(key) {
            if (key) {
                return this._dataContainer[key];
            }
            return this._dataContainer;
        },

        'init': function() {
            this._createDialog();
            this._bindEvents();
        },

        '_getLayerOptions': function() {
            var options = this.layerOptions || {};
            options.background = options.background || "black";
            options.opacity = options.opacity || "0.6";
            options.zIndex = options.zIndex || 40000;
            options.impl = this.impl;
            options.fixed = this.fixed;
            return options;
        },

        '_addToLayer': function() {

            var options = this._getLayerOptions();
            this._layer = !this.newLayer && Layer.getImplInstance(this.impl) ||
                new Layer(options);

            this._layer.setFixed(this.fixed);
            this.stackId = this._layer.newStack(this._panel); // 层叠放入基层中

            this._layer.setActiveStack(this);
            DialogBase.pushToStack(this);
        },

        '_createDialog': function() {
            this._panel = $('<div class="_j_panel"/>');
            this._panel.css({
                'position': 'absolute',
                'opacity': 0,
                'display': 'none',
                'z-index': 0
            });

            this._mask = $('<div class="_j_dialog_mask"></div>');
            var maskCss = {
                'position': 'absolute',
                'height': '100%',
                'display': "none",
                'background': 'black',
                'opacity': '0.1',
                'z-index': 30
            };
            this._mask.css(maskCss);
            this._panel.append(this._mask);

            this.setRect({ 'width': this.width, 'height': this.height });

            this._addToLayer();

            this.drawDialogContent();
        },


        '_bindEvents': function() {
            // 监听window resize事件
            var me = this, timer;
            $(window).resize(function() {
                clearTimeout(timer);
                if (me.visible) {
                    timer = setTimeout(function() {
                        me.setPosition();
                    }, 100);
                }
            });

            // 关闭事件
            this._panel.delegate('._j_dialog_close', 'click', function(ev) {
                me.close();
                ev.preventDefault();
            });
            this._panel.bind('whenESC', function(ev, single) {
                if (me.closeWhenESC) {
                    me.close(single);
                }
            });

            // layer mask点击
            this.maskClickHandler = K.bind(this.onLayerMaskClick, this);
            this._layer.on('mask_clicked', this.maskClickHandler);
        },

        'addClass': function(classes) {
            this._panel.addClass(classes);
        },

        'removeClass': function(classes) {
            this._panel.removeClass(classes);
        },

        'setRect': function(rect) {
            if (rect.width) {
                this._panel.css('width', rect.width);
                this._mask.css('width', rect.width);
                this.width = rect.width;
            }
            if (rect.height) {
                this._panel.css('height', rect.height);
                this.height = rect.height;
            }
        },

        'getPanel': function() {
            return this._panel;
        },

        /**
        * 获得所在层
        */
        'getLayer': function() {
            return this._layer;
        },

        /**
        * 获得遮罩层(显示之前，getMask返回false)
        */
        'getMask': function() {
            return this._layer && this._layer.getMask();
        },

        'drawDialogContent': function() {
            // 重写此方法来设置Dialog Panel内容
        },

        '_getPanelRect': function() {
            var panelClone = this.getPanel().clone()
            .css({ position: "absolute", visibility: "hidden", display: "block" }).appendTo('body');
            panelHeight = panelClone.outerHeight(),
            panelWidth = panelClone.outerWidth();

            panelClone.remove();
            return { 'height': panelHeight, 'width': panelWidth };
        },

        '_getNumric': function(num) {
            num = parseInt(num, 10);
            return isNaN(num) ? 0 : num;
        },

        'setPosition': function(position, noAnimate) {
            var panelRect = this._getPanelRect(),
            winWH = {
                'width': $(window).width(),
                'height': $(window).height()
            };

            // 默认定位（水平居中，2/7*total）
            var defaultLeft = (winWH.width - (this._getNumric(this.width) > 0 ? this._getNumric(this.width) : panelRect.width)) / 2,
            detaultTop = (winWH.height - (this._getNumric(this.height) > 0 ? this._getNumric(this.height) : panelRect.height)) * 2 / 7;

            detaultTop = detaultTop < 0 ? 0 : detaultTop;
            if (isOldIE || !this.fixed) {
                var scrollTop = $(window).scrollTop();
                if (scrollTop > 0) {
                    detaultTop += scrollTop;
                }
            }

            position = K.clone(position) || {};
            position.top = position.top || detaultTop;
            position.left = position.left || defaultLeft;

            // 显示区域超过浏览器边界处理
            if (!isOldIE && this.fixed) {
                if (winWH.height - panelHeight < position.top) {
                    this.getPanel().addClass('dialog_overflow');
                    this._layer.setOverFlow(true);
                } else {
                    this.getPanel().removeClass('dialog_overflow');
                    this._layer.setOverFlow(false);
                }
            }

            var postionAction = noAnimate ? 'css' : 'animate';
            $.fn[postionAction].call(this.getPanel(), position, 200);

            this.position = position;
        },

        'setFixed': function(fixed) {
            this.fixed = !!fixed;
            this._layer.setFixed(this.fixed);
        },

        'getPosition': function() {
            return this.position;
        },

        'show': function(position, noAnimate) {
            if (this.visible) {
                return;
            }

            var me = this;
            this.fire('beforeshow');

            // 切换活动Dialog，显示活动Dialog
            this.visible = true;

            // 显示动作主体
            this._layer.show();
            this.getPanel().css({ 'display': '', 'z-index': 1 });
            this.setPosition(position, noAnimate);

            if (noAnimate) {
                this.getPanel().css({ 'opacity': 1 });
                me.fire('aftershow');
            }
            else {
                this.getPanel().animate({ 'opacity': 1 }, 200, function() {
                    me.fire('aftershow');
                });
            }
        },

        'updatePostion': function(noAnimate) {
            if (this.visible) {
                this.setPosition(null, noAnimate);
            }
            else {
                this.show(null, noAnimate);
            }
        },

        'sendToBack': function() {
            this.isAtBackground = true;
            this._mask.show();
        },

        'bringToFront': function() {

            this.isAtBackground = false;
            this._mask.hide();

            this._layer.setActiveStack(this);
        },

        'onLayerMaskClick': function() {

            if (this.isAtBackground) return;
            if (this.closeWhenMaskClicked) this.close();
        },

        /**
        * 根据当前Dialog是否可被叠加，来确认隐藏还是销毁
        */
        'close': function(callback) {
            this.destroy(callback);
        },

        'hide': function(callback) {

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

        'destroy': function(callback) {
            // 不重复处理
            if (this.destroyed) {
                K.log('Dialog already destroyed!');
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

                DialogBase.popThis(this);

                this._layer = null;

                typeof callback == 'function' && callback();
                this.fire('afterdestroy');
            }, this));
        }
    };

    return DialogBase;
});
