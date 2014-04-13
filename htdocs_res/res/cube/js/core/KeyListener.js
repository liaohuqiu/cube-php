define('core/KeyListener', ['core/jQuery'], function(require, exports) {
    
    var $ = require('core/jQuery'),
    
        keyMap = { // 常用按键keycode对照
            'backspace':8,
            'tab':9,
            'enter':13,
            'shift':16,
            'ctrl':17,
            'alt':18,
            'capslock':20,
            'esc':27,
            'space':32,
            'pageup':33,
            'pagedown':34,
            'end':35,
            'home':36,
            'left':37,
            'up':38,
            'right':39,
            'down':40 
        };
    
    /**
     * listen key for target
     * @param target key listen target
     * @param type key press type(only keydown keypress) - keypress在无有效输入时会不被触发，所以不能支持
     * @param key which key to listen(enter 13 crtl+enter ctrl+13 ctrl,shift+enter)-加号后面只能为单一key
     * 
     * 该方法监听与指定key事件及keycode相匹配的动作，回调输入的方法
     */
    exports.listen = function(target, type, key, callback, context) {
        
        var data, // callback返回数据
            context = context || window,
            keyPart = parseKey(key); // 分析用户的key输入
            
        if((keyPart.holdKey || keyPart.keyCode) && 
                (type === 'keydown' || type === 'keyup') &&
                (typeof callback === 'function')) { // 只对有效输入绑定事件
            $(target).bind(type, function(ev) {
                if(matchHoldKey(ev, keyPart.holdKey)) { // hold key match
                    if(matchKeyCode(ev, keyPart.holdKey, keyPart.keyCode)) { // key code match
                        callback.apply(context, arguments);
                    }
                }
            });
        }
    };
    
    /**
     * 将用户输入的key解析成hold key与key code两部分
     * @param key which key to listen(enter 13 crtl+enter ctrl+13 ctrl,shift+enter)-加号后面只能为单一key
     * 另外，ctrlKey将自动兼容metaKey
     */
    function parseKey(key) {
        var keyPart = {}, tempPart, holdPart, keyCodePart;
        
        if(K.isString(key)) {
            
            tempPart = key.split('+');
            
            // 只对有效输入（有一个或没有+号）进行分析
            if(tempPart.length === 1) {
                keyCodePart = K.trim(tempPart[0]);
            } else if(tempPart.length === 2) {
                holdPart = K.trim(tempPart[0]);
                keyCodePart = K.trim(tempPart[1]);
            }
            
            if(holdPart) {
                keyPart.holdKey = [];
                tempPart = holdPart.split(',');
                K.forEach(tempPart, function(key, index) {
                    key = K.trim(key);
                    if(K.indexOf(['ctrl', 'alt', 'shift'], key) !== -1) {
                        keyPart.holdKey = keyPart.holdKey.concat(key);
                    }
                });
                keyPart.holdKey = K.unique(keyPart.holdKey);
                if(keyPart.holdKey.length === 0) {
                    delete keyPart.holdKey;
                }
            }
            
            if(keyCodePart) {
                keyPart.keyCode = isNaN(keyCodePart) ? keyMap[keyCodePart] : parseInt(keyCodePart, 10);
            }
            
        }
        return keyPart;
    }
    
    /**
     * 检测hold key是否相符
     */
    function matchHoldKey(ev, holdKey) {
        var matchHold = true, i;
        
        if(K.isArray(holdKey)) {
            for(i=0; i<holdKey.length; i++) {
            	var keyCondition = holdKey[i] == 'ctrl' ? (ev.ctrlKey || ev.metaKey) : ev[holdKey[i]+'Key'];
                if(!keyCondition) {
                    matchHold = false;
                    break;
                }
            }
        }
        
        return matchHold;
    }
    
    
    /**
     * 检测按键keycode是否相符
     */
    function matchKeyCode(ev, holdKey, keyCode) {
        var matchKey = true, i, holdKeyCode;
        
        if(K.isArray(holdKey)) {
            for(i=0; i<holdKey.length; i++) {
                if(keyMap[holdKey[i]] === ev.keyCode) {
                    matchKey = false;
                    break;
                }
            }
            matchKey = matchKey && (!keyCode || (keyCode && ev.keyCode === keyCode));
        } else {
            matchKey = keyCode && ev.keyCode === keyCode;
        }
        
        return matchKey;
    }

});
