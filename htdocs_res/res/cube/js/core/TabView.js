define('core/TabView', ['core/jQuery'], function(require) {
    
    var $ = require('core/jQuery');
    
    function TabView(opts) {
        
        this.tabList = null;
        
        this.contentCnt = null;
        
        this.itemClass = '_j_tabcntitem';
        
        this.tabOnClass = 'on';
        
        this.asyncLoad = false;
        
        this.initIndex = 0;
        
        this.shStyle = 'showHide';
        
        K.mix(this, opts);
        
        this.currItem = $();
        
        // jQ 封装
        this.tabList = $(this.tabList);
        this.contentCnt = $(this.contentCnt);
        
        this.init();
    }
    
    TabView.prototype = {
        
        'init' : function() {
            this.loadFirstTab();
            this.bindEvents();
        },
        
        'bindEvents' : function() {
            this.tabList.click($.proxy(this.loadTab, this));
        },
        
        'loadFirstTab' : function() {
            this.loadTabContent($(this.tabList[this.initIndex]));
        },
        
        'loadTab' : function(ev) {
            var $target = $(ev.currentTarget);
            if(!$target.hasClass(this.tabOnClass)) {
                this.loadTabContent($target);
            }
            ev.preventDefault();
        },
        
        'loadTabContent' : function($target) {
            this.tabList.filter('.' + this.tabOnClass).removeClass(this.tabOnClass);
            $target.addClass(this.tabOnClass);
            this.prepareData($target, $.proxy(function() {
                this.viewItem($target);
            }, this));
        },
        
        'viewItem' : function($target) {
            var nextItem = this.getTargetItem($target);
            this.showTargetItem(this.currItem, nextItem);
            this.currItem = nextItem;
        },
        
        'getTargetItem' : function($target) {
            return this.contentCnt.children('.' + this.itemClass + ':eq(' + $target.index() + ')');
        },
        
        'showTargetItem' : function(currItem, nextItem) {
            TabView.shower[this.shStyle](currItem, nextItem);
        },
        
        'prepareData' : function($target, callback) {
            typeof callback === 'function' && callback();
        }
        
    };
    
    TabView.shower = {
        'showHide' : function(currItem, nextItem) {
            currItem.hide();
            nextItem.show();
        },
        
        'fadeInOut' : function(currItem, nextItem) {
            currItem.fadeOut(function() {
                nextItem.fadeIn();
            });
        }
    };
    
    return TabView;
    
});
