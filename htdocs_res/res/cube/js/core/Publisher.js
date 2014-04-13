define('core/Publisher', ['core/jQuery', 'core/ajax/Ajax', 'core/KeyListener', 'core/Caret'], function(require) {
    
    var $ = require('core/jQuery'),
        KeyListener = require('core/KeyListener'),
        Caret = require('core/Caret'),
        Ajax = require('core/ajax/Ajax'),
        mix = K.mix,
        idPrefix = 'pubbox',
        genID = 0;
    
    /**
     * @param {Object}  opts    初始化的参数
     *  {
     *      {string|object} pubArea         发布框对应的页面dom对象（selector/jquery 对象/dom对象，必填）
     *      {string}        pubURL          发布框发布地址（必填）
     *      {string}        pubMethod       发布采用的http提交方式（POST-默认,GET） 
     *      {string}        pubKeyAction    发布框绑定的键盘提交事件方式（相见KeyListener中说明）
     *      {object}        pubData         发布提交的内容（默认：{value:'enter value'}）
     *      {string}        dataType        发布响应的返回值格式（JSON-默认）
     *      {function}      pubCheck        发布前内容合法性检测程序
     *      {function}      filter          发布内容的过滤程序
     *      {string}        initValue       发布框初始化内容
     *  }
     */
    function Publisher(opts) {
        
        // 发布框ID
        var id = idPrefix+'_'+(genID++);
        this.id = id;
        
        // 发布框
        this.pubArea = null;
        
        // 发布按钮
        this.pubButton = null;
        
        // 发布地址
        this.pubURL = null;
        
        // 发布方法（GET POST）
        this.pubMethod = 'POST';
        
        // 发布框接受的返回数据格式
        this.dataType = 'json';
        
        // 发布框数据
        this.pubData = {};
        
        // 支持的键盘发布动作（默认：ctrl+enter [holderkey+otherkey | key]）
        this.pubKeyAction = 'ctrl+enter';
        
        // 发布框内容有效性检测
        this.pubCheck = function() { return true };
        
        // 发布框内容过滤器
        this.filter = null;

        // 发布框初始化填入数据（如果是默认提示语，聚焦就消失之类的，请不要使用此属性，placeholder才是你需要的）
        // 适用于发布内容的一部分如：话题(#郭美美#)
        this.initValue = ''; 
        
        // 合并传入参数
        mix(this, opts);
        
        // 为publish创建动作事件
        K.CustEvent.createEvents(this, 'pubcheckfail,beforepublish,afterpublish,publishsuccess,publishfail');
        
        // 初始化
        this.init();
    }
    
    Publisher.prototype = {
        
        /**
         * 初始化
         */
        'init' : function() {
            if(!this.pubURL || !this.pubArea) { // 未初始化发布地址，不做任何事件
                K.log("No publish url or area for publisher:" + this.id);
                return;
            }
            
            if(this.initValue) {
                $(this.pubArea).val(this.initValue); // 初始化填入数据
            }
            
            this.bindEvents(); // 绑定发布相关事件
        },
        
        /**
         * 事件绑定
         */
        'bindEvents' : function() {
            
            // 键盘发布事件监听
            if(this.pubKeyAction) {
                KeyListener.listen(this.pubArea, 'keydown', this.pubKeyAction, $.proxy(this.publish, this));
            }
            
            // submit按钮事件
            if(this.pubButton) {
                $(this.pubButton).bind('click', $.proxy(function(ev) {
                    if(!$(ev.currentTarget).hasClass('disabled')) {
                        this.publish(ev);
                    }
                    ev.preventDefault();
                }, this));
            }
            
            // reset按钮事件
            if(this.resetButton) {
                $(this.resetButton).bind('click', $.proxy(this.reset, this));
            }
            
            this.pubArea.bind('insertcontent', $.proxy(function(ev, content) {
                this.insertContent(content);
            }, this));
        },
        
        /**
         * 发布
         */
        'publish' : function(ev) {
            var _this = this,
                pubValue = $(this.pubArea).val();
            K.log(pubValue);
            if(!this.pubing && this.pubCheck(pubValue)) {
                this.pubing = true;
                this.fire('beforepublish', {'data':pubValue});
                if(this.filter) { pubValue = this.filter(pubValue); }
                mix(this.pubData, {'content':pubValue});
                Ajax.ajax({
                    'url':this.pubURL,
                    'type':this.pubMethod,
                    'data':this.pubData,
                    'dataType':this.dataType,
                    'success': function(data) {
                        _this.fire('publishsuccess', {'data':data});
                    },
                    'error': function(error) {
                        _this.fire('publishfail', {'data':error});
                    },
                    'complete': function() {
                        _this.pubing = false;
                        _this.fire('afterpublish');
                    }
                });
            } else {
                this.fire('pubcheckfail', {'data':pubValue});
            }
            ev && ev.preventDefault();
        },
        
        /**
         * 重置(发布内容的一部分如：话题(#郭美美#))
         */
        'reset' : function() {
            $(this.pubArea).val(this.initValue); // 初始化填入数据
        },
        
        /**
         * 追加内容到发布框光标位置
         * @param   {string}    content 追加的内容
         */
        'insertContent' : function(content) {
            Caret.insertContent(this.pubArea, content);
        }
    };
    
    return Publisher;
});
