define('core/RangeBar', ['core/jQuery'], function(require) {
    
    var $ = require('core/jQuery');
    
    
    function RangeBar(opts) {
        
        this.container = null; // 滑动条
        this.cursor = null; // 游标
        this.dir = 0; // 滑动条方向(0-水平， 1-垂直)
        this.initRange = null; // 初始化Range
        
        K.mix(this, opts);
        this.holded = false; // 是否按住游标
        this.position = 0; // 当前范围 
        K.CustEvent.createEvents(this, 'setrange');
        
        this.init();
    }
    
    RangeBar.prototype = {
        
        'init' : function() {
            if(this.container && this.cursor) {
                this.initDimension(); // 获得当前滑动条及游标的尺寸信息
                this.bindEvents(); // 绑定事件
            } else {
                K.log('Not enough arguments for RangeBar');
            }
        },
        
        'initDimension' : function() {
            var container = this.container,
                offset = container.offset(),
                left = offset.left, top = offset.top;
                
            this.levelStart = this.dir === 0 ? left : top; // 滑动条的开始点位置
            this.levelEnd = this.dir === 0 ? left + container.outerWidth() : top + container.outerHeight(); // 滑动条结束点位置
            this.cursorSize = this.dir === 0 ? this.cursor.outerWidth() : this.cursor.outerHeight(); // 游标大小
            this.containerScope = this.levelEnd - this.levelStart; // 滑动条长度
            if(this.initRange) {
                this.setRange(this.initRange * this.containerScope); 
            }
        },
        
        'bindEvents' : function() {
            this.cursor
                .bind('mousedown', $.proxy(this.holdCursor, this))
                .bind('click', function(ev) { ev.preventDefault(); });
            this.container.bind('click', $.proxy(this.checkRange, this));
        },
        
        'holdCursor' : function(ev) {
            this.holded = true;
            this.listenMouseMove();
            ev.preventDefault();
        },
        
        'listenMouseMove' : function() {
            $(document)
                .bind('mousemove', $.proxy(this.moveCursor, this))
                .bind('mouseup', $.proxy(this.releaseCursor, this));
        },
        
        'stopListenMouseMove' : function() {
            $(document)
                .unbind('mousemove', $.proxy(this.moveCursor, this))
                .unbind('mouseup', $.proxy(this.releaseCursor, this));
        },
        
        'moveCursor' : function(ev) {
            if(this.holded) {
                this.checkRange(ev);
            }
            ev.preventDefault();
        },
        
        'checkRange' : function(ev) {
            var clientPosition = this.dir === 0 ? ev.clientX : ev.clientY,
                position = this.clientPosition(clientPosition);
            if(this.position !== position) {
                this.setRange(position);
            }
        },
        
        'clientPosition' : function(position) {
            var cursorPosition = position - this.levelStart;
                
            cursorPosition = cursorPosition < 0 ? 0 : (cursorPosition > this.containerScope ? this.containerScope : cursorPosition);
            return cursorPosition;
        },
        
        'setRange' : function(position) {
            position = position < 0 ? 0 : position > this.containerScope ? this.containerScope : position;
            this.position = position;
            this.cursor.css((this.dir === 0 ? 'left' : 'top'), position - this.cursorSize/2);
            this.fire('setrange', {'range' : position/this.containerScope});
        },
        
        'releaseCursor' : function() {
            if(this.holded) {
                this.holded = false;
                this.stopListenMouseMove();
            }
        }
    };
    
    return RangeBar;
});
