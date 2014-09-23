"use strict";
/**
* 文件上传应用，包括Flash上传和Form上传。<br>
* 构造函数的参数配置如下：
* <pre><code>
{String}	type			上传方式( flash 或 form )
- 默认为自动检测(IE、Safari使用Flash，其他使用Form)
{String}	maxSize			文件允许上传的最大体积(例如'1 KB' '4 MB') - 默认为15 MB
{String}	fileType		文件类型 - 默认为"*.*"
{String}	filePostName	File Input的name - 默认为"Filedata"
{String}	serverURL		服务器URL - 默认为空
{Integer}	maxNum			最多允许上传的文件数 - 默认为100
{Integer}	maxRetry		文件上传失败后的重试次数 - 默认为0
{Boolean}	enablDup		允许上传队列中存在重复的文件 - 默认为false
{Array}		QUEUE_ERR_MSGS	上传前错误提示信息
[
超过文件体积限制提示,
文件为空,
格式错误,
超过数量限制提示
]
{Object}	postParam		Key/Value对，上传文件的同时发送给服务器的数据
{String}	btnImgURL		[Flash]供Flash使用的上传按钮图片URL（图片应该包含点击时4个状态的图片）
{String}	btnWrapID		[Flash]上传按钮所在容器ID，Flash加载后容器的内容会被Flash覆盖
{Integer}	btnWidth		[Flash]上传按钮宽度
{Integer}	btnHeight		[Flash]上传按钮高度
{String}	flashURL		[Flash]Flash URL
{String}	flash9URL		[Flash]Flash 9 URL(如果不支持版本10则会使用9)
{Boolean}	preventCache	[Flash]禁止缓存Flash
{Boolean}	enableDebug		[Flash]使用Debug
{String}	dndAreaID		[Form]Form中拖拽上传功能中可放置文件的元素ID
{String}	thumbHeight		[Form]Form上传会生成缩略图，该参数指定缩略图的高度
{String}	thumbWidth		[Form]Form上传会生成缩略图，该参数指定缩略图的宽度
{String}	inputFontSize	[Form]Form上传中使用

支持的自定义事件
上传流程相关事件：
file_dialog_start:		弹出选择文件窗口
{Object} uploader: 上传应用实例
file_queue_error:		选择文件后出错
{Object} uploader: 上传应用实例
{Object} file: 文件实例
{Integer} errorCode: 错误码
{String} message: 错误消息(错误码见附录)

file_queue:				文件加入上传队列
{Object} uploader: 上传应用实例
{Object} file: 文件实例

file_duplicated:		文件重复切选项中enableDup设置为false时才会触发
{Object} uploader: 上传应用实例
{array} files: 重复文件列表

file_dialog_complete:	选择文件窗口关闭
{Object} uploader: 上传应用实例
{Integer} numFilesSelected: 选择的文件数
{Integer} numFilesQueued: 成功加入队列的文件数

upload_start:			开始上传文件( file )
{Object} uploader: 上传应用实例
{Object} file: 文件实例

resize_upload_start:	开始压缩并上传文件(仅上传JPEG图片时触发)
{Object} uploader: 上传应用实例
{Object} file: 文件实例

upload_error:			文件上传失败
{Object} uploader: 上传应用实例
{Object} file: 文件实例
{Integer} errorCode: 错误码

upload_success:			文件上传成功
{Object} uploader: 上传应用实例
{Object} file: 文件实例
{String} serverData: 服务器返回数据

upload_progress:		文件上传中
{Object} uploader: 上传应用实例
{Object} file: 文件实例
{Integer} bytesLoaded: 已上传字节数
{Integer} bytesTotal: 文件总字节数

upload_complete:		文件上传完成（包括成功和失败）
{Object} uploader: 上传应用实例
swfupload_loaded:		[FLASH]	Flash加载完成
{Object} uploader: 上传应用实例
swfupload_failed:		[FLASH]	Flash加载失败
{Object} uploader: 上传应用实例
swfupload_debug:		[FLASH]	Flash上传的Debug信息
{Object} uploader: 上传应用实例
{String} msg: debug信息

thumb_success:			[Form]	缩略图生成完毕(仅在上传图片时有效）
{Object} uploader: 上传应用实例
{Object} file: 文件实例
{String} imgsrc: 图片缩略图src
{boolean} resized: 是否已经resize处理

drag_end:				[Form]	Form拖拽上传时文件Drop时触发
{Object} uploader: 上传应用实例
{array} files: 拖拽的文件实例(数组)

drag_over:				[Form]	Form拖拽上传时鼠标over到拖拽区域时触发
{Object} uploader: 上传应用实例

drag_enter:				[Form]	Form拖拽上传时鼠标进入到拖拽区域时触发
{Object} uploader: 上传应用实例

drag_leave:				[Form]	Form拖拽上传时鼠标离开拖拽区域时触发
{Object} uploader: 上传应用实例

错误码定义：
QUEUE_ERROR = {
QUEUE_LIMIT_EXCEEDED            : -100,
FILE_EXCEEDS_SIZE_LIMIT         : -110,
ZERO_BYTE_FILE                  : -120,
INVALID_FILETYPE                : -130
};
UPLOAD_ERROR = {
HTTP_ERROR                      : -200,
MISSING_UPLOAD_URL              : -210,
IO_ERROR                        : -220,
SECURITY_ERROR                  : -230,
UPLOAD_LIMIT_EXCEEDED           : -240,
UPLOAD_FAILED                   : -250,
SPECIFIED_FILE_ID_NOT_FOUND     : -260,
FILE_VALIDATION_FAILED          : -270,
FILE_CANCELLED                  : -280,
UPLOAD_STOPPED                  : -290,
RESIZE                          : -300
};
FILE_STATUS = {
QUEUED       : -1,
IN_PROGRESS  : -2,
ERROR        : -3,
COMPLETE     : -4,
CANCELLED    : -5
};

其他事件：
before_init:		初始化之前触发
after_init:			初始化之后触发
available:			上传应用可用时触发(Flash加载完毕等)
before_upload:		上传文件前触发
* </code></pre>
* @module uploader
* @class Uploader
* @constructor
*/
define('core/uploader/Uploader', ['jQuery'], function(require) {
    var $ = require('jQuery'),
    //Flash或Form上传应用
    proxyUploader = null,
    //异步加载
    SWFUploader = null,
    //异步加载
    FormUploader = null;

    function Uploader(opts) {
        //配置信息
        this.config = {
            type: null,
            maxSize: '15 MB',
            fileType: '*.*',
            filePostName: 'Filedata',
            serverURL: null,
            maxNum: 100,
            maxRetry: 0,
            enablDup: false,
            QUEUE_ERR_MSGS: ['超过文件大小限制', '文件内容为空，请确认', '请检查您的文件格式', '一次最多上传100个文件'],
            postParam: {},
            btnImgURL: null,
            btnWrapID: null,
            btnWidth: null,
            btnHeight: null,
            flashURL: '/swf/swfupload_f10.swf',
            flash9URL: '/swf/common/kxupload_f9.swf',
            preventCache: true,
            enableDebug: false,
            dndAreaID: null,
            thumbHeight: 83,
            thumbWidth: 113,
            inputFontSize: '200%'
        };

        K.mix(this.config, opts);

        //自定义事件
        K.CustEvent.createEvents(this, [
                                 /*上传文件流程相关事件*/
                                 'file_dialog_start',
                                 'file_queue_error',
                                 'file_queue',
                                 'file_dialog_complete',
                                 'upload_start',
                                 'resize_upload_start',
                                 'upload_error',
                                 'upload_success',
                                 'upload_progress',
                                 'upload_complete',
                                 'swfupload_loaded',
                                 'swfupload_failed',
                                 'swfupload_debug',
                                 'thumb_success',
                                 'drag_end',
                                 'drag_over',
                                 'drag_enter',
                                 'drag_leave',
                                 'file_duplicated',
                                 /*其他事件*/
                                 'before_init',
                                 'after_init',
                                 'available',
                                 'before_upload'
        ]);

        this.init();
    }

    K.mix(Uploader.prototype, {
        init: function() {
            //是否正在上传状态
            this.isUploading = false;

            //确定上传方式
            if ((!(this.config.type) || this.config.type.toLowerCase() === 'form')) {
                this.uploaderType = 'form';
            }
            else {
                this.uploaderType = 'flash';
            }

            //记录当前已在队列中的文件信息（防止重复加入）
            this.existedFiles = {};
            //记录已重复文件
            this.dupFiles = [];

            //记录每个文件的retry次数
            this.retryMap = {};

            //增加默认的Post参数
            this.addPostParam('_user', this.getCookie('_user'));

            this.fire('before_init');
            this.initUploader();
            this.fire('after_init');
        },

        /*初始化上传应用*/
        initUploader: function() {
            //初始化上传应用
            if (this.uploaderType === 'form') {
                this.initFormUploader();
            }
            else {
                this.initSWFUploader();
            }
        },

        /*初始化Flash上传*/
        initSWFUploader: function() {
            //裁剪阈值(大于该值才裁剪-1000K)
            this.resizeThreshold = 1000 * 1024;

            //压缩最大时长(超过该值会放弃压缩 - 60s)
            this.resizeTimeout = 60 * 1000;
            this.resizeTimeouted = false;

            //裁剪大小(仅对图片有效)
            this.resizeWidth = 1500;
            this.resizeHeight = 7800;
            this.resizeQuality = 92;

            var doInit = $.proxy(function() {
                try {
                    this.uploader = new SWFUploader({
                        upload_url: this.config.serverURL,
                        file_size_limit: this.config.maxSize,
                        file_types: this.config.fileType,
                        file_upload_limit: this.config.maxNum,
                        file_post_name: this.config.filePostName,
                        file_dialog_start_handler: $.proxy(this.eFileDialogStart, this),
                        file_queue_error_handler: $.proxy(this.eFileQueueError, this),
                        file_queued_handler: $.proxy(this.eFileQueued, this),
                        file_dialog_complete_handler: $.proxy(this.eFileDialogComplete, this),
                        upload_start_handler: $.proxy(this.eUploadStart, this),
                        upload_resize_start_handler: $.proxy(this.eResizeUploadStart, this),
                        upload_error_handler: $.proxy(this.eUploadError, this),
                        upload_success_handler: $.proxy(this.eUploadSuccess, this),
                        upload_progress_handler: $.proxy(this.eUploadProgress, this),
                        upload_complete_handler: $.proxy(this.eUploadComplete, this),
                        swfupload_loaded_handler: $.proxy(this.eSwfuploadLoaded, this),
                        swfupload_load_failed_handler: $.proxy(this.eSwfuploadFailed, this),
                        debug_handler: $.proxy(this.eSwfuploadDebug, this),
                        button_image_url: this.config.btnImgURL,
                        button_placeholder_id: this.config.btnWrapID,
                        button_action: proxyUploader.BUTTON_ACTION.SELECT_FILES,
                        button_width: this.config.btnWidth,
                        button_height: this.config.btnHeight,
                        button_window_mode: proxyUploader.WINDOW_MODE.TRANSPARENT,
                        button_cursor: proxyUploader.CURSOR.HAND,
                        flash_url: this.config.flashURL,
                        flash9_url: this.config.flash9URL,
                        prevent_swf_caching: this.config.preventCache,
                        debug: this.config.enableDebug
                    });

                    //图片压缩编码(仅对图片有效)
                    if (proxyUploader.RESIZE_ENCODING) {
                        this.encoder = {
                            '.jpg': proxyUploader.RESIZE_ENCODING.JPEG,
                            '.jpeg': proxyUploader.RESIZE_ENCODING.JPEG
                        };
                    }
                }
                catch (e) { }
            }, this);

            //异步加载脚本
            require.async('SWFUpload', function(Loader) {
                SWFUploader = Loader;
                proxyUploader = Loader;
                doInit();
            });
        },
        /*初始化普通表单上传*/
        initFormUploader: function() {
            var doInit = $.proxy(function() {
                this.uploader = new FormUploader({
                    upload_url: this.config.serverURL,
                    file_size_limit: this.config.maxSize,
                    file_types: this.config.fileType,
                    file_upload_limit: this.config.maxNum,
                    file_post_name: this.config.filePostName,
                    file_dialog_start_handler: $.proxy(this.eFileDialogStart, this),
                    file_queue_error_handler: $.proxy(this.eFileQueueError, this),
                    file_queued_handler: $.proxy(this.eFileQueued, this),
                    file_dialog_complete_handler: $.proxy(this.eFileDialogComplete, this),
                    upload_start_handler: $.proxy(this.eUploadStart, this),
                    upload_error_handler: $.proxy(this.eUploadError, this),
                    upload_success_handler: $.proxy(this.eUploadSuccess, this),
                    upload_progress_handler: $.proxy(this.eUploadProgress, this),
                    upload_complete_handler: $.proxy(this.eUploadComplete, this),
                    thumbnail_success_handler: $.proxy(this.eThumbnailSuccess, this),
                    drag_end_handler: $.proxy(this.eDragEnd, this),
                    drag_over_handler: $.proxy(this.eDragOver, this),
                    drag_enter_handler: $.proxy(this.eDragEnter, this),
                    drag_leave_handler: $.proxy(this.eDragLeave, this),
                    button_placeholder_id: this.config.btnWrapID,
                    input_font_size: this.config.inputFontSize,
                    drop_area: '#' + this.config.dndAreaID
                });

                var me = this;
                setTimeout(function() {
                    me.fire('available', {
                        uploader: me
                    });
                }, 20);
            }, this);

            //异步加载脚本
            require.async('core/uploader/FormUploader', function(Loader) {
                FormUploader = Loader;
                proxyUploader = Loader;
                doInit();
            });
        },
        //开始上传文件
        startUpload: function() {
            if (this.uploader.getStats().files_queued === 0) {
                return;
            }
            var fileToUp = this.uploader.getFile(0),
            postParam = this.config.postParam;

            for (var i in postParam) {
                if (postParam.hasOwnProperty(i)) {
                    this.uploader.addPostParam(i, postParam[i]);
                }
            }

            this.fire('before_upload', {
                uploader: this,
                file: fileToUp
            });

            //如果是JPG则使用裁剪上传(PNG比较慢，暂不使用)
            //Resize可能会导致图片大小为空，对此情况采取重试策略(重试时不使用resize)
            var retryCount = this.retryMap[fileToUp.id] || 0;
            if (this.uploader.support
                && this.uploader.support.imageResize
            && this.encoder
            && this.encoder[fileToUp.type.toLowerCase()]
            && !retryCount
            && this.resizeThreshold < fileToUp.size
            //未出现过压缩超时现象
            && !this.resizeTimeouted
               ) {
                   this.useResizeUpload = true;

                   this.uploader.startResizedUpload(
                       fileToUp.id,
                       this.resizeWidth,
                       this.resizeHeight,
                       this.encoder[fileToUp.type.toLowerCase()],
                       this.resizeQuality,
                       false,
                       true);
               }
               else {
                   this.useResizeUpload = false;
                   this.uploader.startUpload();
               }
        },
        /*新的swfupload中getFile方法传入fileID失效，但是可以用index替代*/
        getFile: function(fileID) {
            for (var i = 0; i < 500; i++) {
                var file = this.uploader.getFile(i);
                if (file && file.id === fileID) {
                    return file;
                }
            }
        },
        /*
        * 删除文件
        * @param {object||string} file 需要删除的文件对象或文件ID
        */
        removeFile: function(file) {
            if (typeof (file) === 'string') {
                file = this.getFile(file);
            } /*待上传或上传中的需要手动取消上传，此时会自动修改上传文件数量的计数器*/
            if (file
                && (file.filestatus === proxyUploader.FILE_STATUS.QUEUED
                    || file.filestatus === proxyUploader.FILE_STATUS.IN_PROGRESS)
               ) {

                   //会触发complete但是是异步的，所以需要提前修改inUPLoading状态
                   this.isUploading = false;
                   //取消上传(强制不触发eUploadError事件)
                   this.uploader.cancelUpload(file.id, false);
               }
               else if (file && file.filestatus === proxyUploader.FILE_STATUS.COMPLETE) {
                   var succNum = this.uploader.getStats().successful_uploads;
                   this.uploader.setStats({
                       successful_uploads: succNum - 1
                   });
               }

               //删除已存在信息
               delete this.existedFiles[this.getFileStamp(file)];
        },
        //根据文件生成其特征戳
        getFileStamp: function(file) {
            return file.name +
                '#' +
                file.size +
                '#' +
                file.type +
                '#' +
                file.creationdate +
                '#' +
                file.modificationdate;
        },
        //文件是否已经存在于上传列表
        isFileInList: function(file) {
            return !!this.existedFiles[this.getFileStamp(file)];
        },
        //是否处于上传状态( 上传队列不为空或有文件正在上传 )
        isInUPLoading: function() {
            return this.uploader.getStats().files_queued > 0 || this.isUploading;
        },
        /**
        * 增加POST到服务器的数据
        * @param {string} key 数据key
        * @param {string} val 数据value
        */
        addPostParam: function(key, val) {
            this.config.postParam[key] = val;
        },
        /**
        * 获取指定key对应的Cookie值
        * @param {string} key 数据key
        * @return Cookie值
        */
        getCookie: function(key) {
            var ret = (new RegExp(key + '=([^;=]+)', 'i')).exec(document.cookie);

            if (ret) {
                return ret[1];
            }
            else {
                return null;
            }
        },
        eFileDialogStart: function() {
            this.dupFiles = [];
            this.fire('file_dialog_start', {
                uploader: this
            });
        },
        eFileQueueError: function(file, errorCode, message) {
            //判断存在性
            if (file && this.isFileInList(file) && !this.config.enableDup) {
                this.dupFiles.push(file);
                this.uploader.cancelUpload(file.id, false);
                return;
            }
            else if (file) {
                this.existedFiles[this.getFileStamp(file)] = true;
            }

            var errorMsg = '';
            switch (errorCode) {
                case proxyUploader.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                    errorMsg = this.config.QUEUE_ERR_MSGS[0];
                break;
                case proxyUploader.QUEUE_ERROR.ZERO_BYTE_FILE:
                    errorMsg = this.config.QUEUE_ERR_MSGS[1];
                break;
                case proxyUploader.QUEUE_ERROR.INVALID_FILETYPE:
                    errorMsg = this.config.QUEUE_ERR_MSGS[2];
                break;
                case proxyUploader.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
                    errorMsg = this.config.QUEUE_ERR_MSGS[3];
                break;
                default:
                    errorMsg = '未知错误: ' + errorCode;
            }
            this.fire('file_queue_error', {
                uploader: this,
                file: file,
                errorCode: errorCode,
                errorMsg: errorMsg
            });
        },
        //当文件选择对话框关闭消失时触发。
        //如果选择的文件成功加入上传队列，那么针对每个成功加入的文件都会触发一次该事件（N个文件成功加入队列，就触发N次此事件）。
        eFileQueued: function(file) {
            //判断存在性
            if (this.isFileInList(file) && !this.config.enableDup) {
                this.dupFiles.push(file);
                this.uploader.cancelUpload(file.id, false);
                return;
            }
            else {
                this.existedFiles[this.getFileStamp(file)] = true;
            }
            this.fire('file_queue', {
                uploader: this,
                file: file
            });
        },
        //选中文件
        eFileDialogComplete: function(numFilesSelected, numFilesQueued) {
            if (numFilesSelected > 0) {
                this.startUpload();
            }

            //如果有重复切配置中不允许重复则触发重复事件
            if (this.dupFiles.length && !this.enablDup) {
                this.fire('file_duplicated', {
                    uploader: this,
                    files: this.dupFiles
                });
            }

            this.fire('file_dialog_complete', {
                uploader: this,
                numFilesSelected: numFilesSelected,
                numFilesQueued: numFilesQueued
            });

        },
        //开始上传
        eUploadStart: function(file) {
            this.isUploading = true;
            this.fire('upload_start', {
                uploader: this,
                file: file
            });
        },
        //开始Resize并上传
        eResizeUploadStart: function(file) {
            //如果使用了resize则需要进行超时检测
            if (this.useResizeUpload && !this.resizeTimeouted) {
                if (this.resizeTimer) {
                    clearTimeout(this.resizeTimer);
                    this.resizeTimer = null;
                }

                var ins = this;
                this.resizeTimer = setTimeout(function() {
                    //压缩超时的处理
                    ins.resizeTimeouted = true;

                    this.resizeTimer = null;
                }, this.resizeTimeout);
            }

            this.fire('resize_upload_start', {
                uploader: this,
                file: file
            });
        },
        //上传出错
        eUploadError: function(file, err) {
            //如果采取Resize上传，而且服务器收到的文件大小为0则重试一次
            var retryCount = this.retryMap[file.id] || 0;
            if (this.useResizeUpload
                //空文件或者Flash Resize出错
                && (err === 4 || err === -300) && retryCount < this.config.maxRetry) {

                    this.retryMap[file.id] = retryCount + 1;
                    this.uploader.addPostParam('retry', 1);
                    this.uploader.requeueUpload(file.id);
                    return;
                }
                this.isUploading = false;
                this.fire('upload_error', {
                    uplaoder: this,
                    file: file,
                    errorCode: err
                });
        },
        //FF4+ Chrome等高级浏览器中会创建缩略图
        eThumbnailSuccess: function(file, imgsrc, resized) {
            this.fire('thumb_success', {
                uploader: this,
                file: file,
                imgsrc: imgsrc,
                resized: resized
            });
        },
        //上传成功
        eUploadSuccess: function(file, serverData) {
            var data = serverData;
            try {
                data = typeof (data) === 'string' ? $.parseJSON(data) : data;
            }
            catch (e) {
                data = serverData;
            }

            this.fire('upload_success', {
                uploader: this,
                file: file,
                serverData: data
            });
        },
        //上传中
        eUploadProgress: function(file, bytesLoaded, bytesTotal, fake) {
            this.fire('upload_progress', {
                uploader: this,
                file: file,
                bytesLoaded: bytesLoaded,
                bytesTotal: bytesTotal
            });
            if (this.resizeTimer) {
                clearTimeout(this.resizeTimer);
                this.resizeTimer = null;
            }
        },
        //上传完毕
        eUploadComplete: function(file) {
            this.fire('upload_complete', {
                uploader: this,
                file: file
            });
            this.isUploading = false;
            this.startUpload();
        },
        //Flash加载完毕
        eSwfuploadLoaded: function() {
            this.fire('swfupload_loaded', {
                uploader: this
            });
            this.fire('available', {
                uploader: this
            });
        },
        //Flash加载失败
        eSwfuploadFailed: function() {
            this.swfFailed = true;
            this.fire('swfupload_failed', {
                uploader: this
            });
            this.initFormUploader();
        },
        //Debug
        eSwfuploadDebug: function(msg) {
            this.fire('swfupload_debug', {
                uploader: this,
                msg: msg
            });
        },
        eDragEnd: function(files) {
            this.fire('drag_end', {
                uploader: this,
                files: files
            });
        },
        eDragOver: function() {
            this.fire('drag_over', {
                uploader: this
            });
        },
        eDragEnter: function() {
            this.fire('drag_enter', {
                uploader: this
            });
        },
        eDragLeave: function() {
            this.fire('drag_leave', {
                uploader: this
            });
        }
    });

    return Uploader;
});
