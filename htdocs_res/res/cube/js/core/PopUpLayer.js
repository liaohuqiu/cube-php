/**
 * @class PopUpLayer 实现鼠标悬停或者点击弹出提示功能
 * 目前只支持  mouseenter mouseleave 显隐  与 click 显隐，对于click与mouseleave显隐这种奇怪方式不支持
 */
define( 'core/PopUpLayer', ['core/jQuery'], function( require, exports ) {
    
    var $ = require('core/jQuery'),
        emptyString = '',
        mix = K.mix;
    
    function PopUpLayer(opts) {
        this.context = opts.context || 'body';
        this.events = 'click';
        this.selector = opts.selector;
        this.popUpTime = 0;
        this.popDownTime = 0;
        this.animateShowTime = 0;
        this.animateHideTime = 0;
        this.shStyle = 'fadeInOut';
        mix(this, opts);
        this.init();
    };  
    
    PopUpLayer.prototype = {
        'init': function() {
            if(this.events === 'click') {
                $(this.context).delegate(this.selector, 'click', $.proxy(this.pop, this));
            } else {
                $(this.context)
                    .delegate(this.selector, 'mouseenter', $.proxy(this.popUp, this))
                    .delegate(this.selector, 'mouseleave', $.proxy(this.popDown, this));
            }
        },
        
        'pop' : function() {
            
        },
        
        'popUp' : function(ev) {
            var $target = $(ev.currentTarget);
                
            clearTimeout($target.data('popDownTimer'));
            $target.data('popUpTimer', setTimeout($.proxy(function() { this.showPopUp($target); }, this), this.popUpTime));
        },
        
        'popDown' : function(ev) {
            var $target = $(ev.currentTarget);
            
            clearTimeout($target.data('popUpTimer'));
            $target.data('popDownTimer', setTimeout($.proxy(function() { this.hidePopUp($target); }, this), this.popDownTime));
        },
        
        'showPopUp' : function($target) {
            var popUpWrapper = $target.data('popUpWrapper');
                
            // 创建新弹出框
            if(!popUpWrapper && this.constructPopUp) {
                popUpWrapper = this.constructPopUp($target);  //$(this.constructPopUp($target[0])).appendTo($target.closest(this.context));
                $target.data('popUpWrapper', popUpWrapper);
                this.bindPopUpHoverEvent(popUpWrapper, $target); // 弹出框需要绑定hover事件，防止弹出之后hover消失
                this.bindEvents(popUpWrapper, $target);
            }
            // 设置弹出框内容、样式并显示
            if(popUpWrapper) {
                
                // 给弹出框设置位置
                if(this.setPosition) {
                    this.setPosition($target, popUpWrapper);
                }
                
                // 更新弹出框内容并显示
                if(this.updateAttr) { 
                    this.updateAttr($target, popUpWrapper, $.proxy(function() { this.show(popUpWrapper) }, this)); 
                } else {
                    this.show(popUpWrapper);
                }
            }
        },
        
        'hidePopUp' : function($target) {
            if(this.resume) { this.resume($target); }
            if($target.data('popUpWrapper')) {
                this.hide($target.data('popUpWrapper'));
            }
        },
        
        'bindPopUpHoverEvent' : function(dom, $target) {
            var _this = this;
            dom.hover(function() {
                clearTimeout($target.data('popDownTimer'));
            }, function() {
                $target.data('popDownTimer', setTimeout(function() { _this.hidePopUp($target); }, _this.popDownTime));
            });
        },
        
        'bindEvents' : function(dom, $target) {},
        
        'show' : function(popUpWrapper) {
            PopUpLayer.shower[this.shStyle].show(popUpWrapper, this.animateShowTime);
        },
        
        'hide' : function(popUpWrapper) {
            PopUpLayer.shower[this.shStyle].hide(popUpWrapper, this.animateHideTime);
        }
    };
    
    PopUpLayer.shower = {
        'fadeInOut' : {
            'show' : function(pop, time) { pop.stop(true, true).fadeIn(time); },
            'hide' : function(pop, time) { pop.stop(true, true).fadeOut(time); }
        },
        
        'showHide' : {
            'show' : function(pop, time) { pop.stop(true, true).show(time); },
            'hide' : function(pop, time) { pop.stop(true, true).hide(time); }
        },
        
        'slideDownUp' : {
            'show' : function(pop, time) { pop.stop(true, true).slideDown(time); },
            'hide' : function(pop, time) { pop.stop(true, true).slideUp(time); }
        }
    }; 
    
    return PopUpLayer;   
});
