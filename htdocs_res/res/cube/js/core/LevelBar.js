define('core/LevelBar', ['core/jQuery'], function(require) {
    
    var $ = require('core/jQuery');
    
    
    function LevelBar(opts) {
        
        this.container = null; // 滑动条
        this.cursor = null; // 游标
        this.levelNum = 0; // 滑动条上的级别个数
        this.dir = 0; // 滑动条方向(0-水平， 1-垂直)
        this.initLevel = null; // 用户初始化级别
        
        K.mix(this, opts);
        this.level = 0; // 当前级别
        this.holded = false; // 是否按住游标
        this.levelNum = parseInt(this.levelNum, 10);
        
        K.CustEvent.createEvents(this, 'holdbar,setlevel,releasebar');
        //K.CustEvent.createEvents(this, 'touchlevel');
        
        this.init();
    }
    
    LevelBar.prototype = {
        
        'init' : function() {
            if(this.container && this.cursor && this.levelNum > 1) {
                this.initDimension(); // 获得当前滑动条及游标的尺寸信息
                this.bindEvents(); // 绑定事件
            } else {
                K.log('Not enough arguments for LevelBar');
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
            if(this.initLevel) { this.setLevel(this.initLevel-1); } // 设置初始化的级别
        },
        
        'bindEvents' : function() {
            this.cursor
                .bind('mousedown', $.proxy(this.holdCursor, this))
                .bind('click', function(ev) { ev.preventDefault(); });
            this.container.bind('click', $.proxy(this.checkLevel, this));
            //this.container.bind('mouseover', $.proxy(this.overLevel, this));
        },
        
        'holdCursor' : function(ev) {
            this.holded = true;
            this.fire('holdbar', {'level' : this.level + 1});
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
                this.checkLevel(ev);
            }
            ev.preventDefault();
        },
        
        'checkLevel' : function(ev) {
            var clientPosition = this.dir === 0 ? ev.clientX : ev.clientY,
                level = this.clientLevel(clientPosition);
            if(this.level !== level) {
                this.setLevel(level);
            }
        },
        /*
        'overLevel' : function(ev) {
            var clientPosition = this.dir === 0 ? ev.clientX : ev.clientY,
                level = this.clientLevel(clientPosition);
            if(this.level !== level) {
                this.touchLevel(level);
            }
        },
        */

        'clientLevel' : function(position) {
            var basicLevel, extraLevel,
                ratio = (position - this.levelStart)/this.containerScope,
                perRatio = 1/(this.levelNum-1);
                
            ratio = ratio < 0 ? 0 : (ratio > 1 ? 1 : ratio);
            basicLevel = Math.floor(ratio/perRatio);
            extraLevel = Math.round((ratio - (perRatio * basicLevel))/perRatio);
            return basicLevel + extraLevel;
        },
        
        'setLevel' : function(level) {
            var intLevel = parseInt(level, 10);
            intLevel = isNaN(intLevel) ? 0 : intLevel; // NaN容错
            intLevel = intLevel < 0 ? 0 : (intLevel > this.levelNum - 1 ? this.LevelNum - 1 : intLevel); // 范围容错
            this.cursor.css((this.dir === 0 ? 'left' : 'top'), this.containerScope*(intLevel/(this.levelNum-1))-(this.cursorSize/2));
            this.level = intLevel;
            this.fire('setlevel', {'level' : intLevel + 1});
        },
        
        /*
        'touchLevel' : function(level) {
            var intLevel = parseInt(level, 10);
            intLevel = isNaN(intLevel) ? 0 : intLevel; // NaN容错
            intLevel = intLevel < 0 ? 0 : (intLevel > this.levelNum - 1 ? this.LevelNum - 1 : intLevel); // 范围容错
            this.level = intLevel;
            this.fire('touchlevel', {'level' : intLevel + 1});
        },
        */
        
        'releaseCursor' : function() {
            if(this.holded) {
                this.holded = false;
                this.fire('releasebar', {'level' : this.level + 1});
                this.stopListenMouseMove();
            }
        }
    };
    
    return LevelBar;
});
