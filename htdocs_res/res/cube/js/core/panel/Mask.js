/**
 * @class Mask 继承于Panel，实现Panel Mask功能
 */
define('core/panel/Mask', ['core/jQuery', 'core/panel/Panel'], function( require ){
    var $ = require('core/jQuery'),
        Panel = require('core/panel/Panel'),
        mix = K.mix,
        extend = K.extend,
        UA = K.Browser,
        CustEvent = K.CustEvent;

    var Mask = extend( function( opts ){
        this._initMask( opts );
    }, Panel );

    K.mix(Mask, {
        _actives: [],
        setActive: function(mask, bool) {
            this._actives = K.without(this._actives, mask);
            if (bool) {
                this._actives.unshift(mask);
            }
        },

        /**
         *
         * 获取当前激活的
         */
        getActive: function() {
            if (this._actives.length) {
                return this._actives[0];
            }
            return false;
        }
    });

    mix( Mask.prototype, {
        _initMask: function( opts ){

            this.MASK_CLASS     =   'panel-mask';
            this.scrollInterval =   50,
            this.resizeInterval =   50,

            this.leftOffset     =   200,
            this.topOffset      =   200,
            this.rightOffset    =   0,
            this.bottomOffset   =   200,

            mix( this, opts, true );

            this._addMaskEvent();

            Mask.$super.call(this,opts);

            var pos = this._canbeFixed() ? 'fixed' : 'absolute';
            this.addClass( this.MASK_CLASS );
            this.setStyle('position', pos);

            var ins = this;
            this.on('aftershow',function(){
                ins.adaptBounds();
                Mask.setActive(ins, true);
            });
            this.on('afterhide', function() {
                Mask.setActive(ins, false);
            });
            this.on('afterdispose', function() {
                Mask.setActive(ins, false);
            });
        },
        _addMaskEvent: function() {
            var instance = this;

            if( !this._canbeFixed() ) {

                $(window).bind( 'scroll', function() {
                    clearTimeout(instance._scrollTimer);

                    instance._scrollTimer = setTimeout(function() {
                        clearTimeout(instance._scrollTimer);
                        instance.adaptBounds();
                    }, instance.scrollInterval);

                });

                $(window).bind( 'resize', function() {
                    clearTimeout(instance._resizeTimer);

                    instance._resizeTimer = setTimeout(function() {
                        clearTimeout(instance._resizeTimer);
                        instance.adaptBounds();
                    }, instance.resizeInterval);

                });
            };
        },
        adaptBounds: function() {

            var panel = this.getPanel();
            if (!panel) return;
            var position = panel.css('position').toLowerCase();

            if ( 'fixed' != position ) {

                var bounds = this.getWinRect();
                var p = {
                    width:  panel.width(),
                    height: panel.height()
                };
                var x = y = w = h = 0;

                x = bounds.scrollLeft - this.leftOffset <= 0 ? 0 : bounds.scrollLeft - this.leftOffset;
                y = bounds.scrollTop - this.topOffset <= 0  ? 0 : bounds.scrollTop - this.topOffset;
                w = this.rightOffset;
                h = this.bottomOffset;

                w += bounds.width;
                h += bounds.height;

                if ( p.width  > w ) w = null;
                if ( p.height > h ) h = null;

                this.setRect({
                    left:   x,
                    top:    y,
                    width:  w,
                    height: h
                });

            } else {

                this.setStyle('width' , '100%');
                this.setStyle('height', '100%');
                this.setStyle('left'  , '0');
                this.setStyle('top'   , '0');

            }
        },
        /**
         * 跟随Panel同步展现和隐藏
         */
        follow: function( panel ){
            if( !panel ) return;
            var ins = this;
            panel.on('aftershow', function(){
                ins.show();
                var zIndex = parseInt(panel.getZIndex(),10);
                ins.getPanel().css( 'z-index',zIndex - 1 );
            });
            panel.on('afterhide', function(){
                ins.hide();
            });
            panel.on('afterdispose', function(){
                ins.dispose();
            });
        }
    } );

    return Mask;
});
