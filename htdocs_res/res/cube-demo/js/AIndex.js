K.App('cube-demo/AIndex', ['core/dialog/AsyncDialog', 'core/dialog/MsgBox', 'core/ajax/Request']).define(function(require) {
    var Request = require('core/ajax/Request'); var AsyncDialog = require('core/dialog/AsyncDialog');
    var MsgBox = require('core/dialog/MsgBox');
    var App = {

        events: {
            'click .js-test-btn': 'clickTestButton',
        },

        clickTestButton: function(e) {
            var action = $(e.target).data('action');
            K.log(action);
            if (action == 'show-msgbox-success') {
                var data = {
                    buttons: {
                        'Button 1': function() {
                            K.log('Button 1');
                        },
                        'Button 2': function() {
                            K.log('Button 2');
                        },
                    },
                };
                MsgBox.succ('aaa', data);
            } else if (action == 'show-msgbox-error') {
                var data = {
                };
                MsgBox.error('error message', data);
            } else if (action == 'show-ajax-error-tip') {
                var tip = $('#ajax-error-message');
                tip.show(function() {
                    return $(this).addClass("visible")
                })
            }
        },

        main: function() {
            K.log('main in AIndex');
        },

    };
    return App;
});
