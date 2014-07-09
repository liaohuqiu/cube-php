define('core/dialog/AsyncDialog', ['core/ajax/Request', 'core/dialog/DialogBase'], function(require) {

    var $ = require('core/jQuery'),
    Request = require('core/ajax/Request'),
    DialogBase = require('core/dialog/DialogBase');

    var AsyncDialog = function(options) {

        this.options = options || {};

        K.CustEvent.createEvents(this, 'after_async_show');
        AsyncDialog.$super.call(this, options);

        this._updateRequest = null; // 异步更新自身用
    };

    AsyncDialog = K.extend(AsyncDialog, DialogBase);

    K.mix(AsyncDialog.prototype, {

        options: null,
        _dialogContent: null,
        _close: null,

        _dialogTitle: null,
        _dialogBody: null,
        _dialogContainer: null,

        drawDialogContent: function() {

            var close = this.options.closeElement || '<button type="button" class="close _j_dialog_close" data-dismiss="modal" aria-hidden="true">&times;</button>';
            this._close = $(close);

            var dialogHead = $('<div class="hd modal-header _j_dialog_header"></div>');
            this._dialogTitle = $('<h3></h3>');
            dialogHead.append(this._close);
            dialogHead.append(this._dialogTitle);

            if (this.options.body) {
                this._dialogBody = $(this.options.body);
            } else {
                this._dialogBody = $('<div class="modal-body"></div>');
            }

            this._dialogContent = $('<div class="cube-dialog"></div>');
            this._dialogContent.append(dialogHead);
            this._dialogContent.append(this._dialogBody);

            var panel = this.getPanel();
            panel.append(this._dialogContent);

            if (this.options.noTitle) {
                this.hideTitle();
            } else {
                var title = this.options.title || "";
                this.setTitle(title);
            }

            if (this.options.content) {
                this.setBodyContent(this.options.content);
            }

            this.setPosition({ width: this.options.width, height: this.options.height }, 0);

            this.on('afterdestroy', $.proxy(function() {
                this._updateRequest = null;
            }, this));
        },

        hideTitle: function() {
            this._dialogTitle.parent().css('display', 'none');
        },

        setTitle: function(title) {
            this._dialogTitle.text(title).parent().css('display', '');
            this.title = title;
        },

        getTitle: function() {
            return this.title;
        },

        setBodyContent: function(content) {
            this._dialogBody.html(content);
        },

        replaceBodyContent: function(content) {
            this._dialogBody.remove();
            this._dialogBody = $(content);
            this._dialogContent.append(this._dialogBody);
        },

        autoClose: function(delay, callback) {
            callback = typeof callback === 'function' ? callback : $.noop;
            setTimeout($.proxy(function() {
                this.close(callback);
            }, this), (delay || 2000));
        },

        getClose: function() {
            return this._close;
        },

        requestNewDialog: function(request) {
            var me = this;
            request.setHandler(function(ret) {
                if (!ret || !ret.data.dialog || me.destroyed) {
                    return;
                }
                me.setRequestDialog(ret, request);
            }).setErrorHandler(function(ret) {
                me.close();
            }).send();
        },

        setRequestDialog: function(ret, request) {

            var me = this;

            var data = ret.data.dialog;

            if (data.noTitle) {
                this.hideTitle();
            } else {
                this.setTitle(data.title);
            }
            if (data.body) {
                this.replaceBodyContent(data.body);
            }
            if (data.dialogProperty) {
                K.mix(this, data.dialogProperty);
            }
            if (typeof data.fixed !== 'undefined') {
                this.setFixed(data.fixed);
            }

            if (data.width) {
                this.setPosition({ width: data.width, height: "auto" });
            }

            this.fire('after_async_show');

            // 其他App handler处理
            this.metaData = data.metaData;
            if (data.handler) {
                this.setHandler(data.handler);
            }
        },

        setHandler: function (handlerMod) {
            var me = this;
            K.App(['core/dialog/AsyncDialog', handlerMod]).define(function(require) {
                var handler = K.create(require(handlerMod));
                K.mix(handler, {
                    container: me._panel,
                    getRequest: function() {
                        return request;
                    },
                    metaData: me.metaData,
                    getDialog: function() {
                        return me;
                    }
                });
                me.on('afterdestroy', function() {
                    handler = null;
                });
                return handler;
            });

        },

        setAsyncRequest: function() {
            this.requestUpdateDialog.apply(this, Array.prototype.slice.call(arguments));
        },

        requestUpdateDialog: function(request) {
            var me = this,
            originalHandler = request.getHandler() || $.noop;

            this._updateRequest = request;
            request.setHandler(function(ret) {
                // 其他已绑定handler处理
                originalHandler(ret);

                // 有效性检测
                if (me._updateRequest !== request || me.destroyed) { // 请求被反复，或是Dialog被销毁
                    return;
                }

                if (!ret || !ret.data || !ret.data.dialog) { // 数据格式验证
                    return;
                }

                me.setRequestDialog(ret, request);

            }).send();
        }
    });

    $.extend(AsyncDialog, DialogBase);

    K.mix(AsyncDialog, {

        defaultLoading: '<div style="padding: 15px 0 0 0; height: 30px; text-align: center;">' +
            '<img src="' + K.Resource.getResPrePath() + '/cube/i/base/loading.gif">' +
            '</div>',

        /**
        * noTitle, width, height, loadingContent, closeElement, noLoading
        *
        */
        open: function(href, requestData, opts, dialogOptions) {
            if (!href) {
                return;
            }
            var request = Request.create(href, 1);
            if (!request) return;

            if (!requestData) requestData = {};
            request.setMethod("POST").setData(requestData);

            opts = opts || {};
            var options = {};
            options.noTitle = opts.noTitle;
            options.width = opts.width || 350;
            options.height = opts.height || 'auto';
            options.closeElement = opts.closeElement;
            options.content = opts.loadingContent || AsyncDialog.defaultLoading;

            K.mix(options, dialogOptions);

            var show = !opts.noLoading;
            var dialog = new AsyncDialog(options);
            if (show)
                dialog.show();
            dialog.requestNewDialog(request);
            return dialog;
        },

        load: function(href, element) {
            element = $(element);
            var data = {};
            var opts = {};
            opts.noLoading = element.data("ajax-no-loding");
            return this.open(href, data, opts);
        }
    });

    return AsyncDialog;
});
