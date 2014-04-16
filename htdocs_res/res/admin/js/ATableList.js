K.App('admin/ATableList', ['core/ajax/Request']).define(function(require) {
    var Request = require('core/ajax/Request');
    var App = {

        events: {
            'click ._j_table_delete': 'clickDeleteTable',
        },

        clickDeleteTable: function(e) {
            var a_link = $(e.target);
            var kind = a_link.data('kind');
            var msg = 'Are you sure to delete this table: ' + kind + ' ?';
            require.async('core/dialog/MsgBox', function(MsgBox) {
                MsgBox.confirm(msg, {on_ok: function() {

                    var data = {};
                    data['kind'] = kind;
                    var request = Request.create('table-delete-ajax');
                    if (!request)
                        return;
                    var that = this;
                    request.setData(data).setHandler(function(ret) {
                        a_link.parents('tr').remove();
                    }).send();
                }});
            });
        },

        main: function(){
        },

    };
    return App;
});
