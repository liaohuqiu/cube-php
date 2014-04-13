/*
 * placeholder for input an textarea
 */
define('core/placeholder', ['core/jQuery'], function(require, exports, module) {
    var $ = require('core/jQuery'),
        ph = "PLACEHOLDER_INPUT";
        phl = "PLACEHOLDER_LABEL";
        boundEvents = false;
        default_options = {
            labelClass: 'placeholder',
            css: {}
        };
    
    //check for native support for placeholder attribute, if so stub methods and return
    var input = document.createElement("input");
    if ('placeholder' in input) {
        $.fn.placeholder = function(){}; //empty function
        $.fn.changePlc = function(plc) { $(this).attr('placeholder', plc); };
        $.fn.resetPlc = function(){};
        $.fn.unplaceholder = function() { $(this).removeAttr('placeholder'); };
        input = null; //cleanup IE memory
        return;
    }
    input = null;

    function itemIn() {
        var input = $(this),
            label = $(input.data(phl));

        label.css('display', 'none');
    }

    function itemOut() {
        var that = this;

        //use timeout to let other validators/formatters directly bound to blur/focusout work first
        setTimeout(function(){
            var input = $(that),
                position = input.position(),
                marginLeft = input.css( 'marginLeft' ),
                marginTop = input.css( 'marginTop' );

            $(input.data(phl)).css( {
                'top': position.top + 'px',
                'left': position.left + 'px',
                'margin-left' : marginLeft,
                'margin-top' : marginTop,
                'display': !!input.val() ? 'none' : 'block'
            } );
        }, 200);
    }

    function bindEvents() {
        if (boundEvents) {
            return;
        }

        //prepare live bindings if not already done.
        $('.' + ph)
            .live('click',itemIn)
            .live('focusin',itemIn)
            .live('focusout',itemOut);
        var bound = true;

        boundEvents = true;
    }
    
    $.fn.placeholder = function(options) {
        bindEvents();

        var opts = $.extend(default_options, options);

        this.each(function(){
            var rnd=Math.random().toString(32).replace(/\./,''),
                input=$(this),
                label=$('<label style="position:absolute;display:none;top:0;left:0;cursor:text"></label>');

            if (!input.attr('placeholder') || !input.is(':visible') ||  input.data(ph) === ph) {
                return; //already watermarked
            }

            //make sure the input tag has an ID assigned, if not, assign one.
            if (!input.attr('id')) {
                input.attr('id', '__input_' + rnd );
            }
            
            // get origin line-height separately for jquery get line-height in ie has bug
            var inputLineHeight = 'currentStyle' in this ? this.currentStyle.lineHeight : input.css('line-height');

            label   
                .attr('id',input.attr('id') + "_placeholder")
                .data(ph, '#' + input.attr('id'))   //reference to the input tag
                .attr('for',input.attr('id'))
                .addClass(opts.labelClass)
                .addClass(opts.labelClass + '_for_' + this.tagName.toLowerCase()) //ex: watermark-for-textarea
                .addClass(phl)
                .text(input.attr('placeholder'))
                .css('color', '#999')
                .css('font-size', input.css('font-size'))
                .css('line-height', inputLineHeight)
                .css('font-family', input.css('font-family'))
                .css('padding-left', (pl = parseInt(input.css('padding-left'), 10), isNaN(pl) ? 1 : pl+1))
                .css('padding-top', (pt = parseInt(input.css('padding-top'), 10), isNaN(pt) ? 2 : pt+2))
                .css(opts.css);

            input
                .data(phl, '#' + label.attr('id'))  //set a reference to the label
                .data(ph,ph)        //set that the field is watermarked
                .addClass(ph)       //add the watermark class
                .after(label);      //add the label field to the page

            //setup overlay
            itemIn.call(this);
            itemOut.call(this);
        });
    };
    
    $.fn.resetPlc = function() {
        this.each(function() {
            var input = $(this),
                label = $(input.data(phl));
                
            if(input.data(ph) === ph) {
                itemIn.call(this);
                itemOut.call(this);
            }
        });
    };
    
    $.fn.changePlc = function(placeholder) {
        this.each(function() {
            var input = $(this),
                label = $(input.data(phl));
                
            if(input.data(ph) === ph) {
                label.text(placeholder);
                input.attr('placeholder', placeholder);
            }
        });
    };

    $.fn.unPlc = function(){
        this.each(function(){
            var input=$(this),
                label=$(input.data(phl));

            if (input.data(ph) === ph) {
                label.remove();
                input.removeData(ph).removeData(phl).removeClass(ph);
            }
        });
    };
});
