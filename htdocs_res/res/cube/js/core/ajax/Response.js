define('core/ajax/Response', function(require) {

    var Response = function(response) {
        this.construct(response);
    };

    K.mix(Response.prototype, {
        construct: function(response) {
            this._response = response;
        },

        getPayload: function() {
            if (this._response && this._response.payload) {
                return this._response.payload;
            }
            return null;
        }
    });

    K.mix(Response.prototype, {
        _response: null
    });

    return Response;
});
