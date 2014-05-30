K.App('admin/ATableQuery', ['core/ajax/Request']).define(function(require) {
    var Request = require('core/ajax/Request');
    var App = {

        events: {
            'click #_j_id_btn_query' : 'clickQuery',
        },

        clickQuery: function(e) {

            var kind = $("#_j_id_input_kind").val();
            var sql = $("#_j_id_input_sql").val();
            var msg = $('#_j_id_msg');
            if (!kind || !sql || kind.length == 0 || sql.length == 0) {
                msg.html("kind can not be empty");
                return;
            }
            var request = Request.create('table-query-ajax');

            var data = { kind: kind, sql: sql };
            var url = 'table-query-ajax';
            if (!request)
                return;

            msg.html("querying ...");
            var that = this;
            request.setData(data).setHandler(function(ret) {
                msg.html(ret.data.msg);
            }).setErrorHandler(function(ret) {
                msg.html('');
            }).send();
        },

        main: function(){
        },

    };
    return App;
});
