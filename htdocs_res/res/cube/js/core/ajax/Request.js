"use strict";
/**
*   working with MCore_Web_BaseAjaxApp
*
*   succ :  {ok: true, data: {...}, ... }
*
*   error:  {error: true, errorMsg: "...", ...}
*/
define('core/ajax/Request', ['core/jQuery', 'core/URI'] , function(require) {

    var dataCache = {};

    var $ = require('core/jQuery');
    var URI = require('core/URI');

    var Request = function(url, uniqueId, method) {

        if (!uniqueId) uniqueId = 0;
        if (!method) method = 'POST';

        this._uniqueKey = url + "_" + uniqueId;
        this.url = url;
        this.method = method;
        this.data = {};
        this.xhr = null;
    };

    K.mix(Request, {

        lockObj: {},

        formatUrl: function(url) {
            return url;
        },

        create: function(url, uniqueId, method) {
            url = this.formatUrl(url);

            // check lock
            if (!uniqueId) uniqueId = 0;
            var key = url + "_" + uniqueId;
            if (this.lockObj[key]) {
                return false;
            }

            // lock
            this.lockObj[key] = 1
            var request = new Request(url, uniqueId, method);
            return request;
        }
    });

    K.mix(Request.prototype, {

        _loadingElement: null,

        setLoadingElement: function(element) {
            this._loadingElement = element;
        },

        send: function() {
            var cacheData = dataCache[this._uniqueKey];
            if (cacheData) {
                this.dispatchResponse(cacheData);
                return this;
            }
            else {
                var me = this;
                this.xhr = $.ajax({
                    type: this.method,
                    url: this.url,
                    data: this.data,
                    dataType: 'json'
                }).success(function(ret) {
                    if (!ret.error && ret.ok) {
                        me.invokeResponseHandler(ret);
                    } else {
                        me.invokeErrorHandler(ret);
                    }
                }).error(function(ret) {
                    me.invokeErrorHandler(ret);
                });
                return this;
            }
        },

        unlockRequest: function() {
            delete Request.lockObj[this._uniqueKey];
        },

        setMethod: function(method) {
            this.method = method;
            return this;
        },

        setData: function(data) {
            data['forajax'] = 1;
            this.data = data;
            return this;
        },

        setHandler: function(handler) {
            if (K.isFunction(handler)) {
                this.handler = handler;
            }
            return this;
        },

        getHandler: function() {
            return this.handler;
        },

        setErrorHandler: function(errorHandler) {
            this.errorHandler = errorHandler;
            return this;
        },

        setRelativeTo: function(element) {
            this.relativeTo = element;
            return this;
        },

        getRelativeTo: function() {
            return this.relativeTo;
        },

        abort: function() {
            this.xhr && this.xhr.abort();
            return this;
        },

        invokeResponseHandler: function(ret) {
            if ('resource' in ret) {
                this.loadResource(ret.resource, K.bind(function() {
                    this.dispatchResponse(ret);
                }, this));
            } else {
                this.dispatchResponse(ret);
            }
        },

        invokeErrorHandler: function(ret) {
            this.dispatchErrorResponse(ret);
        },

        loadResource: function(resource, callback) {
            if ('depends' in resource && !K.isEmpty(resource.depends)) {
                K.Resource.addResourceDepends(resource.depends);
            }
            if ('resourceMap' in resource && !K.isEmpty(resource.resourceMap)) {
                K.Resource.addResourceMapViaKxVersion(resource.resourceMap);
            }
            if ('css' in resource && !K.isEmpty(resource.css)) {
                K.Resource.loadCSS(resource.css, callback);
            } else {
                callback();
            }
            if ('js' in resource && !K.isEmpty(resource.js)) {
                K.Resource.loadViaKxVersion(resource.js);
            }
            var me = this;
            if ('onloads' in resource) {
                setTimeout(function() {
                    K.forEach(resource.onloads, function(onload) {
                        (new Function(onload)).apply(me);
                    });
                });
            }
        },

        dispatchResponse: function(ret) {

            // TOOD: cache by time
            if (ret.cacheTime) {
                dataCache[this._uniqueKey] = ret;
            }

            // pop dialog
            if (ret.data.pop_dialog) {

                var dialog = ret.data.pop_dialog;
                require.async('core/dialog/MsgBox', function(MsgBox) {
                    var msg = dialog.msg;
                    var type = dialog.type;

                    var data = {auto_close: dialog.auto_close};
                    if (type == 'succ') {
                        MsgBox.success(msg, data);
                    } else {
                        MsgBox.error(msg, data);
                    }
                });
                if (dialog.block_handler) {
                    return;
                }
            }

            // unlock
            this.unlockRequest();

            if (this.handler) {
                this.handler(ret);
            }
        },

        dispatchErrorResponse: function(ret) {

            // unlock
            this.unlockRequest();

            if (this.errorHandler) {
                var processed = this.errorHandler(ret);
                if (processed)
                    return;
            }

            var msg = '';
            var width = 650;

            // php exception
            if (ret.errorMsg && ret.error) {
                msg = ret.errorMsg;
                this.showError(msg);

            } else {

                width = 1024;
                msg += ret.status + "<br/>";
                msg += ret.statusText + "<br/>";
                msg += ret.responseText;
                this.showError(msg);
            }
        },

        showError: function(msg) {
            require.async('core/dialog/MsgBox', function(MsgBox) {

                var data = {};
                if (msg.length > 1000) {
                    data.width = 960;
                }
                MsgBox.error(msg, data);
            });

        },
    });

    return Request;
});
