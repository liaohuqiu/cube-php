/**
 * 个性化首页中选择头部背景图片区域和定位的
 */

define('core/DragAreaSelection', ['core/jQuery'], function(require) {
	
	var $ = require('core/jQuery');
	
	function DragAreaSelection(opts) {
		this.viewport = null;
		this.areaSelector = null;
		this.img = null;
		K.mix(this, opts);
		
		this.moved = false; // 检测是有拖动过图片
		K.CustEvent.createEvents(this, 'movestart,moveend');
		this.init();
	}
	
	DragAreaSelection.prototype = {
		'init' : function() {
			this.position(); // 定位
			this.bindEvents(); // 事件
		},
		
		'position' : function() {},
		
		'bindEvents' : function() {
			this.viewport.mousedown($.proxy(this.holdImg, this));
		},
		
		'holdImg' : function(ev) {
			this.curClientPostion = {'left':ev.pageX, 'top':ev.pageY};
			this.imgPos = this.img.position();
			this.fire('movestart');
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
        		this.moveHandler = $.proxy(this.moveImg, this);
        	}
        	return this.moveHandler;
        },
        
        'getReleaseHandler' : function() {
        	if(!this.releaseHandler) {
        		this.releaseHandler = $.proxy(this.releaseImg, this);
        	}
        	return this.releaseHandler;
        },
        
        'moveImg' : function(ev) {
        	var clientPostion = {'left':ev.pageX, 'top':ev.pageY};
        	this.img.css({
        		'left' : this.imgPos.left + clientPostion.left - this.curClientPostion.left,
        		'top' : this.imgPos.top + clientPostion.top - this.curClientPostion.top
        	});
        	this.moved = true;
        },
        
        'releaseImg' : function(ev) {
        	this.stopListenMousemove();
        	if(this.moved) {
        		this.moved = false;
        		this.fire('moveend', {'position':this.getPosition()});
        	}
        },
        
		'getPosition' : function() {
			var imgPos = this.img.position(),
				selectPos = this.areaSelector.position();
			return {
				'top' : imgPos.top - selectPos.top,
				'left' : imgPos.left - selectPos.left
			}
		}
	};
	
	return DragAreaSelection;
	
});
