define('core/RichInput', ['core/jQuery', 'core/KeyListener'], function(require) {
    
    var $ = require('core/jQuery'),
        KeyListener = require('core/KeyListener'),
        mix = K.mix,
        idPrefix = 'richinput',
        genID = 0;
    
    /**
     * 富输入框
     * @param {Object}  opts    初始化的参数
     *  {
     *      {string|object} container       richinput外面的包装dom对象（selector/jquery 对象/dom对象）
     *      {string|object} input           richinput中的实际的文本输入框dom对象（selector/jquery 对象/dom对象，必填）
     *      {string|object} richInput       richinput中存放有效输入的框体dom对象（selector/jquery 对象/dom对象，必填）
     *      {object}        initData        richinput初始化需要载入的数据项
     *      {string}        itemSelector    richinput每个输入项dom的selector标示（如果默认值与页面不同，则必填）
     *      {string}        itemDelSelector richinput每个输入项的删除链接selector标示（如果默认值与页面不同，则必填）
     *  }
     */
    function RichInput(opts) {
        
        // richinput ID
        var id = idPrefix+'_'+(genID++);
        this.id = id;
        
        // 整个RichInput的容器
        this.container = null;
        
        // 实际的内部输入框
        this.input = null;
        
        // 富输入框
        this.richInput = null;
        
        // 初始化富文本框数据
        this.initData = null;
        
        // 每个输入的item selector
        this.itemSelector = '._j_richInputItem',
        
        // 每个输入item自我删除的触发按钮（链接、图标selector）
        this.itemDelSelector = '._j_richInputItemDel',
        
        // 富文本框实际数据
        this.data = [];
        
        // 最大容纳数据量（0-无限制）
        this.maxLength = 0;
        
        // 合并传入参数
        mix(this, opts);
        
        // 生成删除和和增加条目的事件
        K.CustEvent.createEvents(this, 'additem,delitem,reachlimit');
        
        this.init();
    }
    
    RichInput.prototype = {
        'init' : function() {
            this.addInitData();
            this.bindEvents();
        },
        
        'bindEvents' : function() {
            
            // 绑定元素删除事件
            KeyListener.listen(this.input, 'keydown', 'backspace', $.proxy(this.backspaceDel, this));
            $(this.richInput).delegate(this.itemDelSelector, 'click', $.proxy(this.clickDel, this));
        },
        
        /**
         * 将初始化数据，输入到富文本框中
         */
        'addInitData' : function() {
            if(this.initData) { this.addItems(this.initData); }
        },
        
        /**
         * 添加一条数据到富文本框中
         * @param {object}  输入的单条数据
         */
        'addItem' : function(data) {
            
            // 控制最大长度
            if(this.maxLength > 0 && this.data.length >= this.maxLength) {
                this.fire('reachlimit');
                return false;
            }
            
            var item = this.constructItem(data);
            $(this.richInput).append(item);
        },
        
        /**
         * 将多条数据插入到富文本框中
         * @param {Array}  输入的多条数据
         */
        'addItems' : function(data) {
            var i, html = '', len;
            if(K.isArray(data) && data.length > 0) {
                for(i=0, len = data.length; i<len; i++) {
                    html += this.constructItem(data[i]);
                }
                $(this.richInput).append(html);
            }
        },
        
        /**
         * 删除一条数据
         * @param {number}  删除的数据index
         */
        'delItem' : function(index) {
            var data = this.data.splice(index, 1);
            $(this.richInput).children(this.itemSelector).eq(index).remove();
            this.fire('delitem', {'data':data[0]});
        },
        
        /**
         * 清空所有数据
         */
        'empty' : function() {
            this.data = [];
            $(this.richInput).children(this.itemSelector).remove();
        },
        
        'disable' : function() {
            this.input.prop('disabled', true);
        },
        
        'enable' : function() {
            this.input.prop('disabled', false);
        },
        
        /**
         * 富文本回删处理
         */
        'backspaceDel' : function() {
            var inputValue = $(this.input).val(), 
                itemCount = this.countItem();
            if(inputValue === '' && itemCount > 0) {
                this.delItem(this.countItem()-1);
            }
        },
        
        /**
         * 点击删除一条记录
         */
        'clickDel' : function(ev) {
            this.delItem($(ev.currentTarget).closest(this.itemSelector).index());
            ev.preventDefault();
        },
        
        /**
         * 构建一条数据（添加到this.data并返回html添加到dom）
         */
        'constructItem' : function(data) {
            
            // 每添加一个数据到dom，也将结构化数据添加到this.data
            if(this.validate(data)) {
                this.data = this.data.concat(data);
                this.fire('additem', {'data':data});
                return this.parseItemHTML(data);
            }
        },
        
        /**
         * 生成一条数据的html拼接器，该方法需要根据每个应用的不同进行重写
         */
        'parseItemHTML' : function(data) {
            return K.toString(data);
        },
        
        /**
         * 当前数据个数
         */
        'countItem' : function() {
            return this.data.length;
        },
        
        /**
         * 检测数据是否合法
         */
        'validate' : function(data) {
            return true
        }
    }
    
    return RichInput;
});
