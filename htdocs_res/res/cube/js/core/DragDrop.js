/**
 * 普通拖拽
 */
define('core/DragDrop', ['core/jQuery'], function(require) {
    
    var $ = require('core/jQuery');
    
    /**
     * <div dragable="true" />  <div dropable="true" />
     */
    function DragDrop(opts) {
        
        this.container = $('body');
        this.onDragClass = 'drag';
        this.onDropClass = 'drop';
        
        K.mix(this, opts);
        
        this.sourceElement = null;
        this.targetElement = null;
        this.moving = false;
        
        K.CustEvent.createEvents(this, 'dragstart,dragenter,dragend');
        
        this.init();
    }
    
    DragDrop.prototype = {
        
        'init' : function() {
            this.getDropMaps();
            this.bindEvents();
        },
        
        'getDropMaps' : function() {
            var _this = this;
            this.dropMaps = [];
            this.container.find('[dropable="true"]').each(function() {
                var $this = $(this),
                    offset = $this.offset();
                _this.dropMaps.push({
                    'x1' : offset.left,
                    'x2' : offset.left + $this.outerWidth(),
                    'y1' : offset.top,
                    'y2' : offset.top + $this.outerHeight(),
                    '$target' : $this
                });
            });
        },
        
        'bindEvents' : function() {
            this.container.delegate('[dragable="true"]', 'mousedown', $.proxy(this.holdElement, this));
        },
        
        'holdElement' : function(ev) {
            var $target = $(ev.currentTarget);
            $target.addClass(this.onDragClass);
            this.sourceElement = $target;
            this.listenMousemove();
            ev.preventDefault();
        },
        
        'listenMousemove' : function() {
            $(document).bind('mousemove', $.proxy(this.moveElement, this));
            $(document).bind('mouseup', $.proxy(this.releaseElement, this));
        },
        
        'stopListenMousemove' : function() {
            $(document).unbind('mousemove', $.proxy(this.moveElement, this));
            $(document).unbind('mouseup', $.proxy(this.releaseElement, this));
        },

        'moveElement' : function(ev) {
            if(this.sourceElement) {
                if(!this.moving) {
                    this.moving = true;
                    this.cloneSourceElement(this.sourceElement);
                    this.clientStartX = ev.clientX;
                    this.clientStartY = ev.clientY;
                    this.fire('dragstart', {'sourceElement':this.sourceElement, 'targetElement':this.targetElement});
                }
                var shadeElementOffset = this.shadeElement.offset();
                this.shadeElement.css({
                    'left' : shadeElementOffset.left + (ev.clientX - this.clientStartX),
                    'top' : shadeElementOffset.top + (ev.clientY - this.clientStartY)
                });
                this.clientStartX = ev.clientX;
                this.clientStartY = ev.clientY;
                this.setDropElement(ev.clientX, ev.clientY);
            }
        },
        
        'setDropElement' : function(clientX, clientY) {
            var dropTarget = this.getDropTarget(clientX, clientY);
            if(dropTarget && dropTarget.get(0) !== this.sourceElement.get(0) && !dropTarget.hasClass(this.onDropClass)) {
                this.container.find('.'+this.onDropClass+'[dropable="true"]').removeClass(this.onDropClass);
                dropTarget.addClass(this.onDropClass);
                this.targetElement = dropTarget;
                this.fire('dragenter', {'sourceElement':this.sourceElement, 'targetElement':this.targetElement});
            } else if(!dropTarget || dropTarget.get(0) === this.sourceElement.get(0)) {
                this.container.find('.'+this.onDropClass+'[dropable="true"]').removeClass(this.onDropClass);
                this.targetElement = null;
            }
        },
        
        'getDropTarget' : function(clientX, clientY) {
            var dropMap,
                dropMaps = this.dropMaps;
            for(var i=0, len=dropMaps.length; i<len; i++) {
                dropMap = dropMaps[i];
                if(clientX > dropMap.x1 && clientX < dropMap.x2 && clientY > dropMap.y1 && clientY < dropMap.y2) {
                    return dropMap.$target;
                }
            }
            return null;
        },
        
        'releaseElement' : function(ev) {
            if(this.sourceElement) {
                this.fire('dragend', {'sourceElement':this.sourceElement, 'targetElement':this.targetElement});
                this.container.find('.'+this.onDropClass+'[dropable="true"]').removeClass(this.onDropClass);
                this.sourceElement.removeClass(this.onDragClass);
                this.sourceElement = null;
                this.targetElement = null;
                this.shadeElement && this.shadeElement.remove();
                this.stopListenMousemove();
                this.moving = false;
            }
        },
        
        'cloneSourceElement' : function($target) {
            var offset = $target.offset();
            this.shadeElement = $target.clone();
            this.shadeElement.css( {
                'position' : 'absolute',
                'cursor' : 'move',
                'z-index' : '999999',
                'opacity' : '0.5',
                'float' : 'none',
                'left' : offset.left,
                'top'  : offset.top,
                'margin' : 0
            }).addClass('drag_shade_item').removeAttr('dragable').removeAttr('dropable').appendTo('body');
        }
    };
    
    return DragDrop;
});
