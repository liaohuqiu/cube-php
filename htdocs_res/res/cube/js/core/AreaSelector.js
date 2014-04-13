/**
 * 鼠标拖拽范围选取
 */
define('core/AreaSelector', ['core/jQuery'], function(require) {
    
    var $ = require('core/jQuery');

    function AreaSelector(opts) {
        
        this.container = 'body';
        this.selectableSelector = '._j_selectitem';
        
        K.mix(this, opts);
        
        this.container = $(this.container);
        this.selectStartOffset = {'top':0, 'left':0};
        this.dragPostion = {'top':false, 'left':false};
        
        K.CustEvent.createEvents(this, 'selectstart,selectend');
        
        this.init();
    }
    
    AreaSelector.prototype = {
        
        'init' : function() {
            this.getDropMaps();
            this.bindEvents();
        },
        
        'getDropMaps' : function() {
            var _this = this;
            this.dropMaps = [];
            this.container.find(this.selectableSelector).each(function() {
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
        	K.on('areaselect:itemupdate', $.proxy(this.getDropMaps, this));
            this.container.bind('mousedown', $.proxy(this.startSelect, this));
        },
        
        'startSelect' : function(ev) {
        	this.fire('selectstart');
        	this.selectStartOffset = {'top':ev.pageY, 'left':ev.pageX};
        	this.listenMousemove();
            ev.preventDefault();
        },
        
        'listenMousemove' : function() {
        	var moveHandler = this.getMoveHandler(),
        		releaseHandler = this.getReleaseHandler();

            $(document).bind('mousemove', moveHandler);
            $(document).bind('mouseup', releaseHandler);
        },
        
        'stopListenMousemove' : function() {
        	var moveHandler = this.getMoveHandler(),
        		releaseHandler = this.getReleaseHandler();
        		
            $(document).unbind('mousemove', moveHandler);
            $(document).unbind('mouseup', releaseHandler);
        },
        
        'getMoveHandler' : function() {
        	if(!this.moveHandler) {
        		this.moveHandler = $.proxy(this.moveSelection, this);
        	}
        	return this.moveHandler;
        },
        
        'getReleaseHandler' : function() {
        	if(!this.releaseHandler) {
        		this.releaseHandler = $.proxy(this.releaseSelection, this);
        	}
        	return this.releaseHandler;
        },

        'moveSelection' : function(ev) {
            this.setSelectionArea(ev);
            this.adjustViewport(ev);
        },
        
        // 只调整垂直方向
        'adjustViewport' : function(ev) {
        	var winViewPortHeight = document.documentElement.clientHeight,
        		needAdjust = false;
			
			if(this.dragPostion.top && ev.clientY < 20) {
				$(document).scrollTop($(document).scrollTop() - 20);
			} else if(!this.dragPostion.top && document.documentElement.clientHeight - ev.clientY < 20) {
				$(document).scrollTop($(document).scrollTop() + 20);
			}
        },
        
        'setSelectionArea' : function(ev) {
        	if(!this.selectArea) {
        		this.createSelectArea();
        	}
        	
        	var container = this.container,
        		startOffset = this.selectStartOffset,
        		currOffset = {'top':ev.pageY, 'left':ev.pageX},
        		containerOffset = this.container.offset(),
        		areaCss = {};
        		
        	// 确定鼠标运动方向
        	this.currOffset = this.currOffset || startOffset;
        	if(this.currOffset.left < currOffset.left) {
        		this.dragPostion.left = false;
        	} else {
        		this.dragPostion.left = true;
        	}
        	if(this.currOffset.top < currOffset.top) {
        		this.dragPostion.top = false;
        	} else {
        		this.dragPostion.top = true;
        	}
        	this.currOffset = currOffset;
        		
        	// 避免超越container边界
        	if(currOffset.left < containerOffset.left) {
        		currOffset.left = containerOffset.left;
        	} else if(currOffset.left > containerOffset.left + container.outerWidth()) {
        		currOffset.left = containerOffset.left + container.outerWidth();
        	}
        	if(currOffset.top < containerOffset.top) {
        		currOffset.top = containerOffset.top;
        	} else if(currOffset.top > containerOffset.top + container.outerHeight()) {
        		currOffset.top = containerOffset.top + container.outerHeight();
        	}
        	
        	var width = Math.abs(currOffset.left - startOffset.left),
        		height = Math.abs(currOffset.top - startOffset.top);
        	
        	// 选区的定位
        	if(currOffset.left < startOffset.left) {
        		areaCss.left = currOffset.left;
        	} else {
        		areaCss.left = startOffset.left;
        	}
        	if(currOffset.top < startOffset.top) {
        		areaCss.top = currOffset.top;
        	} else {
        		areaCss.top = startOffset.top;
        	}
        	
        	// 定位及宽高的设定
        	K.mix(areaCss, {'width':width, 'height':height});
        	this.selectArea.css(areaCss).show();
        	this.selectItems({
        		'x1' : areaCss.left,
        		'x2' : areaCss.left + areaCss.width,
        		'y1' : areaCss.top,
        		'y2' : areaCss.top + areaCss.height
        	});
        },
        
        'createSelectArea' : function() {
        	this.selectArea = $('<div style="display:none;"></div>').appendTo('body');
        	this.selectArea.css({'position':'absolute', 'opacity':0.3, 'background':'#BBBBFF', 'border':'1px solid #0000FF'});
        },
        
        'selectItems' : function(clientRect) {
        	K.forEach(this.dropMaps, $.proxy(function(map, idx) {
        		if(this.withIntersection(clientRect, map)) {
        			map.$target.trigger('areaselect:selected');
        		} else {
        			map.$target.trigger('areaselect:unselected');
        		}
        	}, this));
        },
        
        'withIntersection' : function(mapA, mapB) {
        	var withInter = !(mapA.x2 <= mapB.x1 || mapB.x2 <= mapA.x1 || mapA.y2 <= mapB.y1 || mapB.y2 <= mapA.y1);
        	return withInter;
        },
        
        'destroySelectionArea' : function() {
        	this.selectArea && this.selectArea.hide();
        },
        
        'releaseSelection' : function(ev) {
            this.destroySelectionArea();
            this.stopListenMousemove();
            this.fire('selectend');
        }
    };
    
    return AreaSelector;
});
