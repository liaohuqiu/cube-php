/**
    Copyright (c) 2011, kaixin001 Inc. All rights reserved.
    http://www.kaixin001.com
    @fileoverview: 长短轮询类，封装了本地分享功能，即只保持一个页面发送请求。(目前只支持localStorage，如果浏览器不支持则失效)
    @author: minliang@corp.kaixin001.com
    @date:2011/7/8
*/
define( 'core/Poller', ['core/Storage', 'core/jQuery'], function(require){
    var $ = require('core/jQuery'),
        CustEvent = K.CustEvent,
        insCounter = 0,
        Storage = require('core/Storage');
    /**
     * 轮询类
     * @class
     */
    function Poller( options ){
        /**
            @property {int} interval 轮询间隔时常，也作为storage中数据有效期;
         */
        //对于常轮询而言该值会被重置为0
        this.interval = 5000;


        /**
            @property {int} liveAge 一个请求处于live状态的时长；
            如果是短连接可以保持和interval一致，但是长轮询则需要设置一个相对较长的时间
            至少比轮询的时间要长
         */
        this.liveAge = 5000;
        /**
            @property {string} type 轮询间隔，也作为storage中数据有效期时长;
            轮询类型
            long: 长轮询
            short: 短轮询
            长轮询必须一直保持一个指向服务器的请求
            短轮询只需要在一定的时间段内有请求发出即可
         */
        this.type = 'short';

        /**
            @property {string} url 请求URL
         */
        this.url = null;

        /**
            @property {string} requestType 请求类型(GET or POST)
         */
        this.requestType = 'GET';

        /**
            @property {Object} requestData 请求数据
         */
        this.requestData = {};

        /**
            @property {int} timeout 超时时间
         */
        this.timeout = 60000;

        /**
            @property {boolean} useLocalShare 是否使用本地同步，使用本地同步会使得在开启多页面情况下只有一个页面发送请求
         */
        this.useLocalShare = false;

        /**
            @property {function} callback 请求返回后的callback
         */
        this.callback = null;


        /**
            @property {int} longNextRequestInterval 在长轮询中，一个请求结束后产生下一个请求的间隔
         */
        this.longNextRequestInterval = 0;

        /**
            @property {int} longTryInterval 在长轮询中，页面重试连接的间隔
         */
        this.longTryInterval = 500;

        /**
            @property {int} longMaxLiveAge 在长轮询中，页面处于Live状态的最长时间，
            防止页面关闭后storage中依然为live状态的情况下导致的无请求发出
         */
        this.longMaxLiveAge = 65*1000;

        /**
             @property {boolean} cache 在长轮询中，是否使用jquery ajax cache
         */
        this.cache = false;

        /**
            @name Poller#beforepoll
            @event
            @name Poller#afterpoll
            @event
            @name Poller#pollfail
            @event
            @name Poller#pollsuccess
            @event
         */
        CustEvent.createEvents(this, 'beforepoll,afterpoll,pollfail,pollsuccess');

        K.mix( this, options );

        //是否由本页面发起请求
        this.localRequest = false;

        this.errCount = 0;

        this.init();
    }

    K.mix( Poller.prototype, {
        init: function(){
            //获取存储器
            this.storage = Storage;
            if( !Storage.isAvailable() ){
                this.useLocalShare = false;
            }

            this.genKey();

            if( this.type == 'long' ){
                this.interval = 0;
                //长轮询的live状态最长持续时间
                this.liveAge = this.longMaxLiveAge;
            }

            var ins = this;
            $(window).bind('beforeunload',function(){
                if( ins.localRequest && ins.useLocalShare ){
                    ins.changeState( 0 );
                }
            });
            $(window).bind('unload',function(){
                if( ins.localRequest && ins.useLocalShare ){
                    ins.changeState( 0 );
                }
            });
        },
        start: function(){
            if( !this.useLocalShare ){
                this.request();
            }
            else{
                var st = this.storage,
                    ins = this;

                this.monitor();
                this.tryConnect();
            }
        },
        request: function(){
            if( this.pollTimer ){
                clearTimeout( this.pollTimer );
            }

            this.fire('beforepoll');
            $.ajax({
                url:        this.url,
                type:       this.requestType,
                cache:      this.cache,
                data:       this.requestData,
                context:    this.requestContext,
                timeout:    this.timeout,
                success:    $.proxy(this.onSuccess,this),
                error:      $.proxy(this.onError,this)
            });
        },
        stop: function() {
            clearTimeout(this.pollTimer);
        },
        onSuccess: function( data ){
            this.fire('afterpoll', data);
            this.fire('pollsuccess', data);

            var ins = this,
                interval = this.interval;

            //长轮询下需要立即开始新的请求
            if( this.type == 'long' ){
                interval = this.longNextRequestInterval;
            }

            if( !this.useLocalShare ){
                this.pollTimer = setTimeout( function(){
                    ins.request();
                }, interval );
            }
            else{
                this.changeState(0);
                this.changeData( data );
                this.pollTimer = setTimeout( function(){
                    ins.tryConnect();
                }, interval );
            }

            if( this.requestDataType && this.requestDataType.toLowerCase() == 'json' ){
                data = $.parseJSON( data );
            }
            this.callback && this.callback.call( this.requestContext, data );

            //不会触发自己的浏览器在此处将localRequest设置为false(因为在onstorage中根本不会用到localRequest为true的情况)
            //而会触发自己的浏览器由于需要在onstorage中判断localRequest,而且是异步判断，所以在onstorage中处理
            if( this.type != 'long' && ( !Storage.isStoreTriggerSelf() && !Storage.isStoreUseTimer() )  ){
                this.localRequest = false;
            }
        },
        onError: function( xhr, status ){
            this.errCount++;
            if( this.useLocalShare ){
                this.changeState(0);
            }
            this.fire('afterpoll', xhr, status);
            this.fire('pollfail', xhr, status);
            this.retry();
        },
        retry: function(){
            var retryInterval = this.errCount * 1000,
                ins = this;
            if( !this.useLocalShare ){
                setTimeout( function(){
                    ins.request();
                }, retryInterval );
            }
            else{
                setTimeout( function(){
                    ins.tryConnect();
                }, retryInterval );
            }
        },
        /*
            尝试获取请求权
         */
        tryConnect: function(){
            //无需请求
            var ins = this,
                interval = this.interval;

            //长轮询需要缩短重试时间，防止所有页面长时间都没有发出请求
            if( this.type == 'long' ){
                interval = this.longTryInterval;
            }

            if( this.isDataValid() || this.isLive() ){
                //interval时长之后再次尝试
                this.pollTimer = setTimeout( function(){
                    ins.tryConnect();
                }, interval );
                return;
            }
            this.pk(function(){
                //获得请求权
                ins.changeTimestamp();
                ins.changeState( 1 );
                ins.localRequest = true;
                ins.request();

            },function(){
                //未获得请求权
                ins.localRequest = false;
                //interval时长之后再次尝试
                ins.pollTimer = setTimeout( function(){
                    ins.tryConnect();
                }, interval );
            });
        },
        /*
            修改请求状态
         */
        changeState: function( state ){
            this.storage.setItem( this.stateKey, state );
        },
        /*
            修改请求状态
         */
        changeTimestamp: function( ){
            this.storage.setItem( this.timestampKey, new Date()*1 )
        },
        /*
            修改数据
         */
        changeData: function( data ){
            this.storage.setItem( this.dataKey, data )
        },
        /*
            监听数据变更
         */
        monitor: function(){
            var st = this.storage,
                ins = this;
            if( ins.callback ){
                st.onstorage( this.dataKey, function(data){
                    //只有当当前页面无请求时才执行callback
                    if( !ins.localRequest ){
                        if( ins.requestDataType && ins.requestDataType.toLowerCase() == 'json' ){
                            data = $.parseJSON( data );
                        }
                        ins.callback.call( ins.requestContext, data );
                    }
                    /*
                        短轮询时需要在每次onstorage时都将localRequest设置为false，只有这样才能收到其他页面同步过来的数据
                        仅用于Storage.isStoreTriggerSelf()之内的浏览器
                        而长轮询一旦一个页面获得权限后将一直处于Master的位置，因此不必修改localRequest，直到关闭
                     */
                    if( ins.type != 'long' && ( Storage.isStoreTriggerSelf() || Storage.isStoreUseTimer() )  ){
                        ins.localRequest = false;
                    }
                });
            }
        },
        /*
            检查数据是否过期
         */
        isDataValid: function(){
            var st = this.storage,
                timestamp = parseInt( st.getItem( this.timestampKey ), 10 );
            return (new Date() * 1 - timestamp ) < this.interval;
        },
        /*
            检查当前是否有进行中的请求
            无请求时key值为0
            storage中标记为请求中并且liveAge没有到期
         */
        isLive: function(){
            var st = this.storage,
                state = parseInt( st.getItem( this.stateKey ), 10 ),
                timestamp = parseInt( st.getItem( this.timestampKey ), 10 );
            var isLive = !!state && (new Date() * 1 - timestamp ) < this.liveAge;
            return isLive;
        },
        genKey: function(){
            //生成key前缀
            this.keyPrefix = 'kxpoller_' + this.url.replace(/[^\w]+/ig,'');
            //时间戳key
            this.timestampKey = this.keyPrefix + '_timestamp';
            //数据key
            this.dataKey = this.keyPrefix + '_data';
            //状态key
            this.stateKey = this.keyPrefix + '_state';
            //pk key
            this.pkKey = this.keyPrefix + '_pk';
        },
        /*
            防止多个页面进行同一个操作，通过写storage来争夺机会
         */
        pk: function(win,lose){
            var random = Math.random(),
                ins = this,
                st = this.storage;
            var compare = function() {
                var val = st.getItem( ins.pkKey );
                if ( val == random ) {
                    win && win();
                } else {
                    lose && lose();
                }
            };
            st.setItem( this.pkKey, random);
            setTimeout( compare, 40);
        }
    });
    return Poller;
});
