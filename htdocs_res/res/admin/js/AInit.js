K.App('admin/AInit', ['core/dialog/AsyncDialog', 'core/dialog/MsgBox', 'core/ajax/Request']).define(function(require) {

    var Request = require('core/ajax/Request');
    var AsyncDialog = require('core/dialog/AsyncDialog');
    var MsgBox = require('core/dialog/MsgBox');
    var App = {

        events: {
            'click #j_btn_reset': 'clickReset',
            'click #j_btn_deploy': 'clickDeploy',
            'click #j_btn_check_deploy': 'clickCheckDeploy',
            'click #j_btn_get_config_info': 'clickGetConfigInfo',
            'click #j_btn_check_config': 'clickCheckConfigFile',
        },

        getInputData: function(cmd) {
            var fields = ['db_key', 'db_host', 'db_port', 'db_user', 'db_pwd', 'db_name', 'db_charset'];
            fields = fields.concat(['user_db', 'user_table', 'user_account', 'user_pwd']);

            var data = {};
            var error = false;
            fields.forEach(function(key) {
                var element = $('#' + key);
                element.parents('.form-group').removeClass('has-error');
                var val = element.val();
                data[key] = val;
            });
            if (error)
                return false;

            var ret = {};
            ret['data'] = JSON.stringify(data);
            ret['cmd'] = cmd;
            return ret;
        },

        clickReset: function() {
            var msg = 'Are you sure to clear all setting?';
            var data = this.getInputData('reset');
            if (!data)
                return;
            var that = this;
            var reset = function() {
                var request = Request.create('/init/init-do-ajax');
                request.setData(data).setHandler(function(ret){


                }).setErrorHandler(function(ret){
                    if (ret && ret.data)
                        that.showErrorInput(ret.data.error_keys);
                }).send();
            };
            // reset();
            MsgBox.confirm(msg, {on_ok: reset});
        },

        clickGetConfigInfo: function() {
            var data = this.getInputData('get-config');
            var that = this;
            var request = Request.create('/init/init-do-ajax');
            request.setData(data).setHandler(function(ret){
                that.showConfigInfo(ret);
            }).setErrorHandler(function(ret){
                if (ret && ret.data)
                    that.showErrorInput(ret.data.error_keys);
            }).send();
        },

        clickDeploy: function() {
            var data = this.getInputData('deploy');
            var that = this;
            var request = Request.create('/init/init-do-ajax');
            request.setData(data).setHandler(function(ret){
                that.showConfigInfo(ret);
            }).setErrorHandler(function(ret){
                if (ret && ret.data)
                    that.showErrorInput(ret.data.error_keys);
            }).send();
        },

        clickCheckDeploy: function() {
            var data = this.getInputData('check-deploy');
            var request = Request.create('/init/init-do-ajax');
            request.setData(data).setHandler(function(ret){
            }).setErrorHandler(function(ret){
            }).send();
        },

        showConfigInfo: function(ret) {
            $('#j_id_step2').removeClass('hide');
            $('#sys_config_path').html(ret.data.sys_config_path);
            $('#sys_config_str').val(ret.data.sys_config_str);
            $('#deploy_data_path').html(ret.data.deploy_data_path);
            $('#deploy_data_str').val(ret.data.deploy_data_str);
        },

        showErrorInput: function(keys) {
            if (!keys)
                return;
            keys.forEach(function(key) {
                var element = $('#' + key);
                element.parents('.form-group').addClass('has-error');
            });
        },

        main: function(){
        },

    };
    return App;
});
