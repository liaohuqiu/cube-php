/**
* Dialog基础类
*
* 一个Layer就是一个固定定位层，可以在它上面实现Dialog等弹出浮层
*
* esc键时，关闭当前活动的层
*/
define('core/dialog/Layer', ['core/tool/Browser'], function(require) {

    var $ = require('core/jQuery'),
    Browser = require('core/tool/Browser'),
    layerId = 1,
    offsetRight = 0,

    isIE7 = (function() {
        return Browser.browser == 'MSIE' && parseInt(Browser.version, 10) == 7;
    } ()),

    isOldIE = (function() {
        return Browser.browser == 'MSIE' && parseInt(Browser.version, 10) < 7;
    } ());

    function Layer(options) {

        // Mac 右边的scrollbar是悬浮的
        if (Browser.OS != 'Mac') {
            offsetRight = 17;
        }

        this.opacity = 0.8; // panel遮罩层透明度(遮罩层的透明度使用半透明背景图实现，避免Layer上的组件整体是透明的)
        this.background = '#fff'; // 更加高级的控制背景

        this.impl = 'Dialog'; // 实现类型（默认为Dialog）
        this.fixed = true;

        K.mix(this, options);
        this.id = 'layer_' + layerId;
        this.stacks = [];
        this.activeStackId = null;
        this.overflow = false;
        this.changeFixed = false; // 记录当前layer的定位是否被更改过，对于下个stack定位有帮助
        Layer.instances[this.id] = this;

        // 为各种impl建立快速索引
        if (!Layer[this.impl]) {
            Layer[this.impl] = [];
        }
        Layer[this.impl].push(this.id);

        this.init();
    }

    Layer.prototype = {

        '_content': null,

        'frozenBackground': false,

        'init': function() {

            K.CustEvent.createEvents(this, 'mask_clicked');
            this._createPanel();
            this._bindEvents();
            this._insertCss();
        },

        '_bindEvents': function(e) {

            this._panel.delegate('._j_layer_content', 'click', K.bind(function(ev) {
                var target = $(ev.target);
                if (target.data('is-container')) {
                    this.fire('mask_clicked');
                }
            }, this));

            var me = this;
            $(window).resize(function() {
                if (me.visible && me.frozenBackground) {
                    $('#head').css("width", $(window).width() - offsetRight);
                }
            });
        },

        '_createPanel': function() {
            var css = {
                'position': (!isOldIE && this.fixed) ? 'fixed' : 'absolute',
                'width': '100%',
                'height': '100%',
                'top': 0,
                'left': 0
            },
            cssPanel = K.mix({}, css, {
                'z-index': this.zIndex || 550,
                'display': 'none'
            }),
            cssMask = K.mix({}, css, {
                'position': !isOldIE ? 'fixed' : 'absolute',
                'background': this.background,
                'opacity': this.opacity,
                'z-index': -1
            }),
            cssContent = K.mix({}, css, {
                'z-index': 0
            }, (!isOldIE && this.fixed) ? { 'overflow-x': 'hidden', 'overflow-y': 'hidden'} : { 'overflow': 'visible' });

            this._panel = $('<div id="' + this.id + '" class="_j_layer">\
                            <div class="_j_layer_mask"></div>\
                            <div class="_j_layer_content" data-is-container="1"></div>\
                            </div>')
                            .css(cssPanel)
                            .appendTo('body');
                            this._mask = this._panel.children('._j_layer_mask').css(cssMask);
                            this._content = this._panel.children('._j_layer_content').css(cssContent);
                            layerId++;
        },

        'setFixed': function(fixed) {
            fixed = !!fixed;
            if (this.fixed != fixed) {
                this.changeFixed = true;
                this.fixed = fixed;
                if (!isOldIE && this.fixed) {
                    this._panel.css('position', 'fixed');
                    this._content.css({ 'position': 'fixed', 'overflow-x': 'hidden', 'overflow-y': 'hidden' });
                } else {
                    this._panel.css('position', 'absolute');
                    this._content.css({ 'position': 'absolute', 'overflow-x': '', 'overflow-y': '', 'overflow': 'visible' });
                }
            } else {
                this.changeFixed = false;
            }
        },

        /**
        * 新建Layer上的层叠，返回当前层叠的序号
        */
        'newStack': function(panel) {
            var stack = $(panel).appendTo(this._content);
            this.stacks.push(stack);
            return this.stacks.length - 1;
        },

        'getStack': function(stackId) {
            return this.stacks[stackId];
        },

        'getActiveStack': function() {
            return this.stacks[this.activeStackId];
        },

        'setActiveStack': function(dialog) {
            this.activeStackId = dialog.stackId;
            this.frozenBackground = dialog.frozenBackground;
        },

        'getPanel': function() {
            return this._panel;
        },

        'getMask': function() {
            return this._mask;
        },

        'show': function(callback) {
            var me = this;
            if (this.visible) {
                typeof callback === 'function' && callback();
                return;
            }

            Layer.activeId = this.id;
            this.visible = true;

            if (this.frozenBackground) {
                $('html').addClass('frosen');
                $('#head').css("width", $(window).width() - offsetRight);

                this._panel.show();
                typeof callback === 'function' && callback();
            }
            else {
                if (isOldIE) {
                    var height = document.documentElement && document.documentElement.scrollHeight || document.body.scrollHeight;
                    this._panel.css('height', height);
                    this._mask.css('height', height);
                }
                this._panel.fadeIn(200, function() {
                    typeof callback === 'function' && callback();
                });
            }
        },

        'hide': function(callback) {
            var me = this;
            if (!this.visible) {
                typeof callback === 'function' && callback();
                return;
            }

            this.visible = false;

            if (this.frozenBackground) {
                this._panel.hide();
                typeof callback === 'function' && callback();
                me._recoverTopScroller();
                $('html').removeClass('frosen');
                $('#head').css("width", "100%");
            }
            else {
                if (isOldIE) {
                    this._panel.css('height', '');
                    this._mask.css('height', '');
                }

                this._panel.fadeOut(200, function() {
                    typeof callback === 'function' && callback();
                    me._recoverTopScroller();
                });
            }
        },

        'setOverFlow': function(overflow) {
            this.overflow = overflow;
            if (overflow) {
                if (!isOldIE && this.fixed) {
                    this._hideTopScroller();
                    this._content.css('overflow-y', 'auto');
                }
            } else {
                this._recoverTopScroller();
                if (!isOldIE && this.fixed) {
                    this._content.css('overflow-y', 'hidden');
                }
            }
        },

        '_hideTopScroller': function() {
            return;
            if (isIE7) {
                $('html').css('overflow', 'hidden');
            } else if (!isOldIE) {
                $('body').css('overflow', 'hidden');
            } else {
                $('body').css('overflow-x', 'hidden');
                this._panel.height($(document).height() + 20);
            }
        },

        '_recoverTopScroller': function() {
            return;
            if (isIE7) {
                $('html').css('overflow', '');
            } else if (!isOldIE) {
                $('body').css('overflow', '');
            } else {
                $('body').css('overflow-x', '');
            }
        },

        'destroy': function() {
            this.hide($.proxy(function() {

                this._panel && this._panel.undelegate();
                this._panel && this._panel.remove();
                this._panel = null;
                if (K.indexOf(Layer[this.impl], this.id) != -1) {
                    Layer[this.impl].splice(K.indexOf(Layer[this.impl], this.id), 1);
                }
                delete Layer.instances[this.id];
            }, this));
        },

        'clear': function() {
            this._content.empty();
            this.stacks = [];
            this.activeStackId = null;
        },

        '_insertCss': function() {
            var style = '' +
                '.frosen{' +
                'margin-right: ' + offsetRight + 'px;' +
                '}' +
                '.frosen body{' +
                'overflow: hidden;' +
                '}' +
                '.frosen ._j_layer{' +
                'overflow-y: scroll;' +
                '}' +
                '.frosen ._j_layer_mask{' +
                'overflow-y: scroll;' +
                '}';

            var ie = parseInt(K.Browser.ie, 10);

            if (ie) {
                style +=
                    '.frosen{' +
                    'overflow: hidden;' +
                    '}' +
                    '.frosen ._j_layer_mask{' +
                    'overflow-y: scroll;' +
                    '}';
            }

            if (ie < 9) {
                /*加背景图防止在IE某些版本下scroll无效*/
                /*IE9以下版本滤镜半透明*/
                style +=
                    '.frosen ._j_layer_mask{' +
                    'background:url(http://' + K.data.getBaseInfo('s_host') + '/i/blank.png) no-repeat;' +
                '-ms-filter:"progid:DXImageTransform.Microsoft.gradient' +
                    '(startColorstr=#33000000, endColorstr=#33000000)";' +
                    'filter:progid:DXImageTransform.Microsoft.gradient' +
                    '(startColorstr=#33000000, endColorstr=#33000000)' +
                    '}';
            }
            else if (ie >= 9) {
                /*IE9下需要加，否则渲染会出问题*/
                style +=
                    '.frosen ._j_layer_mask{' +
                    'opacity: 0.9999;' +
                    '}';
            }

            var styleNode = document.createElement('style');
            styleNode.type = 'text/css';

            if (styleNode.styleSheet) {
                styleNode.styleSheet.cssText = style;
            }
            else {
                styleNode.appendChild(document.createTextNode(style));
            }
            document.getElementsByTagName('head')[0].appendChild(styleNode);
        }
    };

    Layer.instances = {};
    Layer.activeId = null;
    Layer.getInstance = function(id) {
        return Layer.instances[id];
    };
    Layer.getActive = function(impl) {
        var active = Layer.getInstance(Layer.activeId);
        if (impl && active) {
            active = active.impl === impl ? active : null;
        }
        return active;
    }
    Layer.getImplInstance = function(impl) {
        var instance = Layer.getActive(impl);
        if (!instance && K.isArray(Layer[impl]) && Layer[impl].length) {
            instance = Layer.getInstance(Layer[impl][Layer[impl].length - 1]);
        }
        return instance;
    };

    Layer.closeActive = function() {
        var activeLayer = Layer.getActive();
        if (activeLayer && activeLayer.getActiveStack()) {
            activeLayer.getActiveStack().trigger('whenESC');
        }
    };

    // 退出统一处理
    $(document).keyup(function(ev) {
        if (ev.keyCode == 27) {
            Layer.closeActive();
        }
    });

    // 销毁layer所有对象，防止内存泄露
    $(document).unload(function() {
        K.forEach(Layer.instances, function() {
            layer.destroy();
        });
    });

    return Layer;

});
