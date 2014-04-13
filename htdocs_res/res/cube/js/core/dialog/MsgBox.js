define('core/dialog/MsgBox', ['core/dialog/AsyncDialog'], function(require) {

    var $ = require('core/jQuery'),
    AsyncDialog = require('core/dialog/AsyncDialog');

    var MsgBox = function(options) {

        MsgBox.$super.call(this, options);
        // K.__callsuper(MsgBox, this, options);
    };

    MsgBox = K.extend(MsgBox, AsyncDialog);

    K.mix(MsgBox.prototype, {

        ok_callback: null,
        cancel_callback: null,
        close_callback: null,

        close: function(){
            this.close_callback && this.close_callback();
            this.___close();
        },

        ___close: function() {
            MsgBox.$super.prototype.close.call(this);
        },

        _bindEvents: function() {
            MsgBox.$super.prototype._bindEvents.call(this);

            var me = this;
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

        confirm: function(msg, ok, cancel, close, auto_close, auto_close_callback) {

            var dialog_body ='<div class="modal-body">' +
                '<div class="alert alert-warning">' + msg + '</div></div>' +
                '<div class="modal-footer">' +
                '<button class="btn btn-danger _j_msgbox_btn_ok">Yes</button>' +
                '<button class="btn _j_msgbox_btn_cancel">Cacel</button>' +
                '</div></div>';

            var options = {};
            options.width = 500;
            options.height = 'auto';
            options.body = dialog_body;
            var dialog = new MsgBox(options);
            dialog.show();

            dialog.cancel_callback = cancel;
            dialog.ok_callback = ok;
            dialog.close_callback = close;

            if (auto_close)
                dialog.autoClose(auto_close, auto_close_callback);
        },

        error: function(msg, ok, close, auto_close, auto_close_callback) {

            var dialog_body ='<div class="modal-body">' +
                '<div class="alert alert-danger alert-error">' + msg + '</div></div>' +
                '<div class="modal-footer">' +
                '<button class="btn btn-primary _j_msgbox_btn_ok">OK</button>' +
                '</div></div>';

            var options = {};
            options.width = 500;
            options.height = 'auto';
            options.body = dialog_body;
            var dialog = new MsgBox(options);
            dialog.show();

            dialog.ok_callback = ok;
            dialog.close_callback = close;

            if (auto_close)
                dialog.autoClose(auto_close, auto_close_callback);
        },
    });

    return MsgBox;
});
