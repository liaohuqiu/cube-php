define('core/ajax/Dialog', [ 'core/jQuery', 'core/ajax/Request', 'core/panel/Dialog' ], function(require) {

    var $ = require('core/jQuery'),
        Request = require('core/ajax/Request'),
        PanelDialog = require('core/panel/Dialog');

    var AsyncDialog = function( opts ) {
        opts = K.mix({
            PANEL_CLASS: 'dialog',
            height: 'auto',
            useIframe: false,
            disp: true // 是否允许被自动销毁
        }, opts);
        AsyncDialog.$super.call(this, opts );

        this.getPanel().delegate('[data-dialog-button]', 'click', K.bind(this._onButton, this));

        // 为普通dialog的展开和关闭添加动画效果
        this._initDialogSwitchAnimate();
    };

    K.extend(AsyncDialog, PanelDialog);

    K.mix(AsyncDialog, {
        _stack: [],

        _generateID: function() {
            return K.uniqueId("__dialog__");
        },

        _push: function(instance) {
            var current = AsyncDialog.getCurrent();

            if (current && current != instance && current.visible) {
                if(current.disp) {
                    current.destroy();
                } else {
                    current.hide();
                }
            }

            AsyncDialog._stack = K.without(AsyncDialog._stack, instance);
            AsyncDialog._stack.push(instance);
        },

        getCurrent: function() {
            var stack = AsyncDialog._stack;
            return stack.length ? stack[stack.length - 1] : null;
        },

        destroy: function(instance) {
            AsyncDialog._stack = K.without(AsyncDialog._stack, instance);
        },

        create: function() {
            return new AsyncDialog({
                closeTPL: '<a title="关闭" class="dialog_close" data-dialog-button="close" href="#"></a>'
            });
        },

        load: function(href, element) {
            var $element = $(element),
                method = $element.attr('data-ajax') == 'dialog-post' ?
                    'POST' : 'GET';

            var loadingElement = $element.find('._j_loading_elem');
            if (! loadingElement.length) {
                loadingElement = $element;
            }

            if (loadingElement.hasClass('loading')) {
                return;
            }

            var request = new Request()
                .setMethod(method)
                .setURI(href)
                .setLoadingElement(loadingElement)
                .setRelativeTo(element);

            return AsyncDialog.create().setAsyncRequest(request);
        },

        _closeDialogWhenEscape: function(event) {
            if (event.keyCode == 27) {
                var dialog = AsyncDialog.getCurrent();
                if (dialog && dialog.visible) {
                    if(dialog.disp) {
                        dialog.destroy();
                    } else {
                        dialog.hide();
                    }
                }
            }
        },

        _closeDialogWhenUnload: function(event) {
            var dialog = AsyncDialog.getCurrent();
            if (dialog) {
                dialog.destroy();
            }
        },

        _handlerFactory: function(instance, HandlerBase, metaData, request) {
            var handler = K.create(HandlerBase);
            K.mix(handler, {
                container: 'div[data-dialog-id="' + instance._dialogId + '"]',
                getRequest: function() {
                    return request;
                },
                getMetaData: function() {
                    return instance.metaData;
                },
                getDialog: function() {
                    return instance;
                }
            });
            return handler;
        }
    });

    K.mix(AsyncDialog.prototype, {
        _initDialogSwitchAnimate: function() {
            this.on('beforeshow', K.bind(this._beforeShow, this));
        },

        _beforeShow: function() {
            var prevDialog = AsyncDialog.getCurrent();
            if (prevDialog && prevDialog != this && prevDialog.visible) {
                this.atCenter = false;

                var prevRect = prevDialog.getRect(),
                    prevHeight = prevDialog.getBody().height();

                this.once('aftershow', K.bind(function() {
                    var currHeight = this.getBody().height();
                    this.getPanel().css({
                        top: prevRect.top,
                        left: prevRect.left
                    });
                    /**
                    this.getBody()
                        .css('height', prevHeight)
                        .animate({ height: currHeight }, function() {
                            $(this).css('height', 'auto');
                        });
                    */
                }, this));
                this._newDlg = false;
            } else {
                this._newDlg = true;
            }
            /*else if (!prevDialog || !prevDialog.visible) {
                this.getPanel().addClass('dialog_zoom');
                this.once('aftershow', K.bind(function() {
                    this.getPanel().addClass('dialog_run');

                    // 解决chrome下展开窗口滚动条在使用了css3动画后不起作用的bug
                    setTimeout(K.bind(function() {
                        this.getPanel().removeClass('dialog_zoom dialog_run');
                    }, this), 450);
                }, this));
                this.once('afterhide', K.bind(function() {
                    this.getPanel().removeClass('dialog_zoom dialog_run');
                }, this));
            }
            */
        },

        autoClose: function(delay, callback) {
            delay = delay || 2000;
            var me = this;
            return setTimeout(function() {
                me.getPanel().fadeOut(200, function() {
                    me.destroy();
                    if (callback) {
                        callback();
                    }
                });
            }, delay);
        },

		autoClosePanel: function(delay, callback) {
            delay = delay || 2000;
            var me = this;
            return setTimeout(function() {
                me.getPanel().fadeOut(200, function() {
                    if (callback) {
                        callback();
                    }
                });
            }, delay);
        },

        show: function() {
            AsyncDialog.$super.prototype.show.call(this);
            AsyncDialog._push(this);
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

        destroy: function() {
            if (!this.beforeDestroy || this.beforeDestroy() !== false) {
                AsyncDialog.destroy(this);
                this.dispose();
            }
        },

        setAsyncRequest: function(request) {
            var handler = function(payload) {
                /*请求不匹配或者窗口已被关闭则不再显示*/
                if (this._async_request != request) {
                    return;
                }
                this._async_request = null;

                if (!payload || !payload.dialog) {
                    return;
                }

                var data = payload.dialog,
                    dialogId = this._dialogId,
                    isNew = false;

                if (! dialogId) {
                    dialogId = this._dialogId = AsyncDialog._generateID();
                    isNew = true;
                }

                var updateDialog = K.bind(function() {
                    K.log(data);
                    if (data.title && data.title.length) {
                        this.setTitle(data.title);
                        this.getPanel().find('.dialog_title').show();
                    } else if (isNew || (data.title === null)) {
                        this.getPanel().find('.dialog_title').hide();
                    }

                    if (data.body) {
                        if (isNew) {
                            this.setContent('<div data-dialog-id="' + dialogId + '">' + data.body + '</div>');
                        } else {
                            this.atCenter = false;
                            var prevHeight = this.getBody().height();
                            this.getPanel().find('div[data-dialog-id]').html(data.body);
                            var currHeight = this.getBody().height();
                            this.getBody().css('height', prevHeight).animate({
                                height: currHeight
                            }, function() {
                                $(this).css('height', 'auto');
                            });
                        }
                    }
                    if (data.maskClassName) {
                        this.getMask().addClass(data.maskClassName);
                    }
                    if (data.dialogClassName) {
                        this.getPanel().addClass(data.dialogClassName);
                    }

                    if (data.width) {
                        this.setSize(data.width, data.height);
                    }
                    this.show();
                }, this);

                updateDialog();

                var me = this;
                this.metaData = data.metaData;
                if (data.handler) {
                    K.App(['core/ajax/Dialog', data.handler]).define(function(require) {
                        return require('core/ajax/Dialog')._handlerFactory(me, require(data.handler), data.metaData, request);
                    });
                }
            };
            this._async_request = request;

            var originalHandler = request.getHandler() || $.noop;

            handler = K.bind(handler, this);
            request.setHandler(function(payload) {
                originalHandler(payload);
                handler(payload);
            });
            request.send();

            return this;
        },

        _onButton: function(event) {
            var currentTarget = $(event.currentTarget),
                name = currentTarget.attr('data-dialog-button');

            if (name == 'close') {
                this.destroy();
                event.preventDefault();
            } else if (name == "hide") {
                this.hide();
                event.preventDefault();
            }
        }
    });

    //$(document).keyup(K.bind(AsyncDialog._closeDialogWhenEscape, AsyncDialog));
    $(document).unload(K.bind(AsyncDialog._closeDialogWhenUnload, AsyncDialog));

    return AsyncDialog;
});
