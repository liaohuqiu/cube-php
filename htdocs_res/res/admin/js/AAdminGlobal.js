K.App('admin/AAdminGlobal', ['core/dialog/AsyncDialog', 'core/dialog/MsgBox', 'core/ajax/Request']).define(function(require) {
    var Request = require('core/ajax/Request');
    var AsyncDialog = require('core/dialog/AsyncDialog');
    var MsgBox = require('core/dialog/MsgBox');
    var App = {

        events: {
            'click #_j_btn_change_pwd': 'clickChangePwd',
            'click #_j_btn_logout': 'clickLogout',
        },

        clickChangePwd: function() {
            AsyncDialog.open('/admin/user-change-pwd-dialog');
        },

        clickLogout: function() {
            var msg = 'Are you sure to logout?';
            MsgBox.confirm(msg, {on_ok: function() {
                var request = Request.create('/admin/user-logout-ajax');
                if (!request)
                    return;
                var that = this;
                request.setHandler(function(ret) {
                }).send();
            }});
        },

        main: function(){
        },

    };
    return App;
});
