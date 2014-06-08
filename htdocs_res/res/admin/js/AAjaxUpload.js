K.App('admin/AAjaxUpload', ['core/AjaxUpload', 'core/dialog/MsgBox']).define(function(require) {

    var AjaxUpload = require('core/AjaxUpload');
    var MsgBox = require('core/dialog/MsgBox');
    var App = {

        events: {
            'click #j_btn_upload': 'clickUpload',
        },

        clickUpload: function() {
            var file = $("#id_file").val();
            if (file == "") {
                MsgBox.error("请先选择图片！", {auto_close: 1000});
                return;
            };
            $("#loading") .ajaxStart(function(){
                K.log('ajaxStart');
            })
            .ajaxComplete(function(){
                K.log('ajaxComplete');
            });
            var conf = {
                url: '/sample/upload-ajax',
                secureuri: false,
                fileElementId:'id_file',
                dataType: 'json',
                data: {name:'logan', id:'id'},
                success: function (data, status) {
                    K.log(data);
                },
                error: function (data, status, e) {
                }
            };
            AjaxUpload.ajaxFileUpload(conf);
        },

        main: function(){
            K.log('main');
        },

    };
    return App;
});
