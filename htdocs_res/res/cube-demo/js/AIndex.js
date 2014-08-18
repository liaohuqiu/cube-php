K.App('cube-demo/AIndex', ['core/dialog/AsyncDialog', 'core/dialog/MsgBox', 'core/ajax/Request']).define(function(require) {
    var Request = require('core/ajax/Request'); var AsyncDialog = require('core/dialog/AsyncDialog');
    var MsgBox = require('core/dialog/MsgBox');
    var App = {

        events: {

        },

        main: function(){
            K.log('main in AIndex');
        },

    };
    return App;
});
