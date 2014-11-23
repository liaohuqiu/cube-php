K.App('admin/AAdminGlobal',
      ['core/dialog/AsyncDialog', 'core/dialog/MsgBox', 'core/ajax/Request', 'core/URI']
     ).define(
     function(require) {
         var Request = require('core/ajax/Request');
         var AsyncDialog = require('core/dialog/AsyncDialog');
         var MsgBox = require('core/dialog/MsgBox');
         var URI = require('core/URI');
         var App = {

             events: {
                 'click #_j_btn_change_pwd': 'clickChangePwd',
                 'click #_j_btn_logout': 'clickLogout',
                 'click ._j_btn_delete': 'clickDelete',
                 'click .msgbox-info': 'clickMsgBoxInfo',
                 'change ._j_check_input' : 'checkInput',
                 'change .js-quick-select' : 'onQuickSelectChange',
             },

             onQuickSelectChange: function(e) {
                 var target = $(e.target);
                 var name = target.attr('name');
                 var uri = new URI(window.location.href);
                 var data = {};
                 data[name] = target.val();
                 uri.addQueryParams(data);
                 uri.go();
             },

             checkInput: function(e) {
                 var t = $(e.target);
                 var value = t.val();
             },

             clickChangePwd: function() {
                 AsyncDialog.open('/admin/user/change-pwd-dialog');
             },

             clickLogout: function() {
                 var msg = 'Are you sure to logout?';
                 MsgBox.confirm(msg, {on_ok: function() {
                     window.location.href = '/admin/user/logout';
                 }});
             },

             clickDelete: function (e) {
                 var target = $(e.target);
                 var id = target.data('id');
                 var url;
                 var p = target.parents('._j_delele');
                 if (p.length) {
                     url = p.data('delete-url');
                 } else {
                     url = target.data('delete-url');
                 }
                 MsgBox.confirm('Are you sure to delete?' , {on_ok: function() {
                     var request = Request.create(url, id);
                     if (!request) {
                         return;
                     }
                     request.setData({id: id}).setHandler(function() {
                         window.location.reload(true);
                     }).send();
                 },});
             },

             clickMsgBoxInfo: function(e) {
                 var target = $(e.target);
                 var p = target.parents('.msgbox-info');
                 var data = {}
                 var width = p.data('msgbox-width');
                 if (width) {
                     data['width'] = width;
                 }
                 MsgBox.info(p.html(), data);
             },

             main: function(){
             },

         };
         return App;
     });
