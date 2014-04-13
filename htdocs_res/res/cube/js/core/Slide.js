define('core/Slide', ['core/jQuery'], function(require) {

    var $ = require('core/jQuery'),
        mix = K.mix;

    function Slide(opts) {

        this.viewSize = 1;

        this.slideCnt = null;

        this.slideList = null;

        this.prev = $();

        this.next = $();

        this.indexer = $();

        this.itemSize = null;

        this.slideTime = 200;

        this.slideSize = 1;

        this.indexerOnClass = 'on';

        this.disabledClass = 'disabled';

        this.indexAttr = 'index';

        this.shStyle = 'slide';

        this.index = 1;

        mix(this, opts);

        K.CustEvent.createEvents(this, 'slide');

        this.init();
    }

    Slide.prototype = {

        'init' : function() {
            this.hasMore = true;
            this.bindEvents();
            this.updateStatus();
        },

        'bindEvents' : function() {
            this.prev.click($.proxy(this.toPrev, this));
            this.next.click($.proxy(this.toNext, this));
            this.indexer.click($.proxy(this.toIndex, this));
        },

        'toPrev' : function(ev) {
            if(!this.prev.hasClass(this.disabledClass) && !this.sliding) { this.slide(-this.slideSize); }
            ev.preventDefault();
        },

        'toNext' : function(ev) {
            if(!this.next.hasClass(this.disabledClass) && !this.sliding) { this.slide(this.slideSize); }
            ev.preventDefault();
        },

        'toIndex' : function(ev) {
            var $target = $(ev.currentTarget),
                index = parseInt($target.data(this.indexAttr), 10);

            if(!$target.hasClass(this.indexerOnClass) && !this.sliding && !isNaN(index)) {
                this.slide(index - this.index);
            }
            ev.preventDefault();
        },

        'updateStatus' : function() {
            if(this.index === 1) {
                this.prev.addClass(this.disabledClass);
            } else {
                this.prev.removeClass(this.disabledClass);
            }

            if((this.index + this.viewSize - 1) >= $(this.slideList).length && !this.hasMore) {
                this.next.addClass(this.disabledClass);
            } else {
                this.next.removeClass(this.disabledClass);
            }

            // 更新标示页码的点得选中状态
            this.indexer.filter('.'+this.indexerOnClass).removeClass(this.indexerOnClass);
            this.indexer.filter('[data-'+this.indexAttr+'="'+this.index+'"]').addClass(this.indexerOnClass);

            this.fire('slide', {'data':{
                'index':this.index,
                'total':$(this.slideList).length
            }});
        },

        'slide' : function(num) {
            this.sliding = true;
            this.realSlideNum = num; // 用以控制在后续切换列表不足以满足切换的size时，获取真实的切换size
            this.prepareData($.proxy(function() {
                var _this = this;
                Slide.shower[this.shStyle].show(this, function() {
                    _this.index += _this.realSlideNum;
                    _this.sliding = false;
                    _this.updateStatus();
                });
            }, this));
        },

        /**
         * 此处会做异步请求，准备数据，异步请求时该方法需要被覆盖，控制hasMore属性
         */
        'prepareData' : function(callback) {
            var num = this.realSlideNum,
                slideListNum = $(this.slideList).length,
                callback = typeof callback === 'function' ? callback : $.noop,
                nextListNum = num >= 0 ? (slideListNum - this.index - this.viewSize + 1) : (this.index - 1);

            if(nextListNum <= 1) {
                this.hasMore = false;
            }

            if(nextListNum <= 0) {
                this.realSlideNum = 0;
            } else {
                this.realSlideNum = Math.abs(num) > nextListNum ? nextListNum : num;
                if(num < 0 && this.realSlideNum > 0) {
                    this.realSlideNum = -this.realSlideNum;
                }
            }
            callback();
        },

        'reset' : function() {
            this.index = 1;
            this.hasMore = true;
            this.updateStatus();

            if(this.shStyle === 'slide') {
                this.slideCnt.css('left', 0);
            }
        }
    };

    Slide.shower = {

        'slide' : {
            'show' : function(slide, callback) {
                var nowPreLength = parseInt($(slide.slideCnt).css('left'), 10),
                    distance = slide.itemSize * slide.realSlideNum;

                nowPreLength = isNaN(nowPreLength) ? 0 : nowPreLength,
                $(slide.slideCnt).animate({
                    'left':nowPreLength - distance
                }, slide.slideTime, function() {
                    callback();
                });
            }
        },

        'fadeInOut' : {

            'show' : function(slide, callback) {
                var nowItem = $(slide.slideList[slide.index - 1]),
                    nextItem = $(slide.slideList[slide.index + slide.realSlideNum - 1]);

                nowItem.fadeOut(slide.slideTime);
                nextItem.fadeIn(slide.slideTime, function() {
                    callback();
                });
            }
        }
    };

    return Slide;
});
