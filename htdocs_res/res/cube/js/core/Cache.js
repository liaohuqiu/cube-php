/**
 * 缓存机制（页面级别，如suggest时避免重复请求后端）
 */
define('core/Cache', [], function() {
    
    function Cache(opts) {
        this.size = typeof opts.size === 'undefined' ? 100 : opts.size;
        this.realsize = 0;
        this.data = {};
    };
    
    Cache.prototype = {
        
        'get' : function(key) {
            return this.data[key];
        },
        
        'put' : function(key, value) {
            if(this.realsize >= this.size) {
                this._del();
            } 
            this.realsize ++;
            this.data[key] = value;
        },
        
        '_del' : function() {
            for(var key in this.data) {
                if(this.data.hasOwnProperty(key)) {
                    delete this.data[key];
                    break;
                }
            }
        }
    };
    return Cache;
});
