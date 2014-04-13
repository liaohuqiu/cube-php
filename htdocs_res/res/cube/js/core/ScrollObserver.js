/**
 * 优化绑定事件到window.scroll，节省效率的方式
 */
define('core/ScrollObserver', ['core/jQuery'], function(require, exports) {
    
    var index = 0,
        $ = require('core/jQuery'),
        handlers = {},
        start = false, timer, emptyLoad = true;
    
    exports.addObserver = function(handler, context) {
        var observerID = 'ScrollObserver_'+index;
        index ++;
        context = context || window;
        handlers[observerID] = $.proxy(handler, context);
        emptyLoad = false;
        return observerID;
    };
    
    exports.removeObserver = function(ID) {
        delete handlers[ID];
        if(K.isEmpty(handlers)) { emptyLoad = true; }
    };
    
    function executeObserver() {
        for(var i in handlers) {
            if(handlers.hasOwnProperty(i)) { handlers[i](); }
        }
    }
    
    $(window).scroll(function() {
        if(emptyLoad) return;
        if(!start) {
            timer = setInterval(function() {
                if (start) {
                    start = false;
                    clearTimeout(timer);
                    executeObserver();
                }
            }, 100);
        }
        start = true;
    });
    
    return exports;
});
