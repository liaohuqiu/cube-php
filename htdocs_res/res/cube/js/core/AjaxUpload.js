define('core/AjaxUpload', [], function(require) {

    var App  = function(options) {};

    K.mix(App, {

        createUploadIframe: function(id, uri) {
            var frameId = 'jUploadFrame' + id;
            var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';

            if (window.ActiveXObject) {
                if(typeof uri== 'boolean'){
                    iframeHtml += ' src="' + 'javascript:false' + '"';
                }
                else if(typeof uri== 'string'){
                    iframeHtml += ' src="' + uri + '"';
                }
            }
            iframeHtml += ' />';
            jQuery(iframeHtml).appendTo(document.body);
            return jQuery('#' + frameId).get(0);
        },

        createUploadForm: function(id, fileElementId, data) {
            //create form
            var formId = 'jUploadForm' + id;
            var fileId = 'jUploadFile' + id;
            var form = jQuery('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
            if (data) {
                for(var i in data){
                    jQuery('<input type="hidden" name="' + i + '" value="' + data[i] + '" />').appendTo(form);
                }
            }
            var oldElement = jQuery('#' + fileElementId);
            var newElement = jQuery(oldElement).clone();
            oldElement.attr('id', fileId);
            oldElement.before(newElement);
            oldElement.appendTo(form);

            //set attributes
            form.css('position', 'absolute');
            form.css('top', '-1200px');
            form.css('left', '-1200px');
            form.appendTo('body');
            return form;
        },

        ajaxFileUpload: function(conf) {

            conf = jQuery.extend({}, conf);

            var id = new Date().getTime()
            var frameId = 'jUploadFrame' + id;
            var formId = 'jUploadForm' + id;

            var form = this.createUploadForm(id, conf.fileElementId, (typeof(conf.data)=='undefined'?false:conf.data));
            var io = this.createUploadIframe(id, conf.secureuri);

            // Watch for a new set of requests
            if (conf.global && !jQuery.active++ ) {
                jQuery.event.trigger( "ajaxStart" );
            }
            var requestDone = false;
            // Create the request object
            var xml = {}
            if ( conf.global ) {
                jQuery.event.trigger("ajaxSend", [xml, conf]);
            }
            // Wait for a response to come back
            var that = this;
            var uploadCallback = function(isTimeout) {
                var io = document.getElementById(frameId);
                try {
                    if(io.contentWindow) {
                        var doc = io.contentWindow.document;
                        xml.responseText = doc.body ? doc.body.innerHTML : null;
                        if (doc.XMLDocument) {
                            xml.responseXML = doc.XMLDocument;
                        } else {
                            xml.responseXML = doc;
                        }
                    } else if(io.contentDocument) {
                        var doc = io.contentDocument.document;
                        xml.responseText = doc.body ? doc.body.innerHTML : null;
                        xml.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
                    }
                } catch(e) {
                    that.handleError(conf, xml, null, e);
                }
                if (xml || isTimeout == "timeout") {
                    requestDone = true;
                    var status;
                    try {
                        status = isTimeout != "timeout" ? "success" : "error";
                        // Make sure that the request was successful or notmodified
                        if ( status != "error" ) {
                            // process the data (runs the xml through httpData regardless of callback)
                            var data = that.uploadHttpData( xml, conf.dataType );
                            // If a local callback was specified, fire it and pass it the data
                            if ( conf.success )
                                conf.success( data, status );

                            // Fire the global callback
                            if( conf.global )
                                jQuery.event.trigger( "ajaxSuccess", [xml, conf] );
                        } else {
                            that.handleError(conf, xml, status);
                        }
                    } catch (e) {
                        status = "error";
                        that.handleError(conf, xml, status, e);
                    }

                    // The request was completed
                    if( conf.global )
                        jQuery.event.trigger( "ajaxComplete", [xml, conf] );

                    // Handle the global AJAX counter
                    if ( conf.global && ! --jQuery.active )
                        jQuery.event.trigger( "ajaxStop" );

                    // Process result
                    if ( conf.complete )
                        conf.complete(xml, status);

                    jQuery(io).unbind()

                    setTimeout(function() {
                        try {
                            jQuery(io).remove();
                            jQuery(form).remove();

                        } catch(e) {
                            that.handleError(conf, xml, null, e);
                        }

                    }, 100)
                    xml = null
                }
            }

            // Timeout checker
            if ( conf.timeout > 0) {
                setTimeout(function(){
                    // Check to see if the request is still happening
                    if( !requestDone ) {
                        uploadCallback( "timeout" );
                    }
                }, conf.timeout);
            }

            try {
                var form = jQuery('#' + formId);
                jQuery(form).attr('action', conf.url);
                jQuery(form).attr('method', 'POST');
                jQuery(form).attr('target', frameId);
                if(form.encoding) {
                    jQuery(form).attr('encoding', 'multipart/form-data');
                } else {
                    jQuery(form).attr('enctype', 'multipart/form-data');
                }
                jQuery(form).submit();

            } catch(e) {
                jQuery.handleError(conf, xml, null, e);
            }

            jQuery('#' + frameId).load(uploadCallback);
            return {abort: function () {}};
        },

        handleError: function() {
            K.log('handleError');
            K.log(arguments);
        },

        uploadHttpData: function( r, type ) {
            var data = !type;
            data = type == "xml" || data ? r.responseXML : r.responseText;
            // If the type is "script", eval it in global context
            if ( type == "script" )
                jQuery.globalEval( data );
            // Get the JavaScript object, if JSON is used.
            if ( type == "json" )
                eval( "data = " + data );
            // evaluate scripts within html
            if ( type == "html" )
                jQuery("<div>").html(data).evalScripts();

            return data;
        }
    });
    return App;
});
