define('core/ajax/ResponseCache', function(require) {

    var now = function() {
        return new Date().getTime();
    };

    var Cache = {
        set: function(uri, response, expires) {
            var key = this._getCacheKey(uri);
            if (key) {
                this._setCache(key, {
                    uri: uri,
                    response: response,
                    time: now(),
                    expires: expires
                });
                return true;
            }
            return false;
        },

        get: function(uri) {
            var key = this._getCacheKey(uri);
            if (key) {
                return this._getCache(key);
            }
            return false;
        },

        invalidate: function(uri) {
            var key = this._getCacheKey(uri);
            if (key) {
                return delete this._cache[key];
            }
            return false;
        },

		clear: function() {
			this._cache = [];
		}
    };

    K.mix(Cache, {
        _getCacheKey: function(uri) {
            return uri;
        },

        _setCache: function(key, data) {
            this._cache[key] = data;
        },

        _getCache: function(key) {
            if (key in this._cache) {
                var data = this._cache[key];
                if (data.expires > 0 && now() <= data.time + data.expires) {
                    return data.response;
                }
            }
            return false;
        },

        _cache: {}
    });

	K.on('userstate:logout', function() {
		Cache.clear();
	});

    return Cache;
});
