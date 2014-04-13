define('core/Router', [ 'core/History', 'core/LinkManager' ], function(require) {

    var History = require('core/History'),
        LinkManager = require('core/LinkManager');

    var namedParam = /:([\w\d]+)/g,
        escapeRegExp = /[-[\]{}()+?.,\\^$|#\s]/g;

    var Router = {
        setHashEnabled: function(bool) {
            this._hashEnabled = bool;
        },

        isHashEnabled: function() {
            return this._hashEnabled;
        },

        route: function(route, callback) {
            route = this._routeToRegExp(route);
            this._handlers.unshift({ route: route, callback: callback });
        },

        dispatch: function(path) {
            return K.some(this._handlers, function(handler) {
                if (handler.route.test(path)) {
                    var args = handler.route.exec(path).slice(1);
                    handler.callback && handler.callback.apply(null, [ path ].concat(args));
                    return true;
                }
            });
        }
    };

    K.mix(Router, {
        _hashEnabled: false,

        _handlers: [],

        _routeToRegExp: function(route) {
            route = route
                .replace(escapeRegExp, "\\$&")
                .replace(namedParam, "([^\/]*)");
            return new RegExp('^' + route + '$');
        }
    });

    History.on('history:change', function(state) {
        var path = state.path;
        Router.dispatch(path);
        K.fire('router:dispatch');
    });

    LinkManager.addHandler(function(target, event) {
        if (target.data('navigate')) {
            if (Router.isHashEnabled() || History.getMechanism() == History.PUSHSTATE) {
                History.push(target.attr('href'));
            } else {
                Router.dispatch(target.attr('href'));
            }
            event.preventDefault();
        }
    });

    return Router;
});
