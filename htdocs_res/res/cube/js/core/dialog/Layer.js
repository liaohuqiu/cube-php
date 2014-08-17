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
    layerId = 0,
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

        this.fixed = true;
        this.frozenBackground = false;

        this.overflow = false;
        this.changeFixed = false;

        this._contentStatck = [];
        this._content = null;

        K.mix(this, options);
        this.id = 'layer_' + layerId++;
        this.init();
    }

    Layer.prototype = {

        'init': function() {

            K.CustEvent.createEvents(this, 'mask_clicked');
            this._createPanel();
            this._bindEvents();
            this._insertCss();
        },

        '_bindEvents': function(e) {

            this._layerContainer.delegate('._j_layer_content', 'click', K.bind(function(ev) {
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

            this._layerContainer = $('<div id="' + this.id + '" class="_j_layer">\
                            <div class="_j_layer_mask"></div>\
                            <div class="_j_layer_content" data-is-container="1"></div>\
                            </div>')
                            .css(cssPanel)
                            .appendTo('body');
                            this._mask = this._layerContainer.children('._j_layer_mask').css(cssMask);
                            this._content = this._layerContainer.children('._j_layer_content').css(cssContent);
        },

        'setFixed': function(fixed) {
            fixed = !!fixed;
            if (this.fixed != fixed) {
                this.changeFixed = true;
                this.fixed = fixed;
                if (!isOldIE && this.fixed) {
                    this._layerContainer.css('position', 'fixed');
                    this._content.css({ 'position': 'fixed', 'overflow-x': 'hidden', 'overflow-y': 'hidden' });
                } else {
                    this._layerContainer.css('position', 'absolute');
                    this._content.css({ 'position': 'absolute', 'overflow-x': '', 'overflow-y': '', 'overflow': 'visible' });
                }
            } else {
                this.changeFixed = false;
            }
        },

        'addContent': function(content, frozenBackground) {
            this._content.append(content);
            this.frozenBackground = frozenBackground;
        },

        /**
        * Push content to statck
        */
        'pushToContentStack': function(content) {
            var current = this.getTopContent();
            if (undefined != current && current != content) {
                current.sendToBack();
            }
            this._contentStatck.push(content);
        },

        /**
        * get content from the top of stack
        */
        'getTopContent': function() {
            return K.last(this._contentStatck);
        },

        /**
        * Try to pop this content from the stack.
        */
        'removeFromContentStack': function(content) {
            var current = this.getTopContent();
            if (current != undefined && current == content) {
                this._contentStatck.pop();
                current = this.getTopContent();
                if (undefined != current) {
                    current.bringToFront();
                }
            } else {
                this._contentStatck.splice(K.indexOf(this._contentStatck, content), 1);
            }
        },

        'getMask': function() {
            return this._mask;
        },

        'show': function(callback) {
            var me = this;
            Layer.pushToActvieStatck(this.id);
            if (this.visible) {
                typeof callback === 'function' && callback();
                return;
            }

            this.visible = true;

            if (this.frozenBackground) {
                $('html').addClass('frosen');
                $('#head').css("width", $(window).width() - offsetRight);

                this._layerContainer.show();
                typeof callback === 'function' && callback();
            }
            else {
                if (isOldIE) {
                    var height = document.documentElement && document.documentElement.scrollHeight || document.body.scrollHeight;
                    this._layerContainer.css('height', height);
                    this._mask.css('height', height);
                }
                this._layerContainer.fadeIn(200, function() {
                    typeof callback === 'function' && callback();
                });
            }
        },

        'hide': function(callback) {
            var me = this;
            Layer.removeFromActiveStack(this.id);
            if (!this.visible) {
                typeof callback === 'function' && callback();
                return;
            }

            this.visible = false;

            if (this.frozenBackground) {
                this._layerContainer.hide();
                typeof callback === 'function' && callback();
                me._recoverTopScroller();
                $('html').removeClass('frosen');
                $('#head').css("width", "100%");
            }
            else {
                if (isOldIE) {
                    this._layerContainer.css('height', '');
                    this._mask.css('height', '');
                }

                this._layerContainer.fadeOut(200, function() {
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
                this._layerContainer.height($(document).height() + 20);
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

                this._layerContainer && this._layerContainer.undelegate();
                this._layerContainer && this._layerContainer.remove();
                this._layerContainer = null;
                if (K.indexOf(Layer.implCache[this.impl], this.id) != -1) {
                    Layer.implCache[this.impl].splice(K.indexOf(Layer.implCache[this.impl], this.id), 1);
                }
                delete Layer.instances[this.id];
            }, this));
        },

        'clear': function() {
            this._content.empty();
            this._contentStatck = [];
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
    Layer.implCache = {};

    Layer.activeStack = [];

    Layer.want = function(options, impl, createNew) {
        var layer;
        if (createNew) {
            layer = Layer.createNew(options, impl);
        } else {
            layer = Layer.currentOrLast(impl) || Layer.createNew(options, impl);
        }
        return layer;
    },

    Layer.createNew = function(options, impl) {
        options.impl = impl;
        var layer = new Layer(options);
        if (!Layer.implCache[impl]) {
            Layer.implCache[impl] = [];
        }
        Layer.implCache[impl].push(layer.id);
        Layer.instances[layer.id] = layer;
        return layer;
    },

    Layer.getInstance = function(id) {
        return Layer.instances[id];
    };

    Layer.pushToActvieStatck = function(id) {
        Layer.removeFromActiveStack(id);
        Layer.activeStack.push(id);
    },

    Layer.removeFromActiveStack = function(id) {
        Layer.activeStack.splice(K.indexOf(Layer.activeStack, id), 1);
    }

    Layer.getActive = function() {
        var id = K.last(Layer.activeStack);
        var active = Layer.getInstance(id);
        return active;
    }

    Layer.currentOrLast = function(impl) {
        var instance = Layer.getActive();
        if (instance && instance.impl == impl) {
            return instance;
        }
        if (K.isArray(Layer.implCache[impl]) && Layer.implCache[impl].length) {
            instance = Layer.getInstance(Layer.implCache[impl][Layer.implCache[impl].length - 1]);
        }
        return instance;
    };

    // process close for ESC
    $(document).keyup(function(ev) {
        if (ev.keyCode == 27) {
            var activeLayer = Layer.getActive();
            if (activeLayer && activeLayer.getTopContent()) {
                activeLayer.getTopContent().whenESC();
            }
        }
    });

    // destroy all layers
    $(document).unload(function() {
        K.forEach(Layer.instances, function() {
            layer.destroy();
        });
    });

    return Layer;

});
