define('admin/ChangePwd', ['core/ajax/Request', 'core/dialog/MsgBox'], function(require) {
    var Request = require('core/ajax/Request');
    var MsgBox = require('core/dialog/MsgBox');
    var Handler = {
        events: {
            'click #_j_btn_submit' : 'doSubmit',
            'change #_j_id_old_pwd' : 'onOlbPwdChange',
            'change #_j_id_new_pwd1' : 'onPwdChange',
            'change #_j_id_new_pwd2' : 'onPwdChange',
        },

        main: function (){
            dialog = this;
        },

        doSubmit: function() {
            var old_pwd = $('#_j_id_old_pwd').val();
            var new_pwd1 = $('#_j_id_new_pwd1').val();
            var new_pwd2 = $('#_j_id_new_pwd2').val();

            var data = {
                old_pwd: old_pwd,
                new_pwd1: new_pwd1,
                new_pwd2: new_pwd2,
            };
            var request = Request.create('/admin/user/change-pwd-ajax');
            if (!request)
                return;
            var me = this;
            request.setData(data).setHandler(function(ret){
                MsgBox.success(ret.data.msg, {on_dismiss: function() {
                    me.getDialog().close();
                    window.location.href = ret.data.redirect;
                }});
            }).send();
        },

        onOlbPwdChange: function() {
        },

        onPwdChange: function() {
            var pwd1 = $('#_j_id_new_pwd1').val();
            var pwd2 = $('#_j_id_new_pwd2').val();

            if (pwd1.length == 0)
                return;

            var strength = this.getPwdStrength(pwd1);
            if (strength < 2) {
                this.showErrorTip('new_pwd1', 'This password is too simple.');
                return;
            } else {
                this.showSuccessTip('new_pwd1');
            }

            if (pwd2.length == 0)
                return;

            if (pwd1 != pwd2) {
                var msg = 'Your password and confirmation password do not match.';
                this.showErrorTip('new_pwd2', msg);
            } else {
                this.showSuccessTip('new_pwd2');
            }
        },

        updateSubmitButton: function() {
            var button = $('#_j_btn_submit');
            if (this.new_pwd1_ok && this.new_pwd2_ok) {
                button.removeClass('disabled');
            } else {
                button.addClass('disabled');
            }
        },

        showErrorTip: function(name, tip) {
            var container = $('#_j_id_container_' + name);
            container.find('.label').removeClass('hide').html(tip);
            container.removeClass('has-success');
            container.addClass('has-error');

            var propertyName = name + '_ok';
            this[propertyName] = false;
            this.updateSubmitButton();
        },

        showSuccessTip: function(name) {
            var container = $('#_j_id_container_' + name);
            container.find('.label').addClass('hide');
            container.removeClass('has-error');
            container.addClass('has-success');

            var propertyName = name + '_ok';
            this[propertyName] = true;
            this.updateSubmitButton();
        },

        showRegSucc: function() {
            $('.result-container').show();
            $('.form-sign').hide();
        },

        getPwdStrength: function(password) {

            //initial strength
            var strength = 0

            //if the password length is less than 6
            if (password.length < 6) {
                return 0;
            }

            //length is ok, lets continue.

            //if length is 6 characters or more, increase strength value
            if (password.length >= 6)
                strength += 1

            //if password contains both lower and uppercase characters, increase strength value
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
                strength += 1

            //if it has numbers and characters, increase strength value
            if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))
                strength += 1

            //if it has one special character, increase strength value
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))
                strength += 1

            //if it has two special characters, increase strength value
            if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/))
                strength += 1
            return strength;
        },

    };
    return Handler;
});
