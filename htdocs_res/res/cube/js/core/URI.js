/**
* Convert a string URI into a maleable object.
*
* var uri = new URI('http://www.example.com/asdf.php?a=b&c=d#anchor123');
* uri.getProtocol(); // http
* uri.getDomain(); // www.example.com
* uri.getPath(); // /asdf.php
* uri.getQueryParams(); // {a: 'b', c: 'd'}
* uri.getFragment(); // anchor123
*
* ...and back into a string:
*
* uri.setFragment('clowntown');
* uri.toString() // http://www.example.com/asdf.php?a=b&c=d#clowntown
*
* from: https://github.com/facebook/javelin/blob/master/src/lib/URI.js
*/

define('core/URI', [], function(require, exports, module) {

    var URI = function(uri) {
        this.setQueryParams({});

        if (uri) {
            // parse the url
            var result = URI._uriPattern.exec(uri);

            this.setProtocol(result[1] || undefined);
            this.setDomain(result[2] || undefined);
            this.setPort(result[3] || undefined);
            var path = result[4];
            var query = result[5];
            this.setFragment(result[6] || undefined);

            // parse the path
            this.setPath(path.charAt(0) == '/' ? path : '/' + path);

            // parse the query data
            if (query) {
                var queryData = {};
                var data;
            while ((data = URI._queryPattern.exec(query)) != null) {
                queryData[decodeURIComponent(data[1].replace(/\+/g, ' '))] =
                    decodeURIComponent(data[2].replace(/\+/g, ' '));
            }
                this.setQueryParams(queryData);
            }
        }
    };

    K.mix(URI, {
        _uriPattern: /(?:([^:\/?#]+):)?(?:\/\/([^:\/?#]*)(?::(\d*))?)?([^?#]*)(?:\?([^#]*))?(?:#(.*))?/,

        _queryPattern: /(?:^|&)([^&=]*)=?([^&]*)/g,

        _defaultQuerySerializer: function(obj) {
            var pairs = [];
            K.forEach(obj, function(val, key) {
                if (val != null) {
                    pairs.push(encodeURIComponent(key) + (val ? '=' + encodeURIComponent(val) : ''));
                }
            });

            return pairs.join('&');
        }
    });

    K.mix(URI.prototype, {
        getProtocol: function() {
            return this._protocol;
        },

        setProtocol: function(protocol) {
            this._protocol = protocol;
            return this;
        },

        getDomain: function() {
            return this._domain;
        },

        setDomain: function(domain) {
            this._domain = domain;
            return this;
        },

        getPort: function() {
            return this._port;
        },

        setPort: function(port) {
            this._port = port;
            return this;
        },

        getPath: function(path) {
            return this._path;
        },

        setPath: function(path) {
            this._path = path;
            return this;
        },

        getQueryParams: function() {
            return this._queryParams;
        },

        addQueryParams: function(params) {
            K.mix(this.getQueryParams(), params);
            return this;
        },

        setQueryParams: function(params) {
            this._queryParams = params;
            return this;
        },

        setQueryParam: function(key, value) {
            var map = {};
            map[key] = value;
            return this.addQueryParams(map);
        },

        getFragment: function() {
            return this._fragment;
        },

        setFragment: function(fragment) {
            this._fragment = fragment;
            return this;
        },

        getQuerySerializer: function() {
            return this._querySerializer;
        },

        setQuerySerializer: function(querySerializer) {
            this._querySerializer = querySerializer;
        },

        _getQueryString: function() {
            var str = (
                this.getQuerySerializer() || URI._defaultQuerySerializer
            )(this.getQueryParams());

            return str ? '?' + str : '';
        },

        go: function() {
            var uri = this.toString();
            (uri && (window.location = uri)) || window.location.reload(true);
        }
    });

    // https://developer.mozilla.org/en/ECMAScript_DontEnum_attribute#JScript_DontEnum_Bug
    URI.prototype.toString = function() {
        var str = '';
        if (this.getProtocol()) {
            str += this.getProtocol() + '://';
        }

        str += this.getDomain() || '';

        // If there is a domain or a protocol, we need to provide '/' for the
        // path. If we don't have either and also don't have a path, we can omit
        // it to produce a partial URI without path information which begins
        // with "?", "#", or is empty.
        str += this.getPath() || (str ? '/' : '');

        str += this._getQueryString();
        if (this.getFragment()) {
            str += '#' + this.getFragment();
        }
        return str;
    };

    return URI;
});
