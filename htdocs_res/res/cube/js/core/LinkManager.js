define('core/LinkManager', [ 'core/jQuery' ], function(require, exports, module) {

    var $ = require('core/jQuery');

    var LinkManager = {
        _callbacks: [],

        init: function() {
            var me = this;
            $(document).on('click', 'a', function(event) {
                var currentTarget = $(event.currentTarget),
                    href = currentTarget.attr('href');

                if (!href) {
                    return;
                }

                if (href && href.indexOf('#') === 0) {
                    event.preventDefault();
                    return;
                }

                if (event.which && event.which != 1) {
                    return;
                }
                if (event.ctrlKey || event.metaKey || event.altKey || event.shiftKey) {
                    return;
                }

                for (var ii = 0, jj=me._callbacks.length; ii<jj; ii++) {
                    me._callbacks[ii](currentTarget, event);
                }
            });
        },

        addHandler: function(callback) {
            this._callbacks.push(callback);
        }
    };

    LinkManager.init();

    exports.addHandler = function(callback) {
        LinkManager.addHandler(callback);
    };
});
