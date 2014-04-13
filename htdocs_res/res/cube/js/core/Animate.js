(function($) {
	
	// ie6直接禁用动画效果
	var browser = $.browser;
	if(browser.msie && parseInt(browser.version, 10) < 7) {
		
		$.fx.off = true;
		
	} else { // 其他浏览器
		
		// 测试是否支持css3 transition
		var div = document.createElement('div'),
			divStyle = div.style;
		var supportTransition = 'MozTransition' in divStyle ? 'MozTransition' :
			'WebkitTransition' in divStyle ? 'WebkitTransition' : false;
		// 销毁元素，防止ie内存泄露
		div = divStyle = null;
		
		console.log(supportTransition);
		
		// 支持的话，改写jquery animate
		if(supportTransition) {
			$.fn.extend({
				cssAnimate: function(props, speed, easing, callback) {
					
					var options = $.speed(speed, easing, callback),
						start = $.noop, end = $.noop;
					
					if ($.isEmptyObject(props)) {
						return this.each(options.complete, [false]);
					}
					
					props = jQuery.extend({}, props);
					
					// slideDown slideUp slideToggle show('...') hide('...') return to jQuery
					if(props['height'] === 'show' || props['height'] === 'hide' || props['height'] === 'toggle') {
						return this.each(function() {
							$(this).animate(props, speed, easing, callback);
						});
					}
					
					// fadeIn fadeOut
					if(props['opacity'] === 'show') {
						props['opacity'] = 1;
						start = function() { $(this).show() };
					} else if(props['opacity'] === 'hide') {
						props['opacity'] = 0;
						end = function() { $(this).hide() };
					}
					
					var altTransition,
						speed = speed || 300,
					    easing = options.easing || 'ease-in-out',
						prefix = getPrefix('transition');
						
					return this.each(function() {
						var $this = $(this);
						
						start.call(this);
						$this.css(prefix + 'transition', 'all ' + speed / 1000 + 's ease-in-out').css(props);
						setTimeout($.proxy(function() {
							$this.css(prefix + 'transition', altTransition);
							if ($.isFunction(options.complete)) {
								options.complete();
								end.call(this);
							}
						}, this), speed);
					});
				}
			});
		}
	}
	
	
	// 获得css3支持浏览器所使用的特殊前缀
	function getPrefix( prop ){
        var prefixes = ['Moz', 'Webkit', 'Khtml', '0', 'ms'],
            elem = document.createElement('div'),
            upper = prop.charAt(0).toUpperCase() + prop.slice(1),
            pref = "";

        for(var len = prefixes.length; len--;){
            if((prefixes[len] + upper) in elem.style){
                pref = (prefixes[len]);
            }
        }

        if(prop in elem.style){
            pref = (prop);
        }

        return '-' + pref.toLowerCase() + '-';
    }

})(jQuery);