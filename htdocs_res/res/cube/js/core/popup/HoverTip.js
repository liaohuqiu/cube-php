/**
 * @class Tip 实现鼠标悬停弹出提示功能
 */
define( 'core/popup/HoverTip', ['core/jQuery'], function( require, exports ) {

    var $ = require('core/jQuery'),
        mix = K.mix;

    function HoverTip(opts) {
        this.context = opts.context || 'body';
        this.selector = opts.selector;
        
        this.single = false; // 弹出层是否唯一
        this.tip = undefined; // 在single等于true时可以直接出入tip，否则需要实现setTip函数
        
        this.showWait = 20;
        this.hideWait = 100;
        this.showTime = 100;
        this.hideTime = 100;
        
        this.shStyle = 'fadeInOut'; // 显示影藏方式（默认渐隐渐显）
        
        mix(this, opts);
        
        K.CustEvent.createEvents(this, 'beforeshow,aftershow,beforehide,afterhide');
        
        this.popUpTimer,
        this.popDownTimer,
        this.init();
    };

    HoverTip.prototype = {
        'init': function() {
            $(this.context)
                .on('mouseenter', this.selector, $.proxy(this.popUp, this))
                .on('mouseleave', this.selector, $.proxy(this.popDown, this));
        },

        'popUp' : function(ev) {
            var $target = $(ev.currentTarget);

            if(this.single) {
                clearTimeout(this.popDownTimer);
                this.popUpTimer = setTimeout($.proxy(function() { this.showPopUp($target); }, this), this.showWait);
            } else {
                clearTimeout($target.data('popDownTimer'));
                $target.data('popUpTimer', setTimeout($.proxy(function() { this.showPopUp($target); }, this), this.showWait));
            }
        },

        'popDown' : function(ev) {
            var $target = $(ev.currentTarget);

            if(this.single) {
                clearTimeout(this.popUpTimer);
                this.popDownTimer = setTimeout($.proxy(function() { this.hidePopUp($target); }, this), this.hideWait);
            } else {
                clearTimeout($target.data('popUpTimer'));
                $target.data('popDownTimer', setTimeout($.proxy(function() { this.hidePopUp($target); }, this), this.hideWait));
            }

        },

        'showPopUp' : function($target) {
            var tip = this.getTip($target);

            // 创建新弹出框
            if(!tip && this.setTip) {
                tip = this.setTip($target);  //$(this.constructPopUp($target[0])).appendTo($target.closest(this.context));
                if(this.single) {
                    this.tip = tip;
                } else {
                    $target.data('popuptip', tip);
                }
                this.bindPopUpHoverEvent(tip, $target); // 弹出框需要绑定hover事件，防止弹出之后hover消失
            }

            // 设置弹出框内容、样式并显示
            if(tip) {
                if(this.deferShow) {
                    this.deferShow($target, tip, $.proxy(function() { this.show($target, tip) }, this));
                } else {
                    this.show($target, tip);
                }
            }
        },

        'getTip' : function($target) {
            return this.single ? this.tip : $($target).data('popuptip');
        },

        'hidePopUp' : function($target) {
            var tip = this.getTip($target);
            if(tip) {
                this.hide($target, tip);
            }
        },

        'bindPopUpHoverEvent' : function(dom, $target) {
            var _this = this;

            dom.hover(function() {
                if(_this.single) {
                    clearTimeout(_this.popDownTimer);
                } else {
                    clearTimeout($target.data('popDownTimer'));
                }
            }, function() {
                if(_this.single) {
                    _this.popDownTimer = setTimeout(function() { _this.hidePopUp($target); }, _this.hideWait);
                } else {
                    $target.data('popDownTimer', setTimeout(function() { _this.hidePopUp($target); }, _this.hideWait));
                }
            });
        },

        'show' : function($target, tip) {
            this.fire('beforeshow', {'trigger':$target, 'tip':tip});
            HoverTip.shower[this.shStyle].show(tip, this.showTime, $.proxy(function() {
                this.fire('aftershow', {'trigger':$target, 'tip':tip});
            }, this));
        },

        'hide' : function($target, tip) {
            this.fire('beforehide', {'trigger':$target, 'tip':tip});
            HoverTip.shower[this.shStyle].hide(tip, this.hideTime, $.proxy(function() {
                this.fire('afterhide', {'trigger':$target, 'tip':tip});
            }, this));
        }
    };

    HoverTip.shower = {
        'fadeInOut' : {
            'show' : function(tip, time, callback) { $(tip).stop(true, true).fadeIn(time, callback); },
            'hide' : function(tip, time, callback) { $(tip).stop(true, true).fadeOut(time, callback); }
        },

        'showHide' : {
            'show' : function(tip, time, callback) { $(tip).stop(true, true).show(time, callback); },
            'hide' : function(tip, time, callback) { $(tip).stop(true, true).hide(time, callback); }
        },

        'slideDownUp' : {
            'show' : function(tip, time, callback) { $(tip).stop(true, true).slideDown(time, callback); },
            'hide' : function(tip, time, callback) { $(tip).stop(true, true).slideUp(time, callback); }
        }
    };

    return HoverTip;
});
