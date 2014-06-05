K.App('admin/ATableEdit', ['core/ajax/Request']).define(function(require) {
    var Request = require('core/ajax/Request');
    var App = {

        events: {
            'click #j_btn_delte': 'clickDeleteTable',
            'click #j_btn_alter': 'clickAlter',
            'click #j_btn_get_info': 'clickGetInfo',
        },

        clickDeleteTable: function(e) {
            var kind = $('#table_kind').val();
            var msg = 'Are you sure to delete this table: ' + kind + ' ?';
            var that = this;
            require.async('core/dialog/MsgBox', function(MsgBox) {
                MsgBox.confirm(msg, {on_ok: function() {
                    var data = {};
                    data['kind'] = kind;
                    var request = Request.create('table-delete-ajax');
                    if (!request)
                        return;
                    request.setData(data).setHandler(function(ret) {
                        that.showResult(ret);
                    }).send();
                }});
            });
        },

        clickGetInfo: function() {
            var kind = $('#table_kind').val();
            var data = {};
            data['kind'] = kind;
            data['cmd'] = 'get-info';
            this.requestEdit(data);
        },

        clickAlter: function() {
            var kind = $('#table_kind').val();
            var data = {};
            data['kind'] = kind;
            data['cmd'] = 'alter';
            data['sql'] = $('#sql').val();
            this.requestEdit(data);
        },

        requestEdit: function(data) {
            var that = this;
            var request = Request.create('table-edit-ajax');
            if (!request)
                return;
            request.setData(data).setHandler(function(ret) {
                that.showResult(ret);
            }).send();
        },

        showResult: function(ret) {
            $('#result').html(ret.data.msg);
        },

        main: function(){
        },

    };
    return App;
});
