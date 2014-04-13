/**
 * @class HoverPopUp 实现鼠标悬停弹出提示功能（已过期，请使用HoverTip）
 */
define( 'core/popup/HoverPopUp', ['core/jQuery'], function( require, exports ) {

    var $ = require('core/jQuery'),
        emptyString = '',
        mix = K.mix;

    function HoverPopUp(opts) {
        this.context = opts.context || 'body';
        this.selector = opts.selector;
        this.popUpTime = 0;
        this.popDownTime = 0;
        this.animateShowTime = 0;
        this.animateHideTime = 0;
        this.shStyle = 'fadeInOut';
        this.single = false; // 弹出层是否唯一
        this.popUpTimer,
        this.popDownTimer,
        mix(this, opts);
        this.init();
    };

    HoverPopUp.prototype = {
        'init': function() {
            $(this.context)
                .on('mouseenter', this.selector, $.proxy(this.popUp, this))
                .on('mouseleave', this.selector, $.proxy(this.popDown, this));
            //if($(this.context).is('body')) {
                //$(this.context).delegate(this.selector, 'mouseenter', $.proxy(this.popUp, this));
                //$(this.context).delegate(this.selector, 'mouseleave', $.proxy(this.popDown, this));
            //} else {
                //$(this.context + ' ' + this.selector).live('mouseenter', $.proxy(this.popUp, this));
                //$(this.context + ' ' + this.selector).live('mouseleave', $.proxy(this.popDown, this));
            //}
        },

        'popUp' : function(ev) {
            var $target = $(ev.currentTarget);

            if(this.single) {
                clearTimeout(this.popDownTimer);
                this.popUpTimer = setTimeout($.proxy(function() { this.showPopUp($target); }, this), this.popUpTime);
            } else {
                clearTimeout($target.data('popDownTimer'));
                $target.data('popUpTimer', setTimeout($.proxy(function() { this.showPopUp($target); }, this), this.popUpTime));
            }
        },

        'popDown' : function(ev) {
            var $target = $(ev.currentTarget);

            if(this.single) {
                clearTimeout(this.popUpTimer);
                this.popDownTimer = setTimeout($.proxy(function() { this.hidePopUp($target); }, this), this.popDownTime);
            } else {
                clearTimeout($target.data('popUpTimer'));
                $target.data('popDownTimer', setTimeout($.proxy(function() { this.hidePopUp($target); }, this), this.popDownTime));
            }

        },

        'showPopUp' : function($target) {
            var popUpWrapper = this.single ? this.popUpWrapper : $target.data('popUpWrapper');

            // 创建新弹出框
            if(!popUpWrapper && this.constructPopUp) {
                popUpWrapper = this.constructPopUp($target);  //$(this.constructPopUp($target[0])).appendTo($target.closest(this.context));
                if(this.single) {
                    this.popUpWrapper = popUpWrapper;
                } else {
                    $target.data('popUpWrapper', popUpWrapper);
                }
                this.bindPopUpHoverEvent(popUpWrapper, $target); // 弹出框需要绑定hover事件，防止弹出之后hover消失
                this.bindEvents(popUpWrapper, $target);
            }

            // 设置弹出框内容、样式并显示
            if(popUpWrapper) {

                // 更新弹出框内容并显示
                if(this.updateAttr) {
                    this.updateAttr($target, popUpWrapper, $.proxy(function() { this.show($target, popUpWrapper) }, this));
                } else {
                    this.show($target, popUpWrapper);
                }
            }
        },

        'getPopUp' : function($target) {
            return this.single ? this.popUpWrapper : $($target).data('popUpWrapper');
        },

        'hidePopUp' : function($target) {
            var popUpWrapper = this.single ? this.popUpWrapper : $target.data('popUpWrapper');
            if(this.resume) { this.resume($target); }
            if(popUpWrapper) {
                this.hide(popUpWrapper);
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
                    _this.popDownTimer = setTimeout(function() { _this.hidePopUp($target); }, _this.popDownTime);
                } else {
                    $target.data('popDownTimer', setTimeout(function() { _this.hidePopUp($target); }, _this.popDownTime));
                }
            });
        },

        'bindEvents' : function(dom, $target) {},

        'show' : function($target, popUpWrapper) {

            // 给弹出框设置位置
            if(this.setPosition) {
                this.setPosition($target, popUpWrapper);
            }

            // 显示
            HoverPopUp.shower[this.shStyle].show(popUpWrapper, this.animateShowTime);
        },

        'hide' : function(popUpWrapper) {
            HoverPopUp.shower[this.shStyle].hide(popUpWrapper, this.animateHideTime);
        }
    };

    HoverPopUp.shower = {
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

    return HoverPopUp;
});
