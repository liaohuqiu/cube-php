define('core/AsyncLoader', function(require) {

    function AsyncLoader(opts) {
        this.context = 'body';
        this.triggerSelector = null;
        this.ev = 'click';
        this.modules = [];
        this.handler = $.noop;
        
        K.mix(this, opts);
        
        this.init();
    }
        
    AsyncLoader.prototype = {
        
        'init' : function() {
            $(this.context).delegate(this.triggerSelector, this.ev, $.proxy(this.asyncLoad, this));
            
            if(this.ev === 'mouseenter') {
                $(this.context).delegate(this.triggerSelector, 'mouseenter', foucsIn);
                $(this.context).delegate(this.triggerSelector, 'mouseout', foucsOut);
            }
        },
        
        'asyncLoad' : function(ev) {
            var target = $(ev.currentTarget);
            
            if(!target.hasClass('_j_asyncloading')) {
                target.addClass('_j_asyncloading');
                require.async(this.modules, $.proxy(function() {
                    var modules = Array.prototype.slice.call(arguments);
                    $(this.context).undelegate(this.triggerSelector, this.ev);
                    this.handler.apply(null, modules.concat(target));
                    target.removeClass('_j_asyncloading');
                    
                    // 再次触发事件，让相关js加载完毕之后事件得以继续执行
                    if(this.ev !== 'mouseenter' || !target.hasClass('_j_focusout')) { // 用以控制某些情况：如mouseenter出现，mouseout消失，如果当前已经不是mouseenter的状态，就不需要
                        target.trigger(this.ev);
                    }
                    
                    // 取消多余的mouseenter事件绑定
                    if(this.ev === 'mouseenter') {
                        $(this.context).undelegate(this.triggerSelector, 'mouseenter', foucsIn);
                        $(this.context).undelegate(this.triggerSelector, 'mouseout', foucsOut);
                    }
                    //this = null;
                }, this));
            }
            
            ev.preventDefault();
        }
        
    };
    
    function foucsIn(ev) {
        $(ev.currentTarget).removeClass('_j_focusout');
    }
    
    function foucsOut(ev) {
        $(ev.currentTarget).addClass('_j_focusout');
    }
    
    return AsyncLoader;
    
});
