/**
 * Geometry.js: portable functions for querying window and document geometry
 *
 * This module defines functions for querying window and document geometry.
 *
 * winX/Y( ): return the position of the window on the screen
 * viewportWidth/Height( ): return the size of the browser viewport area
 * docWidth/Height( ): return the size of the document
 * scrollTop( ): return the position of the horizontal scrollbar
 * scrollLeft( ): return the position of the vertical scrollbar
 *
 * Note that there is no portable way to query the overall size of the
 * browser window, so there are no getWindowWidth/Height( ) functions.
 *
 * IMPORTANT: This module must be included in the <body> of a document
 *            instead of the <head> of the document.
 */
define('core/Geometry', [], function(require) {
    
    var win = window, doc = win.document,
    
        Geometry = { // Ĭ�Ϸ���0,������һЩ����iframe���޷���ȡ��ݱ���
            'winX' : function() { return 0; },
            'winY' : function() { return 0; },
            'viewportWidth' : function() { return 0; },
            'viewportHeight' : function() { return 0; },
            'scrollLeft' : function() { return 0; },
            'scrollTop' : function() { return 0; },
            'docWidth' : function() { return 0; },
            'docHeight' : function() { return 0; }
        };
    
    if (window.screenLeft) { // IE and others
        Geometry.winX = function() { return win.screenLeft; };
        Geometry.winY = function() { return win.screenTop; };
    } else if (window.screenX) { // Firefox and others
        Geometry.winX = function() { return win.screenX; };
        Geometry.winY = function() { return win.screenY; };
    }
    
    if (window.innerWidth) { // All browsers but IE
        Geometry.viewportWidth = function() { return win.innerWidth; };
        Geometry.viewportHeight = function() { return win.innerHeight; };
        Geometry.scrollLeft = function() { return win.pageXOffset; };
        Geometry.scrollTop = function() { return win.pageYOffset; };
    } else if (doc.documentElement && doc.documentElement.clientWidth) {
        // These functions are for IE 6 when there is a DOCTYPE
        Geometry.viewportWidth = function() { return doc.documentElement.clientWidth; };
        Geometry.viewportHeight = function() { return doc.documentElement.clientHeight; };
        Geometry.scrollLeft = function() { return doc.documentElement.scrollLeft; };
        Geometry.scrollTop = function() { return doc.documentElement.scrollTop; };
    } else if (doc.body.clientWidth) {
        // These are for IE4, IE5, and IE6 without a DOCTYPE
        Geometry.viewportWidth = function() { return doc.body.clientWidth; };
        Geometry.viewportHeight = function() { return doc.body.clientHeight; };
        Geometry.scrollLeft = function() { return doc.body.scrollLeft; };
        Geometry.scrollTop = function() { return doc.body.scrollTop; };
    } 
    
    // These functions return the size of the document. They are not window
    // related, but they are useful to have here anyway.
    if (doc.documentElement && doc.documentElement.scrollWidth) {
        Geometry.docWidth = function() { return doc.documentElement.scrollWidth; };
        Geometry.docHeight = function() { return doc.documentElement.scrollHeight; };
    } else if (document.body.scrollWidth) {
        Geometry.docWidth = function() { return doc.body.scrollWidth; };
        Geometry.docHeight = function() { return doc.body.scrollHeight; };
    }
    
    return Geometry;
});
