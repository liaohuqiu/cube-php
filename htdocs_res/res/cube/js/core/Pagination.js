define('core/Pagination', ['core/jQuery'], function(require, exports) {
    var $ = require('core/jQuery');
    
    function Pagination(opts) {
        
        this.container = $(opts.container);     // 分页数据输出的page dom区域
        this.viewSize = opts.viewSize;          // 显示页面个数
        this.totalNum = opts.totalNum;          // 总条数
        this.listNum = opts.listNum,            // 每页显示条数
        this.needBE = opts.needBE,              // 是否需要显示首末页
        this.simple = opts.simple,              // 是否一直显示首末、上下页
        this.currClass = opts.currClass || '',  // 当前页的class标示
        this.currPage = opts.currPage || 1,     // 当前页码
        
        this.startTxt = opts.startTxt || '首页',
        this.prevTxt = opts.prevTxt || '上一页',
        this.endTxt = opts.endTxt || '末页',
        this.nextTxt = opts.nextTxt || '下一页',
        
        K.CustEvent.createEvents(this, 'getpage');
        
        this.init();
    }
    
    Pagination.prototype = {
        
        'init' : function() {
            this.refresh();
            this.container.delegate('._j_pgitem', 'click', $.proxy(this.getPage, this));
        },
        
        'getPage' : function(ev) {
            var $target = $(ev.currentTarget),
                pgnum = $target.data('pgnum');
                
            this.fire('getpage', {'data' : pgnum});
            this.currPage = pgnum;
            this.refresh();
            ev.preventDefault();
        },
        
        'refresh' : function() {
            this.container.html(Pagination.genHTML(this));
        }
        
    };
    
    Pagination.genHTML = function(data) { 
        var viewSize = data.viewSize,           // 显示页面个数
            totalNum = data.totalNum,           // 总条数
            listNum = data.listNum,             // 每页显示条数
            needBE = data.needBE,               // 是否需要显示首末页
            simple = data.simple,               // 是否一直显示首末、上下页
            currClass = data.currClass || '',   // 当前页的class标示
            currPage = data.currPage || 1,      // 当前页码
            
            startTxt = data.startTxt || '首页',
            prevTxt = data.prevTxt || '上一页',
            endTxt = data.endTxt || '末页',
            nextTxt = data.nextTxt || '下一页',
            
            itemClass = '_j_pgitem' + (data.itemClass ? ' ' + data.itemClass : ''),
            totalPage, startPage, endPage, i,
            html = '';
            
        totalPage = Math.ceil(totalNum/listNum);
        currPage = currPage > totalPage ? totalPage : currPage;
        startPage = currPage - Math.floor(viewSize/2);
        startPage = startPage < 1 ? 1 : (startPage > totalPage ? totalPage : startPage);
        endPage = startPage + viewSize - 1;
        endPage = endPage > totalPage ? totalPage : (endPage < startPage ? startPage : endPage);
        
        if(endPage > startPage) {
            
            if(!simple || startPage > 1) {
                if(needBE) {
                    html += '<a class="_j_pgbegin word ' + itemClass + '" href="#" data-pgnum="1"><span>' + startTxt + '</span></a>';
                }
                html += '<a class="_j_pgprev word ' + itemClass + '" href="#" data-pgnum="' + (currPage <= 1 ? 1 : (currPage - 1)) + '"><span>' + prevTxt + '</span></a>';
            }
            
            for(i=startPage; i<=endPage; i++) {
                html += '<a class="' + ((i !== currPage) ? itemClass : itemClass + ' ' + currClass) + '" href="#" data-pgnum="' + i + '"><span>' + i + '</span></a>';
            }
            
            if(!simple || (endPage < totalPage)) {
                html += '<a class="_j_pgnext word ' + itemClass + '" href="#" data-pgnum="' + (currPage >= totalPage ? totalPage : (currPage + 1)) + '"><span>' + nextTxt + '</span></a>';
                if(needBE) {
                    html += '<a class="_j_pgend word ' + itemClass + '" href="#" data-pgnum="' + totalPage + '"><span>' + endTxt + '</span></a>';
                }
            }
            
        }
        
        return html;
    };
    
    return Pagination;
});
