define('core/dialog/MsgBox', ['core/dialog/AsyncDialog'], function(require) {

    var $ = require('core/jQuery'),
    AsyncDialog = require('core/dialog/AsyncDialog');

    var MsgBox = function(options) {

        this.dialog_data = null;
        this.ok_callback = null;
        this.cancel_callback = null;
        this.close_callback = null;
        this.dismiss_callback = null;

        MsgBox.$super.call(this, options);
        // K.__callsuper(MsgBox, this, options);
    };

    MsgBox = K.extend(MsgBox, AsyncDialog);

    K.mix(MsgBox.prototype, {

        close: function(){
            this.close_callback && this.close_callback();
            this.___close();
        },

        ___close: function() {
            this.dismiss_callback && this.dismiss_callback();
            MsgBox.$super.prototype.close.call(this);
        },

        _bindEvents: function() {
            MsgBox.$super.prototype._bindEvents.call(this);

            var me = this;

            this._panel.delegate('._j_msgbox_btn_uni', 'click', function(ev) {
                var title = $(ev.target).html();
                var callback = me.dialog_data.buttons[title];
                callback && callback();
                me.___close();
            });

            this._panel.delegate('._j_msgbox_btn_ok', 'click', function(ev) {
                me.ok_callback && me.ok_callback();
                me.___close();
            });

            this._panel.delegate('._j_msgbox_btn_cancel', 'click', function(ev) {
                me.cancel_callback && me.cancel_callback();
                me.___close();
            });
        },
    });

    K.mix(MsgBox, {

        info: function(msg, data) {
            var dialog_body ='<div class="modal-body">' +
                '<div class="">' + msg + '</div></div>' +
                '<div class="modal-footer">' +
                '<button class="btn btn-success _j_msgbox_btn_ok">Yes</button>' +
                '</div></div>';
            return this.showDialog(dialog_body, data);
        },

        succ: function(msg, data) {
            return this.success(msg, data);
        },

        success: function(msg, data) {
            var dialog_body ='<div class="modal-body">' +
                '<div class="alert alert-success">' + msg + '</div></div>' +
                '<div class="modal-footer">';

            if (data.buttons) {
                K.forEach(data.buttons, function(callback, title) {
                    dialog_body += '<button class="btn btn-success _j_msgbox_btn_uni">' + title + '</button>';
                });
            } else {
                dialog_body +='<button class="btn btn-success _j_msgbox_btn_ok">Yes</button>';
            }
            dialog_body += '</div></div>';

            return this.showDialog(dialog_body, data);
        },

        confirm: function(msg, data) {

            var dialog_body ='<div class="modal-body">' +
                '<div class="alert alert-warning">' + msg + '</div></div>' +
                '<div class="modal-footer">' +
                '<button class="btn btn-danger _j_msgbox_btn_ok">Yes</button>' +
                '<button class="btn _j_msgbox_btn_cancel">Cacel</button>' +
                '</div></div>';
            this.showDialog(dialog_body, data);
        },

        /**
        * on_ok
        * on_close
        * auto_close
        * on_cancel
        * on_auto_close
        * on_dismiss
        * width
        * height
        */
        error: function(msg, data) {

            var dialog_body ='<div class="modal-body">' +
                '<div class="alert alert-danger alert-error">' + msg + '</div></div>' +
                '<div class="modal-footer">' +
                '<button class="btn btn-primary _j_msgbox_btn_ok">OK</button>' +
                '</div></div>';

            this.showDialog(dialog_body, data);
        },

        showDialog: function(body, data) {

            data = data || {};

            var options = {};
            options.width = data.width || 500;
            options.height = data.height ||'auto';
            options.body = body;
            var dialog = new MsgBox(options);
            dialog.show();

            dialog.dialog_data = data;
            dialog.cancel_callback = data.on_cancel;
            dialog.ok_callback = data.on_ok;
            dialog.close_callback = data.on_close;
            dialog.dismiss_callback = data.on_dismiss;

            if (data.auto_close)
                dialog.autoClose(data.auto_close, data.on_auto_close);
        }
    });

    return MsgBox;
});
