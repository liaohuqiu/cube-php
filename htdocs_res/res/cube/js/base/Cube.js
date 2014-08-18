// Global Namespace
var K = this.K || {};

// Loading Resource
K.Resource = K.Resource || {};

K.error = function(obj, type) {
    type = type || Error;
    throw new type(obj);
};

K.global = this;

// Tool function
(function(K) {

    var ArrayProto = Array.prototype, ObjProto = Object.prototype, FuncProto = Function.prototype;

    var slice            = ArrayProto.slice,
    unshift          = ArrayProto.unshift,
    toString         = ObjProto.toString,
    hasOwnProperty   = ObjProto.hasOwnProperty;

    // ECMAScript 5
    var
    nativeForEach      = ArrayProto.forEach,
    nativeMap          = ArrayProto.map,
    nativeReduce       = ArrayProto.reduce,
    nativeReduceRight  = ArrayProto.reduceRight,
    nativeFilter       = ArrayProto.filter,
    nativeEvery        = ArrayProto.every,
    nativeSome         = ArrayProto.some,
    nativeIndexOf      = ArrayProto.indexOf,
    nativeLastIndexOf  = ArrayProto.lastIndexOf,
    nativeIsArray      = Array.isArray,
    nativeKeys         = Object.keys,
    nativeCreate     = Object.create,
    nativeBind         = FuncProto.bind;

    // Debug
    K.log = function() {
        if (! K.__debug) {
            return;
        }
        if ('console' in window && 'log' in window.console) {
            var len = arguments.length;
            if (len == 1) {
                window.console.log(arguments[0]);
            } else if (len == 2) {
                window.console.log(arguments[0], arguments[1]);
            }
        } else if (window.opera && window.opera.postError) {
            window.opera.postError.apply(window.opera, arguments);
        }
    };

    // Array / Set
    // -----------------

    var breaker = {};

    // foreach
    K.forEach = function(obj, iterator, context) {
        if (obj == null) {
            return;
        }
        if (nativeForEach && obj.forEach === nativeForEach) {
            obj.forEach(iterator, context);
        } else if (K.isNumber(obj.length)) {
            for (var i = 0, l = obj.length; i < l; i++) {
                if (iterator.call(context, obj[i], i, obj) == breaker) {
                    return;
                }
            }
        } else {
            for (var key in obj) {
                if (hasOwnProperty.call(obj, key)) {
                    if (iterator.call(context, obj[key], key, obj) === breaker) {
                        return;
                    }
                }
            }
        }
    };

    // map the property, use the return value.
    K.map = function(obj, iterator, context) {
        var results = [];
        if (obj == null) {
            return results;
        }
        if (nativeMap && obj.map === nativeMap) {
            return obj.map(iterator, context);
        }
        K.forEach(obj, function(value, index, list) {
            results[results.length] = iterator.call(context, value, index, list);
        });
        return results;
    };

    // reduce
    K.reduce = function(obj, iterator, memo, context) {
        var initial = memo !== void 0;
        if (obj == null) {
            obj = [];
        }
        if (nativeReduce && obj.reduce === nativeReduce) {
            if (context) {
                iterator = K.bind(iterator, context);
            }
            return initial ? obj.reduce(iterator, memo) : obj.reduce(iterator);
        }
        K.forEach(obj, function(value, index, list) {
            if (!initial && index === 0) {
                memo = value;
                initial = true;
            } else {
                memo = iterator.call(context, memo, value, index, list);
            }
        });
        if (!initial) {
            throw new TypeError("Reduce of empty array with no initial value");
        }
        return memo;
    };

    // reduce reverse
    K.reduceRight = function(obj, iterator, memo, context) {
        if (obj == null) {
            obj = [];
        }
        if (nativeReduceRight && obj.reduceRight === nativeReduceRight) {
            if (context) iterator = K.bind(iterator, context);
            return memo !== void 0 ? obj.reduceRight(iterator, memo) : obj.reduceRight(iterator);
        }
        var reversed = (K.isArray(obj) ? obj.slice() : K.toArray(obj)).reverse();
        return K.reduce(reversed, iterator, memo, context);
    };

    // find the first one
    K.detect = function(obj, iterator, context) {
        var result;
        K.some(obj, function(value, index, list) {
            if (iterator.call(context, value, index, list)) {
                result = value;
                return true;
            }
        });
        return result;
    };

    // filter
    K.filter = function(obj, iterator, context) {
        var results = [];
        if (obj == null) {
            return results;
        }
        if (nativeFilter && obj.filter === nativeFilter) {
            return obj.filter(iterator, context);
        }
        K.forEach(obj, function(value, index, list) {
            if (iterator.call(context, value, index, list)) results[results.length] = value;
        });
        return results;
    };

    // check every
    K.every = function(obj, iterator, context) {
        var result = true;
        if (obj == null) {
            return result;
        }
        if (nativeEvery && obj.every === nativeEvery) {
            return obj.every(iterator, context);
        }
        K.forEach(obj, function(value, index, list) {
            if (!(result = result && iterator.call(context, value, index, list))) {
                return breaker;
            }
        });
        return result;
    };

    // has some
    K.some = function(obj, iterator, context) {
        iterator || (iterator = K.identity);
        var result = false;
        if (obj == null) {
            return result;
        }
        if (nativeSome && obj.some === nativeSome) {
            return obj.some(iterator, context);
        }
        K.forEach(obj, function(value, index, list) {
            if (iterator.call(context, value, index, list)) {
                result = true;
                return breaker;
            }
        });
        return result;
    };

    // contains
    K.contains = function(obj, target) {
        var found = false;
        if (obj == null) {
            return found;
        }
        if (nativeIndexOf && obj.indexOf === nativeIndexOf) {
            return obj.indexOf(target) != -1;
        }
        K.some(obj, function(value) {
            if (found = value === target) {
                return true;
            }
        });
        return found;
    };

    // get fields
    K.pluck = function(obj, key) {
        return K.map(obj, function(value) {
            return value[key];
        });
    };

    // sort
    K.sortBy = function(obj, iterator, context) {
        return K.pluck(K.map(obj, function(value, index, list) {
            return {
                value : value,
                criteria : iterator.call(context, value, index, list)
            };
        }).sort(function(left, right) {
            var a = left.criteria, b = right.criteria;
            return a < b ? -1 : a > b ? 1 : 0;
        }), 'value');
    };

    // Use a comparator function to figure out at what index an object should
    // be inserted so as to maintain order. Uses binary search.
    K.sortedIndex = function(array, obj, iterator) {
        iterator || (iterator = K.identity);
        var low = 0, high = array.length;
        while (low < high) {
            var mid = (low + high) >> 1;
            iterator(array[mid]) < iterator(obj) ? low = mid + 1 : high = mid;
        }
        return low;
    };

    // toArray
    K.toArray = function(iterable) {
        if (!iterable)                return [];
        if (iterable.toArray)         return iterable.toArray();
        if (K.isArray(iterable))      return iterable;
        if (K.isArguments(iterable))  return slice.call(iterable);
        return K.values(iterable);
    };

    // Array
    // ---------------

    // last
    K.last = function(array) {
        var len = array.length;
        return len > 0 ? array[ len - 1 ] : undefined;
    };

    // return which is true
    K.compact = function(array) {
        return K.filter(array, function(value) {
            return !!value;
        });
    };

    // flatten
    K.flatten = function(array) {
        return K.reduce(array, function(memo, value) {
            if (K.isArray(value)) {
                return memo.concat(K.flatten(value));
            }
            memo[memo.length] = value;
            return memo;
        }, []);
    };

    // remove
    K.without = function(array, obj) {
        var values = slice.call(arguments, 1);
        return K.filter(array, function(value) {
            return !K.contains(values, value);
        });
    };

    // unique
    K.unique = function(array, isSorted) {
        return K.reduce(array, function(memo, el, i) {
            if (0 == i || (isSorted === true ? K.last(memo) != el : !K.contains(memo, el))) memo[memo.length] = el;
            return memo;
        }, []);
    };

    // intersect
    K.intersect = function(array) {
        var rest = slice.call(arguments, 1);
        return K.filter(K.unique(array), function(item) {
            return K.every(rest, function(other) {
                return K.indexOf(other, item) >= 0;
            });
        });
    };

    // indexOf
    K.indexOf = function(array, item, isSorted) {
        if (array == null) return -1;
        var i, l;
        if (isSorted) {
            i = K.sortedIndex(array, item);
            return array[i] === item ? i : -1;
        }
        if (nativeIndexOf && array.indexOf === nativeIndexOf) {
            return array.indexOf(item);
        }
        for (i = 0, l = array.length; i < l; i++) if (array[i] === item) {
            return i;
        }
        return -1;
    };

    // lastIndexOf
    K.lastIndexOf = function(array, item) {
        if (array == null) return -1;
        if (nativeLastIndexOf && array.lastIndexOf === nativeLastIndexOf) {
            return array.lastIndexOf(item);
        }
        var i = array.length;
        while (i--) if (array[i] === item) {
            return i;
        }
        return -1;
    };

    // Function
    // ------------------

    // bind
    K.bind = function(func, context) {
        var extraArgs = Array.prototype.slice.call(arguments, 2);
        return function() {
            context = context || (this == K.global ? false : this);
            var args = extraArgs.concat(Array.prototype.slice.call(arguments));
            if (typeof(func) == "string" && context[func]) {
                context[func].apply(context, args);
            } else if (K.isFunction(func)) {
                return func.apply(context, args);
            } else {
            }
        };
    };

    // methodize, the first will be: this, or this[attr]
    K.methodize = function(func, attr) {
        if (attr) {
            return function() {
                return func.apply(null, [this[attr]].concat([].slice.call(arguments)));
            };
        }
        return function() {
            return func.apply(null, [this].concat([].slice.call(arguments)));
        };
    };

    K.extend = function(klass, proto) {

        var T = function() {}; //构造prototype-chain
        T.prototype = proto.prototype;

        var klassProto = klass.prototype;

        klass.prototype = new T();

        // coyp the methods if the prototype has some
        K.mix(klass.prototype, klassProto, true);

        K.mix(klass, proto);
        klass.$super = proto; // use arguments.callee.$super to call parent construct.

        return klass;
    };

    // 以某对象为原型创建一个新的对象
    K.create = nativeCreate || function(proto, props) {
        var ctor = function(ps) {
            if (ps) {
                K.mix(this, ps, true);
            }
        };
        ctor.prototype = proto;
        return new ctor(props);
    };

    // delay
    K.delay = function(func, wait) {
        var args = slice.call(arguments, 2);
        return setTimeout(function() {
            return func.apply(func, args);
        }, wait);
    };

    K.defer = function(func) {
        return K.delay.apply(K, [func, 1].concat(slice.call(arguments, 1)));
    };

    // Object
    // ----------------

    // 得到一个对象中所有可以被枚举出的属性的列表
    K.keys = nativeKeys || function(obj) {
        if (obj !== Object(obj)) {
            throw new TypeError('Invalid object');
        }
        var keys = [];
        for (var key in obj) if (hasOwnProperty.call(obj, key)) {
            keys[keys.length] = key;
        }
        return keys;
    };

    // 得到一个对象中所有可以被枚举出的属性值的列表
    K.values = function(obj) {
        return K.map(obj, K.identity);
    };

    // 得到一个对象中所有可以被枚举出的方法列表
    K.methods = function(obj) {
        return K.filter(K.keys(obj), function(key) {
            return K.isFunction(obj[key]);
        }).sort();
    };

    // 将源对象的属性并入到目标对象
    K.mix = function(obj) {
        K.forEach(slice.call(arguments, 1), function(source) {
            for (var prop in source) if (source[prop] !== void 0) {
                obj[prop] = source[prop];
            }
        });
        return obj;
    };

    // 克隆
    K.clone = function(obj) {
        return K.isArray(obj) ? obj.slice() : K.mix({}, obj);
    };

    // 是否为空数组或对象
    K.isEmpty = function(obj) {
        if (K.isArray(obj) || K.isString(obj)) {
            return obj.length === 0;
        }
        for (var key in obj) if (hasOwnProperty.call(obj, key)) {
            return false;
        }
        return true;
    };

    // 判断一个变量是否是Html的Element元素
    K.isElement = function(obj) {
        return !!(obj && obj.nodeType == 1);
    };

    // 判断对象是否为数组
    K.isArray = nativeIsArray || function(obj) {
        return toString.call(obj) === '[object Array]';
    };

    // 判断对象是否为函数
    K.isFunction = function(obj) {
        return !!(obj && obj.constructor && obj.call && obj.apply);
    };

    if (toString.call(arguments) == '[object Arguments]') {
        K.isArguments = function(obj) {
            return toString.call(obj) == '[object Arguments]';
        };
    } else {
        K.isArguments = function(obj) {
            return !!(obj && hasOwnProperty.call(obj, 'callee'));
        };
    }


    // 判断对象是否为字符串
    K.isString = function(obj) {
        return !!(obj === '' || (obj && obj.charCodeAt && obj.substr));
    };

    // 判断对象是否为数字
    K.isNumber = function(obj) {
        return !!(obj === 0 || (obj && obj.toExponential && obj.toFixed));
    };

    // 判断对象是否是Nan
    K.isNaN = function(obj) {
        return obj !== obj;
    };

    // 判断对象是否为boolean类型
    K.isBoolean = function(obj) {
        return obj === true || obj === false;
    };

    // 判断对象是否为Date类型
    K.isDate = function(obj) {
        return !!(obj && obj.getTimezoneOffset && obj.setUTCFullYear);
    };

    // 判断对象是否为正则
    K.isRegExp = function(obj) {
        return !!(obj && obj.test && obj.exec && (obj.ignoreCase || obj.ignoreCase === false));
    };

    // 判断对象是否为null
    K.isNull = function(obj) {
        return obj === null;
    };

    // 判断对象是否为undefined
    K.isUndefined = function(obj) {
        return obj === void 0;
    };

    // 字符函数
    // -----------------

    // 除去字符串两边的空白字符
    K.trim = function(str) {
        return str.replace(/^[\s\xa0\u3000]+|[\u3000\xa0\s]+$/g, "");
    };

    // 得到字节长度
    K.byteLen = function(str) {
        return str.replace(/[^\x00-\xff]/g, "--").length;
    };

    // 得到指定字节长度的子字符串
    K.subByte = function(str, len, tail) {
        if (K.byteLen(str) <= len) {
            return str;
        }
        tail = tail || '';
        len -= K.byteLen(tail);
        return str.substr(0, len).replace(/([^\x00-\xff])/g, "$1 ") //双字节字符替换成两个
        .substr(0, len) // 截取长度
        .replace(/[^\x00-\xff]$/, "") //去掉临界双字节字符
        .replace(/([^\x00-\xff]) /g, "$1") + tail; //还原
    };

    // 字符串为javascript转码
    K.encode4JS = function(str) {
        return str.replace(/\\/g, "\\u005C")
        .replace(/"/g, "\\u0022")
        .replace(/'/g, "\\u0027")
        .replace(/\//g, "\\u002F")
            .replace(/\r/g, "\\u000A")
        .replace(/\n/g, "\\u000D")
        .replace(/\t/g, "\\u0009");
    };

    // 为http的不可见字符、不安全字符、保留字符作转码
    K.encode4HTTP = function(str) {
        return str.replace(/[\u0000-\u0020\u0080-\u00ff\s"'#\/\|\\%<>\[\]\{\}\^~;\?\:@=&]/, function(s) {
            return encodeURIComponent(s);
        });
    };

    /**
    * 字符串为Html转码
    * @method encode4Html
    * @static
    * @param {String} s 字符串
    * @return {String} 返回处理后的字符串
    * @example
    var s="<div>dd";
    alert(encode4Html(s));
    */
    K.encode4Html = function(s){
        return s.replace(/&(?!\w+;|#\d+;|#x[\da-f]+;)/gi, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#x27;').replace(/\//g,'&#x2F;');
    };

        /**
        * 字符串为Html的value值转码
        * @method encode4HtmlValue
        * @static
        * @param {String} s 字符串
        * @return {String} 返回处理后的字符串
        * @example:
        var s="<div>\"\'ddd";
        alert("<input value='"+encode4HtmlValue(s)+"'>");
        */
        K.encode4HtmlValue = function(s){
            return K.encode4Html(s).replace(/"/g,"&quot;").replace(/'/g,"&#039;");
        }

        // 将所有tag标签消除，即去除<tag>，以及</tag>
        K.stripTags = function(str) {
            return str.replace(/<[^>]*>/gi, '');
        };

        //日期函数
        //------------------
        /**
        * 格式化日期
        * @method format
        * @static
        * @param {Date} d 日期对象
        * @param {string} pattern 日期格式(y年M月d天h时m分s秒)，默认为"yyyy-MM-dd"
        * @return {string}  返回format后的字符串
        * @example
        var d=new Date();
        alert(format(d," yyyy年M月d日\n yyyy-MM-dd\n MM-dd-yy\n yyyy-MM-dd hh:mm:ss"));
        */
        K.formatDate = function(d,pattern){
            pattern=pattern||"yyyy-MM-dd";
            var y=d.getFullYear();
            var o = {
                "M" : d.getMonth()+1, //month
                "d" : d.getDate(),    //day
                "h" : d.getHours(),   //hour
                "m" : d.getMinutes(), //minute
                "s" : d.getSeconds() //second
            }
            pattern=pattern.replace(/(y+)/ig,function(a,b){var len=Math.min(4,b.length);return (y+"").substr(4-len);});
            for(var i in o){
                pattern=pattern.replace(new RegExp("("+i+"+)","g"),function(a,b){return (o[i]<10 && b.length>1)? "0"+o[i] : o[i]});
            }
            return pattern;
        }

        // 工具函数
        // -----------------

        K.identity = function(value) {
            return value;
        };

        // 生成唯一ID
        var idCounter = 0;
        K.uniqueId = function(prefix) {
            var id = idCounter++;
            return prefix ? prefix + id : id;
        };

})(K);

// touch事件检测
(function(K, undefined) {
    var result;

    var detect = function() {
        var events = ['touchstart', 'touchmove', 'touchend'],
        flag = true;

        var el = document.createElement('div');

        for (var i=0, j=events.length; i<j; i++) {
            var eventName = 'on' + events[i];
            var isSupported = (eventName in el);
            if (! isSupported) {
                el.setAttribute(eventName, 'return;');
                isSupported = typeof el[eventName] == 'function';
            }
            if (! isSupported) {
                flag = false;
                break;
            }
        }
        return flag;
    };

    K.isSupportTouch = function() {
        if (result === undefined) {
            result = detect();
        }
        return result;
    };
})(K);

// js的运行环境，浏览器以及版本信息。（Browser仅基于userAgent进行嗅探，存在不严谨的缺陷。）
K.Browser = (function() {
    var na = window.navigator,
    ua = na.userAgent.toLowerCase(),
    browserTester = /(msie|webkit|gecko|presto|opera|safari|firefox|chrome|maxthon)[ \/]([\d.]+)/ig,
    Browser = {
        platform: na.platform
    };
    ua.replace(browserTester, function(a, b, c) {
        var bLower = b.toLowerCase();
        Browser[bLower] = c;
    });
    if (Browser.opera) { //Opera9.8后版本号位置变化
        ua.replace(/opera.*version\/([\d.]+)/, function(a, b) {
            Browser.opera = b;
        });
    }
    if (Browser.msie) {
        Browser.ie = Browser.msie;
        var v = parseInt(Browser.msie, 10);
        Browser['ie' + v] = true;
    }

    if (Browser.safari && Browser.chrome){
        Browser.safari = false;
    }
    return Browser;
}());

if (K.Browser.ie) {
    try {
        document.execCommand("BackgroundImageCache", false, true);
    } catch (e) {}
}

//鉴于该脚本加载时间比较靠前，故将窗口是否Focus的判断放在此处，提高准确性
function onDetectFocus(){
    K.windowFocused = true;
}
function onDetectBlur(){
    K.windowFocused = false;
}


if (K.Browser.ie){
    document.attachEvent('onfocusin', onDetectFocus);
    document.attachEvent('onfocusout', onDetectBlur);
}
else{
    window.addEventListener('focus', onDetectFocus, false);
    window.addEventListener('blur', onDetectBlur, false);
}

// 内部使用的 Event
(function(K) {

    var Pubsub = function() {
        this.__callbacks__ = {};
    };

    K.mix(Pubsub.prototype, {
        on: function(name, callback, context) {
            if (context) {
                callback = K.bind(callback, context);
            }

            var arr = this.__callbacks__[name];
            if (arr && arr.fired === true){
                callback.apply(context, arr.args);
                return;
            }

            //如果是fireReady并且已经触发则立即执行回调

            this.__callbacks__[name] = this.__callbacks__[name] || [];
            this.__callbacks__[name].push(callback);

            return [ name, callback ];
        },

        un: function(handle) {
            var name = handle[0];
            var callback = handle[1];

            if (name in this.__callbacks__) {
                var callbacks = this.__callbacks__[name];
                K.forEach(callbacks, function(cb, i) {
                    if (callback == cb) {
                        callbacks[i] = null;
                    }
                });
            }
        },

        once: function(name, callback, context) {
            var me = this;
            var handle = this.on(name, function() {
                callback.apply(context, arguments);
                me.un(handle);
            });
        },

        fire: function(name/* args */) {
            if (name in this.__callbacks__) {
                var callbacks = this.__callbacks__[name];
                var args = Array.prototype.slice.call(arguments, 1);
                K.forEach(callbacks, function(callback, i) {
                    if (typeof(callback) != "function") {
                        return null;
                    }
                    return callback.apply(null, args);
                });
            }
        },
        /**
        * fireReady触发的事件的特点是，即使是事件已经触发，后续on注册的callback仍然会执行
        * 同时请注意，这类事件应该只触发一次，多次会导致逻辑混乱
        */
        fireReady: function(name/* args */){
            var callbacks = this.__callbacks__[name];
            if (!callbacks){
                callbacks = this.__callbacks__[name] = [];
            }
            var args = Array.prototype.slice.call(arguments, 1);
            K.forEach(callbacks, function(callback, i) {
                if (typeof(callback) != "function") {
                    return null;
                }
                return callback.apply(null, args);
            });
            callbacks.fired = true;
            callbacks.args = args;
        }
    });

    K.Pubsub = Pubsub;
    K.mix(K, (new Pubsub()));

})(K);

// Module
(function(K, Resource, global) {

    var definedModules = {};
    var loadingModules = {};

    function Module (id, dependency, factory) {
        if (K.indexOf(dependency, id) >= 0) {
            throw new Error('Module:' + id + ' could\'t depends on itself!');
        }

        this.id = id;
        this.dependency = dependency;
        this.factory = factory;
    };


    /**************************************
    * define(moduleId, dependency, factory)
    * @param {string} moduleId
    * @param {array} dependency
    * @param {function} factory
    *
    * TODO: make this function argments unique.
    *
    **************************************/
    function define (moduleId, dependency, factory) {
        if (arguments.length < 2) {
            throw new Error('module has invalid parameters');
        }

        if (! K.isString(moduleId)) {
            throw new Error('module must have a ID');
        }

        // define('xx', function() {});
        if (K.isFunction(dependency)) {
            factory = dependency;
            dependency = [];
        }
        // define('xxx', {});
        else if (!K.isArray(dependency)) {
            var def = dependency; // 不能省略这个赋值，因为dependency在后面被改写了。
            factory = function(){ return def; };
            dependency = [];
        }

        if (moduleId != 'core/jQuery')
            dependency = ['core/jQuery'].concat(dependency);

        if (definedModules[ moduleId ]) {
            throw new Error(moduleId + ' has defined');
            return;
        }

        var realDependency = K.unique(dependency),
        allModules = Module.getDependencyDeeply(realDependency);
        Module.load(allModules);
        Module.wait(allModules, function() {
            var mod = new Module(moduleId, dependency, factory);
            definedModules[ moduleId ] = mod;

            try {
                delete loadingModules[ moduleId ];
            } catch(e) {}

            K.fire('Module:' + moduleId + ':Ready');
        });
    };

    Module.getLoaded = function(id) {
        if (!id)
            return definedModules;
        var mod = definedModules[ id ];
        return mod;
    };

    Module.createRequire = function(depends, sourceId) {
        function require(id, async) {
            if (!async && K.indexOf(depends, id) == -1) {
                throw new Error(id + ' counld\'t be required in ' + sourceId + ', as you not declared to depend it ');
            }

            var mod = definedModules[ id ];

            if (!mod) {
                return null;
            }

            if (!mod.exports) {
                createExports(mod);
            }

            return mod.exports;
        }

        require.async = function(modules, callback, context) {
            modules = K.isString(modules) ? [ modules ] : modules;
            var realModules = K.unique(modules);
            var allModules = Module.getDependencyDeeply(realModules);
            allModules = K.unique(allModules.concat(modules));

            Module.load(allModules);
            Module.wait(allModules, function() {
                var mods = [];
                for (var i = 0, length = modules.length; i < length; i++) {
                    mods.push(require(modules[ i ], true));
                }

                callback.apply(context || global, mods);
            });

        };

        return require;
    };

    /**
    * 获得模块依赖列表
    * @param {array} 依赖模块ID列表
    * @return {array} 模块ID列表
    */
    Module.getDependencyDeeply = function(midList) {
        return Resource.getDependencyDeeply(midList);
    };

    /**
    * 加载模块
    * 只是简单的将script中添加到dom树中
    * 不检测文件是否加载完成
    */
    Module.load = function(moduleList) {

        moduleList = K.isString(moduleList) ? [ moduleList ] : moduleList;

        K.forEach(moduleList, function(mid) {
            if (definedModules[mid])
                return;
            Resource.loadModule(mid);
        });
    };

    /**
    * 检测所有已加载完成的模块
    */
    Module.defined = function() {
        return definedModules;
    };

    /**
    * 检测加载中模块
    */
    Module.waiting = function() {
        return K.keys(loadingModules);
    };

    /**
    * 检测模块是否加载完毕
    * 如果没有则等待
    */
    Module.check = function(mid, callback) {
        mid = K.trim(mid);
        if (mid.length == 0 || definedModules[ mid ]) {
            callback && callback();

        } else {
            // 记录正在加载中的模块
            loadingModules[ mid ] = true;
            K.on('Module:' + mid + ':Ready', callback);
        }
    };

    /**
    * 等待所有的依赖模块加载完毕
    * 串行检测所依赖模块
    * @param { array } dependency 依赖模块
    * @param { function } callback 回调函数
    */
    Module.wait = function(dependency, callback) {
        dependency = [].concat(dependency);
        if (dependency.length > 0) {
            var mod = dependency.shift();
            Module.check(mod, function() {
                Module.wait(dependency, callback);
            });
        } else {
            callback && callback();
        }
    };

    function createExports(mod) {
        var factory = mod.factory,
        ret;

        if (K.isFunction(factory)) {
            mod.exports = {};
            ret = factory(Module.createRequire(mod.dependency, mod.id), mod.exports, mod);
            if (ret) {
                mod.exports = ret;
            }
        }
        else if (factory !== undefined) {
            mod.exports = factory;
        }
    }

    // 暴露api
    global.define = K.define = define;
    global.Module = K.Module = Module;

})(K, K.Resource, this);


(function(K, Resource) {

    var jsInfo = {};
    var dependsMap = {};
    var _loaded = {};
    var _loading = {};
    var _callbacks = [];
    var _links = [];
    var _timer;
    var _resPrePath;

    Resource.setResPrePath = function(path) {
        _resPrePath = path;
    };

    Resource.getResPrePath = function() {
        if (_resPrePath== undefined)
            throw new Error('_resPrePath is not set. Call Resource.setResPrePath() first.');
        return _resPrePath;
    };

    /**
    * 添加文件版本信息
    * @param {object} info
    */
    Resource.addJsInfo = function(info) {
        K.mix(jsInfo, info);
    };


    /**
    * 获得文件版本信息
    */
    Resource.getVersionInfo = function(file) {
        if (file)
            return jsInfo[file];
        else
            return jsInfo;
    };


    /**
    * 添加静态文件依赖信息
    * @param {object} map
    */
    Resource.addResourceDepends = function(map) {
        K.mix(dependsMap, map);
    };

    /**
    * 获取依赖模块
    * @param {array|string} 依赖模块列表
    * @param {array} inlist 已经找出的依赖模块
    * todo 处理循环依赖
    */
    Resource.getDependencyDeeply = function(midList) {
        var unload = [].concat(K.isString(midList) ? [ midList ] : midList);
        var ret = [].concat(unload);
        if (unload.length == 0)
            return [];
        var loaded = [];
        while (mid = unload.shift()) {
            if (K.indexOf(loaded, mid) == -1) {
                if (mid == 'core/jQuery')
                    continue;
                if (!jsInfo[mid] ) {
                    throw new Error('Module:' + mid + ' could\'t find dependency info!');
                }
                var depends = jsInfo[ mid ]['d'];
                ret = ret.concat(depends);
                unload = unload.concat(depends);
                loaded.push(mid);
            }
        }
        K.unique(ret);
        return ret;
    };

    Resource.loadModule = function(mid) {
        var url = Resource.getFullPath(mid);
        if (_loaded[url] || _loading[url])
            return;
        Resource.loadJS(url);
    };

    Resource.getModuleName = function(js){
        js = js.replace(jsPre, '');
        js = js.replace(jsPost, '');
        return js;
    };

    Resource.getFullPath = function(js) {
        var mid = Resource.getModuleName(js);
        if (!jsInfo[mid]) {
            throw new Error('Can not find information for: ' + mid);
        }
        var url = jsInfo[mid]['url'];
        var ret = Resource.getResPrePath() + url;
        return ret;
    };

    Resource.canonicalURI = function(src) {
        if (/^\/\/\/?/.test(src)) {
            src = location.protocol + src;
        }
        return src;
    };

    var _initResourceMap = function() {
        var allTags = document.getElementsByTagName('link'),
        len = allTags.length,
        tag;
        while (len) {
            tag = allTags[--len];
            if ((tag.rel == 'stylesheet' || tag.type == 'text/css') && tag.href) {
                _loaded[Resource.canonicalURI(tag.href)] = true;
            }
        }

        allTags = document.getElementsByTagName('script');
        len = allTags.length;
        while (len) {
            tag = allTags[--len];
            if ((!tag.type || tag.type == "text/javascript") && tag.src) {
                _loaded[Resource.canonicalURI(tag.src)] = true;
            }
        }

        _initResourceMap = function() {};
    };

    var _complete = function(uri) {
        var list = _callbacks,
        item, i;

        delete _loading[uri];
        _loaded[uri] = true;

        for (i=0; i<list.length; i++) {
            item = list[i];
            delete item.resources[uri];
            if (K.isEmpty(item.resources)) {
                item.callback && item.callback();
                list.splice(i--, 1);
            }
        }
    };

    var _poll = function() {
        var sheets = document.styleSheets,
        i = sheets.length,
        links = _links;

        while (i--) {
            var link = sheets[i],
            ready = false;

            if (! K.Browser.firefox) {
                ready = true;
            } else {
                try {
                    if (link.cssRules) {
                        ready = true;
                    }
                } catch (ex) {
                    // cp from seajs
                    if (ex.name === 'SecurityError' || // firefox >= 13.0
                        ex.name === 'NS_ERROR_DOM_SECURITY_ERR') { // old firefox
                        ready = true;
                    }
                    /*
                    if (ex.code == 1000) {
                    ready = true;
                    }
                    */
                }
            }

            if (ready) {
                var owner = link.ownerNode || link.owningElement,
                j = links.length;

                if (owner) {
                    while (j--) {
                        if (owner == links[j]) {
                            _complete(links[j]['data-href']);
                            links.splice(j, 1);
                        }
                    }
                }
            }
        }

        if (! links.length) {
            clearInterval(_timer);
            _timer = null;
        }
    };

    var _injectJS = function(uri) {
        var script = document.createElement('script');
        var callback = function() {
            script.onload = script.onerror = script.onreadystatechange = null;
            _complete(uri);
        };

        K.mix(script, {
            type: 'text/javascript',
            src: uri,
            async: true
        });
        script.onload = script.onerror = callback;
        script.onreadystatechange = function() {
            var state = this.readyState;
            if (state == 'complete' || state == 'loaded') {
                callback();
            }
        };
        document.getElementsByTagName('head')[0].appendChild(script);
    };

    var _injectCSS = function(uri) {
        var link = document.createElement('link');
        K.mix(link, {
            type: 'text/css',
            rel: 'stylesheet',
            href: uri,
            'data-href': uri
        });
        document.getElementsByTagName('head')[0].appendChild(link);

        if (link.attachEvent) {
            var callback = function() {
                _complete(uri);
            };
            link.onload = callback;
        } else {
            _links.push(link);
            if (! _timer) {
                _timer = setInterval(_poll, 20);
            }
        }
    };

    var _load = function(list, callback) {
        var resources = {},
        uri, path, type, ret;

        _initResourceMap();

        list = K.isArray(list) ? list : [ list ];
        for (var i=0, j=list.length; i < j; i++) {
            uri = Resource.canonicalURI(list[i]);
            resources[uri] = true;

            if (_loaded[uri]) {
                setTimeout(K.bind(_complete, null, uri), 0);
            } else if (! _loading[uri]) {
                _loading[uri] = true;
                if (uri.indexOf('.css') > -1) {
                    _injectCSS(uri);
                } else {
                    _injectJS(uri);
                }
            }
        }

        if (callback) {
            _callbacks.push({
                resources: resources,
                callback: callback
            });
        }
    };

    /**
    * 加载JS文件
    * @param {mixed} src JS文件绝对地址
    * @param {function} callback js加载完成后回调函数
    **/
    Resource.loadJS = function(src, callback) {
        _load(src, callback);
    };

    /**
    * 加载CSS文件
    * @param {mixed} uri css文件绝对地址
    * @param {function} callback todo: 文件加载完成后回调函数
    */
    Resource.loadCSS = function(uri, callback) {
        _load(uri, callback);
    };

    /**
    * 加载image文件
    * @param {string} uri image文件绝对地址
    * @param {function} callback todo: 文件加载完成后回调函数
    */
    Resource.loadIMG = function(src, callback) {
        var image = new Image();

        callback = typeof callback === 'function' ? callback : function() {};
        image.onload = function() {
            image.onload = null;
            callback.call(null, image);
        };
        if (image.readyState == "complete") {
            callback.call(null, image);
        }
        image.src = src;
    };

    // js/ |/js/
    var jsPre = /^\/?js\//gi;

    // -abced.js | .js
    var jsPost = /(?:-[A-Za-z0-9]+)?.js?$/gi;

})(K, K.Resource);


// Dom Ready
(function(K) {

    var isDomContentLoaded = false;
    var isWindowLoaded = false;

    K.ready = function(callback) {
        if (isDomContentLoaded) {
            callback();
        } else {
            K.on('Onload:DomContentLoaded', callback);
        }
    };

    K.load = function(callback) {
        if (isWindowLoaded) {
            callback();
        } else {
            K.on('Onload:Loaded', callback);
        }
    };

    var onDomContentLoaded = function() {
        if (! isDomContentLoaded) {
            isDomContentLoaded = true;
            K.fire("Onload:DomContentLoaded");
        }
    };

    var onWindowLoaded = function() {
        if (! isWindowLoaded) {
            isWindowLoaded = true;
            K.fire("Onload:Loaded");
        }
    };

    var DOMContentLoaded;
    if (document.addEventListener) {
        DOMContentLoaded = function() {
            document.removeEventListener("DOMContentLoaded", DOMContentLoaded, false);
            onDomContentLoaded();
        };
    } else if (document.attachEvent) {
        DOMContentLoaded = function() {
            if (document.readyState === "complete") {
                document.detachEvent("onreadystatechange", DOMContentLoaded);
                onDomContentLoaded();
            }
        };
    }

    var doScrollCheck = function() {
        if (isDomContentLoaded) {
            return;
        }

        try {
            // If IE is used, use the trick by Diego Perini
            // http://javascript.nwbox.com/IEContentLoaded/
            document.documentElement.doScroll("left");
        } catch(e) {
            setTimeout(doScrollCheck, 1);
            return;
        }

        // and execute any waiting functions
        document.detachEvent("onreadystatechange", DOMContentLoaded);
        onDomContentLoaded();
    };

    var bootstrapHandler = function() {

        var window = K.global,
        document = window.document,
        onloadHandler = function() {
            if (! isDomContentLoaded) {
                isDomContentLoaded = true;
                onDomContentLoaded();
            }

            onWindowLoaded();
        };

        if (document.readyState == "complete") {
            setTimeout(onDomContentLoaded, 0);
            return;
        }

        if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', DOMContentLoaded, false);
            window.addEventListener('load', onloadHandler, false);
        } else if (document.attachEvent) {
            document.attachEvent("onreadystatechange", DOMContentLoaded);
            window.attachEvent("onload", onloadHandler);

            // If IE and not a frame
            // continually check to see if the document is ready
            var toplevel = false;

            try {
                toplevel = window.frameElement == null;
            } catch(e) {}

            if (document.documentElement.doScroll && toplevel) {
                doScrollCheck();
            }
        }
    };

    bootstrapHandler();

})(K);


// APP
(function(K, Module) {

    var reserveMethods = 'require getContainer'.split(/\s+/ig);  // 一些保留属性或者方法
    var appList = {};

    function Application(id, dependency, execBeforeDomready) {
        if (appList[ id ]) {
            throw new Error('App:' + id + ' has defined');
            return;
        }
        appList[ id ] = this;
        this.id = id;

        // App默认加载jQuery
        this.requiredModList = [ 'core/jQuery' ].concat(dependency);
        this.execBeforeDomready = execBeforeDomready;

        this.destroy_fns = [];
        this.destroyed = false;
    }

    K.mix(Application.prototype, {

        destroy: function() {
            this.destroy_fns.forEach(function(fn) {
                fn.apply();
            });
            this.destroyed = true;
            delete appList[this.id];
        },

        onDestroy: function(fn) {
            this.destroy_fns.push(fn);
        },

        define: function(definition) {

            var dependency = K.unique(this.requiredModList),
            allModules = Module.getDependencyDeeply(dependency),
            me = this,
            ready = function() {
                K.fire('App:' + me.id + ':Ready', me);
            };

            Module.load(allModules);

            // 等待加载完成
            Module.wait(allModules, function() {

                // factory 方法
                // 如果factory里覆盖了require方法，暂时无法检测
                var require = Module.createRequire(dependency, me.id);
                if (K.isFunction(definition)) {
                    definition = definition.call({}, require);
                    definition = K.mix(definition, me);
                };

                // App的factory可能没有return definition
                if (!definition) {
                    return;
                }

                // 检测definition中是否定义了保留方法
                var i, length, prop;
                for (i = 0, length = reserveMethods.length; i < length; i++) {
                    prop = reserveMethods[ i ];
                    if (definition[ prop ]) {
                        throw new Error(prop + ' is a reserve method or property');
                    }
                }

                definition.require = require;

                // jQuery默认加载
                var $ = definition.require('core/jQuery');
                me.__jQuery__ = $;

                // 如果App中设定了container，但是DOM树中找不到
                // 则不给App绑定事件或者执行main方法
                if (definition.container && $(definition.container).length == 0) {
                    return;
                }

                // 设定Container;
                var container = processContainer(definition, $);

                // 绑定事件
                if (definition.events) {
                    bindEvents(definition, container);
                }

                // 入口初始化
                if (K.isFunction(definition.main)) {
                    if (me.execBeforeDomready) {
                        definition.main();
                        ready();
                    }
                    // Dom Ready 后再执行
                    else {
                        K.ready(function() {
                            definition.main();
                            ready();
                        });
                    }
                } else {
                    ready();
                }
            });
            return this;
        }
    });

    function processContainer(definition, $) {
        var container;
        if (!definition.container) {
            definition.container = document.body; // 如果没有指定container，则默认为document.body
        }

        container = $(definition.container);

        if (!container.length || container.length > 1) {
            container = $(document.body);
        }

        // 修改container
        definition.container = container;
        definition.getContainer = function() {
            return container;
        };

        return container;
    }

    // 给APP绑定事件
    var eventSpliter = /^([\w\.]+)(?:\s+(.*))?$/;
    function bindEvents(app, container) {
        var events = app.events;

        K.map(events, function(handler, evtStr) {
            var match = evtStr.match(eventSpliter),
            evtName = match[1],
            selector = match[2];
            method = K.bind(app[handler], app);

            var _handler = function() {
                app[handler].apply(app, arguments);
            };

            if (!selector) {
                container.bind(evtName, _handler);
                app.onDestroy(function() {
                    container.unbind(evtName, _handler);
                });
            } else {
                container.delegate(selector, evtName, _handler);
                app.onDestroy(function() {
                    container.undelegate(selector, evtName, _handler);
                });
            }
        });
    }

    // 生成不重复的app id
    var appId = 0;
    function genAppId() {
        return '__APP__' + (appId++);
    }

    function getId(info) {
        var id = info[ 0 ];
        if (id && K.isString(id)) {
            // App id 必须以A开头
            if (K.last(id.split(/\//ig)).charAt(0) != 'A') {
                throw new Error(id + ' is a invalid name for a App');
            }
        } else {
            id = genAppId();
        }

        return id;
    }

    function getDepends(info) {
        return K.detect(info, function(item) {
            return K.isArray(item);
        }) || [];
    }

    function unsafeExec(info) {
        var execBeforeDomready = K.last(info);
        return K.isBoolean(execBeforeDomready) ? execBeforeDomready : false;
    }

    /**
    * 定义APP
    * @param {string|optinal} id  App id
    * @param {array|optional} depends 依赖模块
    */
    K.App = function(id, depends, execBeforeDomready) {
        return new Application(getId(arguments), getDepends(arguments), unsafeExec(arguments));
    };

    K.App.get = function(appID) {
        return appList[ appID ] || appList;
    };

})(K, K.Module);

// 自定义事件
(function(K) {
    var mix = K.mix,
    indexOf = K.indexOf;

    //----------K.CustEvent----------
    /**
    * @class CustEvent 自定义事件
    * @namespace K
    * @param {object} target 事件所属对象，即：是哪个对象的事件。
    * @param {string} type 事件类型。备用。
    * @param {object} eventArgs (Optional) 自定义事件参数
    * @returns {CustEvent} 自定义事件
    */
    var CustEvent = K.CustEvent = function(target, type, eventArgs) {
        this.target = target;
        this.type = type;
        mix(this, eventArgs || {});
    };

    mix(CustEvent.prototype, {
        /**
        * @property {Object} target CustEvent的target
        */
        target: null,
        /**
        * @property {Object} currentTarget CustEvent的currentTarget，即事件派发者
        */
        currentTarget: null,
        /**
        * @property {String} type CustEvent的类型
        */
        type: null,
        /**
        * @property {boolean} returnValue fire方法执行后的遗留产物。(建议规则:对于onbeforexxxx事件，如果returnValue===false，则不执行该事件)。
        */
        returnValue: undefined,
        /**
        * 设置event的返回值为false。
        * @method preventDefault
        * @returns {void} 无返回值
        */
        preventDefault: function() {
            this.returnValue = false;
        }
    });
    /**
    * 为一个对象添加一系列事件，并添加on/un/fire三个方法，参见：K.CustEventTarget.createEvents
    * @static
    * @method createEvents
    * @param {Object} obj 事件所属对象，即：是哪个对象的事件。
    * @param {String|Array} types 事件名称。
    * @returns {void} 无返回值
    */

    /**
    * @class CustEventTargetH  自定义事件Target
    * @namespace K
    */

    K.CustEventTargetH = {
        /**
        * 添加监控
        * @method on
        * @param {string} sEvent 事件名称。
        * @param {Function} fn 监控函数，在CustEvent fire时，this将会指向oScope，而第一个参数，将会是一个CustEvent对象。
        * @return {boolean} 是否成功添加监控。例如：重复添加监控，会导致返回false.
        * @throw {Error} 如果没有对事件进行初始化，则会抛错
        */
        on: function(target, sEvent, fn) {
            var cbs = (target.__custListeners && target.__custListeners[sEvent]) || K.error("unknown event type", TypeError);
            if (indexOf(cbs, fn) > -1) {
                return false;
            }
            cbs.push(fn);
            return true;
        },
        /**
        * 添加监控(只发生一次)
        * @method once
        * @param {string} sEvent 事件名称。
        * @param {Function} fn 监控函数，在CustEvent fire时，this将会指向oScope，而第一个参数，将会是一个CustEvent对象。
        * @return {boolean} 是否成功添加监控。例如：重复添加监控，会导致返回false.
        * @throw {Error} 如果没有对事件进行初始化，则会抛错
        */
        once: function(target, sEvent, fn) {
            var cbs = (target.__custListeners && target.__custListeners[sEvent]) || K.error("unknown event type", TypeError);
            var handler = $.proxy(function(custEvent) {
                fn.call(target, custEvent);
                K.CustEventTargetH.un(target, sEvent, handler);
            }, this);

            K.CustEventTargetH.on(target, sEvent, handler);
            return true;
        },
        /**
        * 取消监控
        * @method un
        * @param {string} sEvent 事件名称。
        * @param {Function} fn 监控函数
        * @return {boolean} 是否有效执行un.
        * @throw {Error} 如果没有对事件进行初始化，则会抛错
        */
        un: function(target, sEvent, fn) {
            var cbs = (target.__custListeners && target.__custListeners[sEvent]) || K.error("unknown event type", TypeError);
            if (fn) {
                var idx = indexOf(cbs, fn);
                if (idx < 0) {
                    return false;
                }
                cbs.splice(idx, 1);
            } else {
                cbs.length = 0;
            }
            return true;

        },
        /**
        * 事件触发。触发事件时，在监控函数里，this将会指向oScope，而第一个参数，将会是一个CustEvent对象，与Dom3的listener的参数类似。<br/>
        如果this.target['on'+this.type],则也会执行该方法,与HTMLElement的独占模式的事件(如el.onclick=function(){alert(1)})类似.<br/>
        如果createEvents的事件类型中包含"*"，则所有事件最终也会落到on("*").
        * @method fire
        * @param {string | sEvent} sEvent 自定义事件，或事件名称。 如果是事件名称，相当于传new CustEvent(this,sEvent,eventArgs).
        * @param {object} eventArgs (Optional) 自定义事件参数
        * @return {boolean} 以下两种情况返回false，其它情况下返回true.
        1. 所有callback(包括独占模式的onxxx)执行完后，custEvent.returnValue===false
        2. 所有callback(包括独占模式的onxxx)执行完后，custEvent.returnValue===undefined，并且独占模式的onxxx()的返回值为false.
        */
        fire: function(target, sEvent, eventArgs) {
            if (sEvent instanceof CustEvent) {
                var custEvent = mix(sEvent, eventArgs);
                sEvent = sEvent.type;
            } else {
                custEvent = new CustEvent(target, sEvent, eventArgs);
            }

            var cbs = (target.__custListeners && target.__custListeners[sEvent]) || K.error("unknown event type", TypeError);
            if (sEvent != "*") {
                cbs = cbs.concat(target.__custListeners["*"] || []);
            }

            custEvent.returnValue = undefined; //去掉本句，会导致静态CustEvent的returnValue向后污染
            custEvent.currentTarget = target;
            var obj = custEvent.currentTarget;
            if (obj && obj['on' + custEvent.type]) {
                var retDef = obj['on' + custEvent.type].call(obj, custEvent); //对于独占模式的返回值，会弱影响event.returnValue
            }

            for (var i = 0; i < cbs.length; i++) {
                cbs[i].call(obj, custEvent);
            }
            return custEvent.returnValue !== false || (retDef === false && custEvent.returnValue === undefined);
        },
        createEvents: function(target, types) {
            /**
            * 为一个对象添加一系列事件，并添加on/un/fire三个方法<br/>
            * 添加的事件中自动包含一个特殊的事件类型"*"，这个事件类型没有独占模式，所有事件均会落到on("*")事件对应的处理函数中
            * @static
            * @method createEvents
            * @param {Object} obj 事件所属对象，即：是哪个对象的事件。
            * @param {String|Array} types 事件名称。
            * @returns {any} target
            */
            types = types || [];
            if (typeof types == "string") {
                types = types.split(",");
            }
            var listeners = target.__custListeners;
            if (!listeners) {
                listeners = target.__custListeners = {};
            }
            for (var i = 0; i < types.length; i++) {
                listeners[types[i]] = listeners[types[i]] || []; //可以重复create，而不影响之前的listerners.
            }
            listeners['*'] = listeners["*"] || [];
            return target;
        }
    };

}(K));

(function(K) {

    var Methodized = function() {};

    var Helper = {
        /**
        * 对helper的方法，进行methodize化，使其的第一个参数为this，或this[attr]。
        * <strong>methodize方法会抛弃掉helper上的非function类成员以及命名以下划线开头的成员（私有成员）</strong>
        * @method methodize
        * @static
        * @param {Helper} helper Helper对象，如DateH
        * @param {optional} attr (Optional)属性
        * @return {Object} 方法已methodize化的对象
        */
        methodize: function(helper, attr) {
            var ret = new Methodized(); //因为 methodize 之后gsetter和rwrap的行为不一样

            for (var i in helper) {
                if (typeof helper[i] == "function" && !/^_/.test(i)) {
                    ret[i] = K.methodize(helper[i], attr);
                }
            }
            return ret;
        }
    };

    K.Helper = Helper;
}(K));

(function(K) {
    var mix = K.mix;

    var CustEventTarget = K.CustEventTarget = function() {
        this.__custListeners = {};
    };

    var methodized = K.Helper.methodize(K.CustEventTargetH, null, {
        on: 'operator',
        un: 'operator'
    }); //将Helper方法变成prototype方法，同时修改on/un的返回值

    mix(CustEventTarget.prototype, methodized);

    K.CustEvent.createEvents = CustEventTarget.createEvents = function(target, types) {
        K.CustEventTargetH.createEvents(target, types);
        return mix(target, CustEventTarget.prototype); //尊重对象本身的on。
    };
}(K));

//由于onbeforeunload事件在浏览器中只能独占，因此在添加时很容易相互覆盖，以下方法可以保留已有的方法
(function(K) {
    K.onbeforeunload = function(callback) {
        if (!callback) return;
        if (typeof window.onbeforeunload == 'function') {
            var oldBeforeUnload = window.onbeforeunload;
            window.onbeforeunload = function() {
                //Chrome必须有返回值才会出提示，其他浏览器可以设置event参数的returnValue属性
                var v = callback.apply(null, arguments);
                var ov = oldBeforeUnload.apply(null, arguments);

                //确保所有都执行，优先返回原回调值，防止覆盖
                return ov ? ov : v;
            };
        } else {
            window.onbeforeunload = callback;
        }
    };
})(K);

// 性能数据
(function(K) {
    K.Performance = { timing: {} };
    var performance = window.performance || window.msPerformance || window.webkitPerformance || window.mozPerformance;
    if (performance && performance.timing) {
        K.Performance.timing = performance.timing;
    } else {
        K.Performance.timing.responseStart = K.global.pageStart;
        K.ready(function() {
            K.Performance.timing.domContentLoadedEventStart = (new Date()).getTime();
        });
        K.load(function() {
            K.Performance.timing.loadEventStart = (new Date()).getTime();
        });
    }
})(K);

// Default Event Handler
(function(K) {

    var getParentByTag = function(node, tagName) {
        tagName = tagName.toUpperCase();
        while (node && node.nodeName != tagName) {
            node = node.parentNode;
        }
        return node;
    };

    var getAttribute = function(element, attr) {
        if ('hasAttribute' in element) {
            return element.getAttribute(attr);
        } else if ('getAttributeNode' in element) {
            var node = element.getAttributeNode(attr);
            if (node) {
                return node.value;
            }
        }
        return undefined;
    };

    var isAppReady = function(app) {
        if (! ('App' in K) || ! K.App.get(app)) {
            return false;
        }
        return true;
    };

    var getNotReadyApp = function(element) {
        var app = getAttribute(element, 'data-app');
        if (app && app.length && !isAppReady(app)) {
            return app;
        }
        return false;
    };

    var delegateEvent = function(container, eventName, target) {
        var app = getNotReadyApp(container),
        ajax, href;
        if (app) {
            K.once('App:' + app + ':Ready', function(app) {
                app.__jQuery__(target || container)[eventName]();
            });
            return false;
        } else if (eventName == "click") {
            ajax = container.getAttribute('data-ajax');
            if (ajax && ajax.length) {
                href = container.getAttribute('data-ajax-url') || container.href;
                switch (ajax) {
                    case 'dialog-post':
                        case 'dialog':
                        K.Module.createRequire().async('core/dialog/AsyncDialog', function(Dialog) {
                        Dialog.open(href, container);
                    });
                    break;
                    case 'request-post':
                        case 'request':
                        K.Module.createRequire().async('core/ajax/Request', function(Request) {
                        Request.load(href, container);
                    });
                    break;
                    default:
                        return;
                }
                return false;
            }
        }
        return;
    };

    var delegateSubmit = function(target) {
        var form = getParentByTag(target, 'FORM') || target;
        return delegateEvent(form, 'submit');
    };

    var delegateClick = function(target) {
        var container = getParentByTag(target, 'A') || target;
        return delegateEvent(container, 'click', target);
    };

    var supportSubmitBubble = (function() {
        var el = document.createElement("div");
        var support = ('onsubmit' in el);
        if (! support) {
            el.setAttribute('onsubmit', "return;");
            support = typeof el.onsubmit === 'function';
        }
        return support;
    })();

    var defaultEventHandler = function() {
        var document = K.global.document,
        documentElement = document.documentElement;

        var target = null;
        documentElement.onclick = function(event) {
            event = event || window.event;
            target = event.target || event.srcElement;

            if (! supportSubmitBubble) {
                var type = target.type;
                if (type == 'submit' || type == 'image') {
                    return delegateSubmit(target);
                }
            }

            var href = target.href;
            if (href && !/#$/.test(href)) {
                var isNotClick = (event.which && event.which != 1),
                isSpecialChar = (event.altKey || event.ctrlKey || event.metaKey || event.shiftKey);

                if (isNotClick || isSpecialChar) {
                    return;
                }
            }

            return delegateClick(target);
        };

        if (! supportSubmitBubble) {
            documentElement.onkeypress = function(event) {
                event = event || window.event;
                target = event.target || event.srcElement;

                var type = target.type;
                if ((type == 'text' || type == 'password') && event.keyCode == 13) {
                    return delegateSubmit(target);
                }
                return;
            };
        }

        if (supportSubmitBubble) {
            documentElement.onsubmit = function(event) {
                event = event || window.event;
                var form = event.target || event.srcElement;

                return delegateSubmit(form);
            };
        }
    };

    defaultEventHandler();

})(K);

/**
*
*/
(function() {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
})();

/**
* data
*/
(function(K) {
    var dataList = {};
    var baseInfo = {};

    K.data = {

        set: function() {
            if (arguments.length == 2)
                dataList[arguments[0]] = argments[1];
            else
                dataList = arguments[0];
        },

        get: function(key) {
            if (key)
                return dataList[key];
            return dataList;
        },

        setBaseInfo: function() {
            if (arguments.length == 2)
                baseInfo[arguments[0]] = argments[1];
            else
                baseInfo = arguments[0];
        },

        getBaseInfo: function(key) {
            if (key)
                return baseInfo[key];
            return baseInfo;
        }
    };
}(K));

/**
* img
*/
(function(K) {
    K.img = {
        getImageWrapper: function(target) {
            var wrapper = target.parentNode;
            var time = 0;
            while (wrapper && wrapper.nodeName == "A" && time < 10) {
                wrapper = wrapper.parentNode;
                time++;
            }
            return wrapper;
        },

        resize: function(img, width, height, getFullPic) {
            var w = img.width;
            var h = img.height;

            var wrapper = this.getImageWrapper(img);

            if (!wrapper) return;

            if (!getFullPic) {

                // 按较小边显示，多余切除
                wrapper.style["overflow"] = "hidden";
                wrapper.style["width"] = width + "px";
                wrapper.style["height"] = height + "px";

                var p1 = w / width;
                var p2 = h / height;

                if (p1 >= 1 && p2 >= 1) {
                    if (p1 > p2) {
                        w = w / p2;
                        h = height;
                    }
                    else {
                        w = width;
                        h = h / p1;
                    }
                }

                img.width = w;
                img.height = h;

                img.style["marginTop"] = (height - h) / 2 + "px";
                img.style["marginLeft"] = (width - w) / 2 + "px";

            } else {

                //获得整张图片的显示，先获取较大的边的缩放比例，然后居中
                var scallValue = ((h / height) > (w / width)) ? (h /height) : (w/width);

                img.width = w / scallValue;
                img.height = h / scallValue;

                img.style["marginTop"] = -(img.height - height) / 2 + "px";
                img.style["marginLeft"] = -(img.width - width) / 2 + "px";

            }

            img = $(img);
            wrapper = $(wrapper);

            wrapper.css('opacity', 0);
            if (wrapper.css('opacity') == 0 && img.css('opacity') == 0) {
                img.css('opacity', 1);
                wrapper.animate({"opacity": 1}, "slow");
            } else {
                if (img.css('opacity') == 0) {
                    img.animate({"opacity": 1}, "slow");
                }
                if (wrapper.css('opacity') == 0) {
                    wrapper.animate({"opacity": 1}, "slow");
                }
            }
        },
    };

})(K);

K.define('core/jQuery',[], function() {
    return jQuery;
});

/**
* 以下是专门为简化开发添加的插件，不属于框架（添加的插件将释放到全局）
*
* doT      --- 模板解析
* Cookie   --- cookie操作
*/
(function() {

    /**
    * doT
    */
    var doT = { version : '0.1.3' };
    doT.templateSettings = {
        evaluate : /\{\{([\s\S]+?)\}\}/g,
        interpolate : /\{\{=([\s\S]+?)\}\}/g,
        encode :  /\{\{!([\s\S]+?)\}\}/g,
        defines:  /\{\{#([\s\S]+?)\}\}/g,
        varname : 'it',
        strip : true
    };
    doT.template = function(tmpl, c, defs) {
        c = c || doT.templateSettings;
        var str = ("var out='" +
                   ((c.strip) ? tmpl.replace(/\s*<!\[CDATA\[\s*|\s*\]\]>\s*|[\r\n\t]|(\/\*[\s\S]*?\*\/)/g, ''):
                    tmpl)
        .replace(c.defines, function(match, code) {
            return eval(code.replace(/[\r\t\n]/g, ' '));
        })
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(c.interpolate, function(match, code) {
            return "';out+=" + code.replace(/\\'/g, "'").replace(/\\\\/g,"\\").replace(/[\r\t\n]/g, ' ') + ";out+='";
        })
        .replace(c.encode, function(match, code) {
            return "';out+=(" + code.replace(/\\'/g, "'").replace(/\\\\/g, "\\").replace(/[\r\t\n]/g, ' ') + ").toString().replace(/&(?!\\w+;)/g, '&#38;').split('<').join('&#60;').split('>').join('&#62;').split('" + '"' + "').join('&#34;').split(" + '"' + "'" + '"' + ").join('&#39;').split('/').join('&#x2F;');out+='";
        })
        .replace(c.evaluate, function(match, code) {
            return "';" + code.replace(/\\'/g, "'").replace(/\\\\/g,"\\").replace(/[\r\t\n]/g, ' ') + "out+='";
        })
        + "';return out;")
        .replace(/\n/g, '\\n')
        .replace(/\t/g, '\\t')
        .replace(/\r/g, '\\r')
        .split("out+='';").join('')
        .split('var out="";out+=').join('var out=');

        try {
            return new Function(c.varname, str);
        } catch (e) {
            if (typeof console !== 'undefined') console.log("Could not create a template function: " + str);
            throw e;
        }
    };

    /**
    * Cookie
    */
    function Cookie(){
        return this.init.apply(this,arguments);
    }
    Cookie.prototype = (function(){
        return{
            /**
            * 初始化
            *
            * @method init
            * @public
            * @param {options}  配置
            * @return void
            */
            init: function(opt){
                opt = opt || {};
                this.path    = opt.path || "/";
                this.domain  = opt.domain || "";
                this.expires = opt.expires || 1000 * 60 * 60 * 24 * 365;
                this.secure  = opt.secure || "";
            },
            /**
            * 存储
            * @method set
            * @public
            * @param {string} key
            * @param {string} value
            * @return void
            */
            set: function(key, value){
                var now = new Date();
                if (typeof(this.expires)=="number"){
                    now.setTime(now.getTime() + this.expires);
                }
                document.cookie =
                    key + "="+ escape(value)
                + ";expires=" + now.toGMTString()
                + ";path="+ this.path
                + (this.domain == "" ? "" : ("; domain=" + this.domain))
                + (this.secure ? "; secure" : "");
            },
            /**
            * 读取
            * @method get
            * @public
            * @param {string} key
            * @return string
            */
            get: function(key){
                var a, reg = new RegExp("(^|)" + key + "=([^;]*)(;|$)");
                if (a = document.cookie.match(reg)){
                    return unescape(a[2]);
                }else{
                    return "";
                }
            },
            /**
            * 移除
            * @method remove
            * @public
            * @param {string} key
            * @return void
            */
            remove: function(key){
                var old=this.expires;
                this.expires = -1 * 1000 * 60 * 60 * 24 * 365;
                this.set(key,"");
                this.expires=old;
            }
        };
    })();


    /**
    * 存储
    * @method set
    * @static
    * @param {string} key
    * @param {string} value
    * @param {object} option
    * @return void
    */
    Cookie.set=function(key,value,option){
        var cookie = new Cookie(option);
        cookie.set(key,value);
    };

    /**
    * 读取
    * @method get
    * @static
    * @param {string} key
    * @param {object} option
    * @return string
    */
    Cookie.get=function(key,option){
        var cookie = new Cookie(option);
        var ret = cookie.get(key);
        return ret;
    };

    /**
    * 移除
    * @method set
    * @static
    * @param {string} key
    * @param {object} option
    * @return void
    */
    Cookie.remove=function(key,option){
        var cookie = new Cookie(option);
        cookie.remove(key);
    };

    // 释放到全局
    window.doT = doT;
    window.Cookie = Cookie;
}());

