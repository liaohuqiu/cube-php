/**
    Copyright (c) 2011, kaixin001 Inc. All rights reserved.
    http://www.kaixin001.com
    @fileoverview: 本地存储(优先使用localStorage，然后是userData)
    @author: minliang@corp.kaixin001.com
    @date:2011/7/9
*/
define( 'core/Storage', [], function(require){
    /**
        @class
     */
    var Storage = {
        _store: null,
        _getStore: function(){
            if( this._store ){
                return this._store;
            }
            /*创建store*/
            //localStorage
            if( !!window.localStorage ){
                this._store = LocalStorage;
            }
            //userData
            /*
            else{
                this._store = UserDataStorage;
            }
            */
            return this._store;
        },
        isAvailable: function(){
            return !!(this._getStore());
        },
        /**
            写入数据
            @static
            @param {string} key 
            @param {string} val 
         */
        setItem: function( key, val ){
            var st = this._getStore();
            return st && st.setItem( key, val );
        },
        /**
            读取数据
            @static
            @param {string} key 
         */
        getItem: function( key ){
            var st = this._getStore();
            return st && st.getItem( key );
        },
        /**
            删除数据
            @static
            @param {string} key 
         */
        removeItem: function( key ){
            var st = this._getStore();
            return st && st.removeItem( key );
        },
        /**
            清空
            @static
         */
        clear: function( ){
            var st = this._getStore();
            st && st.clear( );
        },
        /**
            监听某个key的变化
            @param {string} key 需要监听的key
            @param {string} callback 当数据发生变化时的回调（回调中传入的参数为当前key对应的值）
         */
        onstorage: function( key, callback ){
            var st = this._getStore();
            st && st.onstorage( key, callback );
        },
        /**
            跨页面同步指定的函数
            @param {function}   func        需要同步执行的函数(不允许重名，函数参数必须为string类型，否则参数无法同步)
            @param {Object}     context     返回的函数执行时的上下文
            @param {Boolean}    onlySync    是否只执行同步，为true时不执行原函数而只执行同步操作
            @return {function}  当执行新的函数时将会通过本地存储进行同步操作
         */
        sync: function( func, context, onlySync ){
            if( !K.isFunction( func ) ) return;
            //生成存储key(对于同一个函数而言，生成的key应该相同，这样才能保证多个页面对于同一个函数监听的是同一个key)
            var key = this._genSyncKey( func );

            //数据项中前缀和数据之间的分隔符
            var split = '~{##}~';

            //参数之间的分隔符
            var argSplit = '~{###}~';

            //是否当前页面触发
            //(IE、Firefox3.6下的onstorage事件不论是否当前页面都会收到，所以需要加锁识别，确保当前页面不响应，其他浏览器则不必)
            var isLocalTrigger = false;

            //监听key变化
            this.onstorage( key, function(val){
                if( !isLocalTrigger || !Storage.isStoreTriggerSelf() ){
                    var args = val.split( split )[1],
                        arrArg = args.split( argSplit );
                    func.apply( context, arrArg);
                }
                isLocalTrigger = false;
            });
            
            var ins = this;
            //返回的函数执行时，传入参数必须为string类型，否则参数无法同步
            return function( ){
                //执行原方法
                if( !onlySync ){
                    func.apply( context, arguments );
                }
                isLocalTrigger = true;
                //同步(加时间戳和随机数是为了让数据项发生变化，否则其他页面无法监听到
                ins.setItem( key, 
                    (new Date())*1 
                    + '' 
                    + Math.random() 
                    + split 
                    + ([].slice.call(arguments).join(argSplit)) 
                );
            };
        },
        /*
            生成函数同步的key
            算法：
                1. func.toString
                2. 获取函数名（因为函数名一般不重名，因此保留整个函数名作为key前缀，但如果是匿名函数则无效）
                3. 将其中的空白符删除(IE下引号等也会导致问题)
                4. 在剩余的内容中平均取N个字符拼接成key
            算法的问题在于不同的函数内容应该不能一样，否则会出现同步异常
            通过调整字符数可以调整精度

            之所以要如此处理是为了确保每个页面对于同一个函数生成的Key是相同的，故不能使用随机数。
            这个算法的问题在于不允许函数重名。
         */
        _genSyncKey: function( func ){
            //key长度
            var keyLen = 30,
                func = func.toString();
            //获取函数名
            var funcName = '',
                reg = /^function\s+([^\(]+)\s*\(/ig,
                matches = reg.exec( func );
            if( matches ){
                funcName = matches[1];
                funcName = funcName.replace(/[^\w]+/ig,'');
            }
            
            func = func.replace(/(function|[^\w]+)/ig,'');

            keyLen -= funcName.length;

            if( keyLen <= 0 ){
                return funcName.substring( 0, 30 );
            }

            if( func.length <= keyLen ){
                return funcName + func;
            }           
            var leftCharLen = keyLen,
                funcLeftLen = func.length,
                step = Math.ceil( func.length / keyLen ),
                key = [];
            for( var i = 0; i < func.length; i += step ){
                key.push( func.substring(i, i+1) );
                leftCharLen--;
                funcLeftLen -= step;
                //检查剩余长度
                if( funcLeftLen <= leftCharLen ){
                    key.push( func.substring(i) );
                    break;
                }
            }
            return funcName + key.join('');
        },
        isStoreUseTimer: function(){
            return this._getStore().useTimer;
        },
        isStoreTriggerSelf: function(){
            return this._getStore().triggerSelf;
        },
        getStoreInterval: function(){
            return this._getStore().interval;
        }
    };

    var LocalStorage = (function(){
            var ls = window.localStorage;
            function _onstorage( key, callback ){
                var oldValue = ls[key];
                /*
                    IE下即使是当前页面触发的数据变更，当前页面也能收到onstorage事件，其他浏览器则只会在其他页面收到
                 */
                return function( e ){
                    //IE下不使用setTimeout尽然获取不到改变后的值?!
                    setTimeout( function(){
                        e = e || window.storageEvent;

                        var tKey = e.key,
                            newValue = e.newValue;
                        //IE下不支持key属性,因此需要根据storage中的数据判断key中的数据是否变化
                        if( !tKey ){
                            var nv = ls[key];
                            if( nv != oldValue ){
                                tKey = key;
                                newValue = nv;
                            }
                            
                        }
                        
                        if( tKey == key ){                  
                            callback && callback(newValue);

                            oldValue = newValue;
                        }
                    }, 0 );
                }
            }
        return {
            getItem: function( key ){
                return ls.getItem( key );
            },
            setItem: function( key, val ){
                return ls.setItem( key, val );
            },
            removeItem: function( key, val ){
                return ls.removeItem( key );
            },
            clear: function(){
                return ls.clear();
            },
            onstorage: function( key, callback ){
                //IE6/7、Chrome使用interval检查更新，其他使用onchange事件
                /*
                Chrome下(14.0.794.0)重写了document.domain之后会导致onstorage不触发
                鉴于onstorage的兼容性问题暂时不使用onstorage事件，改用传统的轮询方式检查数据变化               
                */
                var b = K.Browser;

                if( !this.useTimer ){
                    //IE注册在document上
                    if( document.attachEvent && !K.Browser.opera ) {
                        document.attachEvent("onstorage", _onstorage(key,callback));
                    }
                    //其他注册在window上
                    else{
                        window.addEventListener("storage", _onstorage(key,callback), false);
                    };
                }
                else{
                /*
                    新的检查方式
                 */
                    var listener = _onstorage( key, callback );
                    setInterval(function(){
                        listener({});
                    }, this.interval);  
                }
            },
            //是否使用timer来check
            useTimer: ( K.Browser.ie && K.Browser.ie < 8 ) || ( K.Browser.chrome ),
            //onstorage会响应当前页面对存储数据的修改(IE以及Firefox3.6)
            triggerSelf: K.Browser.ie || ( parseInt( K.Browser.firefox ) < 4 ) ,
            //检查storage是否发生变化的时间间隔
            interval: 1000
        };
    })();

    /*暂未开放*/
    var UserDataStorage = (function(){
        var storeName = 'local_storage';
        function _onstorage( key, callback ){
            var oldValue = UserDataStorage.getItem(key);
            return function( ){
                var newValue = UserDataStorage.getItem(key);
                
                if( oldValue != newValue ){
                    callback && callback(newValue);
                    oldValue = newValue;
                }
            }
        }
        /*userData不允许跨目录，因此通过iframe proxy页面统一进行存储(如果确实需要使用需要在HTML中增加一个iframe，IE下动态创建有Bug*/
        /*
        var iframeProxy = document.createElement('iframe');
            iframeProxy.src = '/interface/userdata_proxy.html';
            iframeProxy.style.display = 'none';

            document.body.appendChild(iframeProxy);
            */
        return {
            _store: null,
            _getStore: function(){
                if(!this._store){
                   try{
                        var doc = iframeProxy.contentWindow.document;

                        this._store = doc.createElement('input');
                        this._store.type = "hidden";
                        this._store.addBehavior("#default#userData");
                        doc.body.appendChild( this._store );
                    }
                    catch(e){
                        var info = [];
                        for( var i in e ){
                            info.push(i + ': ' + e[i] );
                        }
                        document.title = (info.join('\n'));
                        return false;
                    }
                };
                return this._store;
            },
            getItem: function( key ){
                var st = this._getStore();
                if( !st ) return false;
                st.load( storeName );
                return st.getAttribute( key );
            },
            setItem: function( key, val ){
                var st = this._getStore();
                if( !st ) return false;
                st.load( storeName );
                st.setAttribute( key, val );
                st.save( storeName );
            },
            removeItem: function( key, val ){
                var st = this._getStore();
                if( !st ) return false;
                st.load( storeName );
                st.removeAttribute( key );
                st.save( storeName );
            },
            clear: function(){
                var st = this._getStore();
                if( !st ) return false;

                var doc = st.XMLDocument;
                var rootNode = doc.selectSingleNode("ROOTSTUB");
                for (var i = 0; i < rootNode.attributes.length; ++i){
                    var att = rootNode.attributes[i];
                    st.removeAttribute(att.baseName);
                }
                st.save( storeName );
            },
            onstorage: function( key, callback ){
                var listener = _onstorage( key, callback );
                setInterval(function(){
                    listener();
                }, this.interval);      
            },
            isAvailable: function() {
                try{
                    var st = this._getStore();
                    if( !st ) return false;
                    st.save();
                    return true;
                }
                catch( ex ){
                    if( ex.number && Math.abs( parseInt(ex.number) ) == 2146827838 ){
                        return true;
                    }
                    if( ex.description && (ex.description.indexOf("Wrong number") != -1 
                        || ex.description.indexOf("\u9519\u8bef\u7684\u53c2\u6570\u4e2a\u6570") != -1)) {
                        return true;
                    }
                    return false;
                }
            },
            //是否使用timer来check
            useTimer: true,
            //检查storage是否发生变化的时间间隔
            interval: 1000
        };
    })();

    return Storage;
});
