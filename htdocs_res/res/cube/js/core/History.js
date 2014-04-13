/*jshint scripturl:true */
define('core/History', [ 'core/jQuery', 'core/URI' ], function(require, exports, module) {

    var $ = require('core/jQuery'),
        URI = require('core/URI');

    var History = {
        DEFAULT: Infinity,
        PUSHSTATE: 3,
        HASHCHANGE: 2,
        POLLING: 1,

        install: function(mechanism) {
            mechanism = mechanism || this.DEFAULT;

            this._initialPath = this._getBasePath(location.href);
            if (mechanism >= this.PUSHSTATE && 'pushState' in history) {
                this._mechanism = this.PUSHSTATE;
                $(window).bind('popstate', K.bind(this._handleChange, this));
            } else if (mechanism >= this.HASHCHANGE && 'onhashchange' in window && (! $.browser.msie || parseInt($.browser.version, 10) >= 8)) {
                this._mechanism = this.HASHCHANGE;
                $(window).bind('hashchange', K.bind(this._handleChange, this));
                setTimeout(K.bind(this._handleChange, this), 200);
            } else {
                this._mechanism = this.POLLING;
                setInterval(K.bind(this._handleChange, this), 200);
                if ($.browser.msie && parseInt($.browser.version, 10) < 8) {
                    this._installIframeHelper();
                }
            }
        },

        getMechanism: function() {
            return this._mechanism;
        },

        getPath: function() {
            if (this.getMechanism() === this.PUSHSTATE) {
                return this._getBasePath(location.href);
            } else {
                var parsed = this._parseFragment(location.hash);
                var search = window.location.search;
                if (search) {
                    parsed += search;
                }
                return parsed || this._getBasePath(location.href);
            }
        },

        push: function(path) {
            if (this.getMechanism() === this.PUSHSTATE) {
                if (this._initialPath && this._initialPath !== path) {
                    this._initialPath = null;
                }
                history.pushState(null, null, path);
                this._fire(path);
            } else {
                var hash = this._composeFragment(path);
                if (this._iframe) {
                    this._updateIframe(hash);
                } else {
                    location.hash = hash;
                }
            }
        },

        replace: function(path) {
            if (this.getMechanism() === this.PUSHSTATE) {
                history.replaceState(null, null, path);
                this._fire(path);
            } else {
                var uri = new URI(location.href);
                uri.setFragment(this._composeFragment(path));
                // Safari bug: "location.replace" does not respect changes made via
                // setting "location.hash", so use "history.replaceState" if possible.
                if ('replaceState' in history) {
                    history.replaceState(null, null, uri.toString());
                    this._handleChange();
                } else {
                    if (this._iframe) {
                        this._updateIframe(this._composeFragment(path), true);
                    } else {
                        location.replace(uri.toString());
                    }
                }
            }
        }
    };

    K.mix(History, (new K.Pubsub()));

    K.mix(History, {
        // Last path parsed from the URL fragment.
        _hash: null,

        // Some browsers fire an extra "popstate" on initial page load, so we keep
        // track of the initial path to normalize behavior (and not fire the extra
        // event).
        _initialPath: null,

        // Mechanism used to interface with the browser history stack.
        _mechanism: null,

        _iframe: null,

        _iframeReady: false,

        _installIframeHelper: function() {
            var iframeSrc = [
                'javascript:void( function(){',
                'document.open();',
                'document.write("<script>");',
                'document.write("document.domain=\\\"' + document.domain + '\\\";");',
                'document.write("window.location.hash =\\\"' + window.location.hash + '\\\";");',
                'document.write("window.onload=function(){ setTimeout(function() {parent.K.fire(\\\"iframe_history:ready\\\")}, 0);};");',
                'document.write("</script>");',
                'document.close();',
                '}() )'
            ].join( '' );
            this._iframe = $('<iframe tabindex="-1">').attr('src', iframeSrc).hide().appendTo('body').get(0).contentWindow;

            K.on('iframe_history:ready', K.bind(function() {
                this._iframeReady = true;
				var hash = decodeURIComponent(this._iframe.location.hash)
					.replace('#', '');
                if (hash != window.location.hash.replace('#')) {
                    window.location.hash = hash;
                }
            }, this));
        },

        _updateIframe: function(hash, isReplace) {
            if (this._iframeReady) {
                if (! isReplace) {
                    this._iframeReady = false;
                    var doc = this._iframe.document;
                    doc.open();
                    doc.write('<script>');
                    doc.write('document.domain = "' + document.domain + '";');
                    doc.write('window.onload=function() { setTimeout(function() {parent.K.fire("iframe_history:ready"); }, 0); };');
                    doc.write('</script>');
                    doc.close();
                    this._iframe.location.hash = encodeURIComponent(hash);
                } else {
                    this._iframe.location.hash = encodeURIComponent(hash);
                    K.fire('iframe_history:ready');
                }
            } else {
                K.log('iframe not ready');
            }
        },

        _handleChange: function() {
            var path = this.getPath();
            if (this.getMechanism() === this.PUSHSTATE) {
                if (path === this._initialPath) {
                    this._initialPath = null;
                } else {
                    this._fire(path);
                }
            } else {
                if (path == this._initialPath) {
                    this._initialPath = null;
                    this._hash = path;
                } else {
                    if (path !== this._hash) {
                        this._hash = path;
                        this._fire(path);
                    }
                }
            }
        },

        _fire: function(path) {
            this.fire('history:change', {
                path: this._getBasePath(path)
            });
        },

        _getBasePath: function(href) {
            var uri = new URI(href).setProtocol(null).setDomain(null);
            return uri.toString();
        },

        _composeFragment: function(path) {
            path = this._getBasePath(path);
            // If the URL fragment does not change, the new path will not get pushed
            // onto the stack. So we alternate the hash prefix to force a new state.
            if (this.getPath() === path) {
                this._hash = null;
                var hash = location.hash;
                if (hash && hash.charAt(1) === '!') {
                    return '~!' + path;
                }
            }
            return '!' + path;
        },

        _parseFragment: function(fragment) {
            if (fragment) {
                if (fragment.charAt(1) === '!') {
                    return fragment.substr(2);
                } else if (fragment.substr(1, 2) === '~!') {
                    return fragment.substr(3);
                }
            }
            return null;
        }
    });

    History.install();
    window.History = History;
    module.exports = History;
});
