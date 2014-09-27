K.App('cube-demo/AIndex', ['core/dialog/AsyncDialog', 'core/dialog/MsgBox', 'core/ajax/Request']).define(function(require) {
    var Request = require('core/ajax/Request'); var AsyncDialog = require('core/dialog/AsyncDialog');
    var MsgBox = require('core/dialog/MsgBox');
    var App = {

        events: {
            'click .js-test-btn': 'clickTestButton',
        },

        clickTestButton: function() {
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
        },

        main: function() {
            K.log('main in AIndex');
        },

    };
    return App;
});
