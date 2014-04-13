/**
 * @class ClickPopUp 实现点击弹出提示功能
 */
define( 'core/popup/ClickPopUp', ['core/jQuery'], function(require) {
    
    var $ = require('core/jQuery');
    
    function ClickPopUp(trigger, pop, clickedClass) {
        this.trigger = $(trigger);
        this.pop = $(pop);
        this.clickedClass = clickedClass || 'clicked';
        this.bindEvents();
    };  
    
    ClickPopUp.prototype = {
        
        'bindEvents': function() {
            this.pop.bind('mouseenter', $.proxy(this.popMouseOver, this));
            this.pop.bind('mouseleave', $.proxy(this.popMouseOut, this));
            this.pop.bind('click', $.proxy(this.popClick, this));
            this.trigger.bind('blur', $.proxy(this.triggerBlur, this));
            this.trigger.bind('click', $.proxy(this.togglePop, this));
        },
        
        'popMouseOver' : function(ev) {
            this.trigger.data('mouseon', true);
        },
        
        'popMouseOut' : function(ev) {
            this.trigger.data('mouseon', false);
        },
        
        'popClick' : function(ev) {
            this.trigger.focus();
        },
        
        'triggerBlur' : function(ev) {
            if(!this.trigger.data('mouseon')) {
                this.hide();
            }
        },
        
        'hide' : function() {
            this.trigger.data('mouseon', false);
            this.pop.hide();
            this.trigger.removeClass(this.clickedClass);
        },
        
        'togglePop' : function(ev) {
            if(this.pop.css('display') !== 'none') {
                this.pop.hide();
                this.trigger.removeClass(this.clickedClass);
            } else {
                this.pop.show();
                this.trigger.addClass(this.clickedClass);
            }
            this.trigger.focus();
            ev.preventDefault();
        }
    }; 
    
    return ClickPopUp;   
});
