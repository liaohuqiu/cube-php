/*jsl:option explicit*/
/*jsl:declare document*/
/*
This class is used to cut the string which is having html tags. 
It does not count the html tags, it just count the string inside tags and keeps
the tags as it is.

ex: If the string is "welcome to <b>JS World</b> <br> JS is bla". and If we want to cut the string of 12 charaters then output will be "welcome to <b>JS</b>". 

Here while cutting the string it keeps the tags for the cutting string and skip the rest and without distorbing the div structure.

USAGE:
 var obj = new cutString("welcome to <b>JS World</b> <br> JS is",12);
 var newCutString = obj.cut();
 
 # want to take some img and make it take place, use this:
 var obj = new cutString("welcome to <b>JS World</b> <br> JS <img class="icon" src="face.gif" />is", 12, {'IMG':3});
 var newCutString = obj.cut();
*/
define('core/cutHTML', [], function(require) {
    
    function CutString(string, limit, nodeMap){
        // temparary node to parse the html tags in the string
        this.tempDiv = document.createElement('div');
        this.tempDiv.id = "TempNodeForTest";
        this.tempDiv.innerHTML = string;
        // while parsing text no of characters parsed
        this.nodeMap = nodeMap || {};
        this.charCount = 0;
        this.limit = limit;
        this.cuted = false; // 标识是否有截断
    }
    
    CutString.prototype.cut = function(){
        var newDiv = document.createElement('div');
        this.searchEnd(this.tempDiv, newDiv);
        return newDiv.innerHTML;
    };
    
    CutString.prototype.searchEnd = function(parseDiv, newParent){
        var ele, newEle, eleLength;
        for(var j=0; j< parseDiv.childNodes.length; j++){
            ele = parseDiv.childNodes[j];
            newEle = ele.cloneNode(true);
            // not text node
            if(ele.nodeType != 3){
                newParent.appendChild(newEle);
                if(ele.childNodes.length === 0) {
                    eleLength = this.nodeMap[ele.tagName];
                    if(typeof eleLength === 'number') {
                        if(eleLength + this.charCount > this.limit) {
                            this.cuted = true;
                            return true;
                        }
                        else this.charCount += eleLength;
                    }
                    continue;
                }
                newEle.innerHTML = '';
                var res = this.searchEnd(ele,newEle);
                if(res) return res;
                else continue;
            }
    
            // the limit of the char count reached
            if(K.byteLen(ele.nodeValue) + this.charCount > this.limit){
                newEle.nodeValue = K.subByte(ele.nodeValue, this.limit - this.charCount);
                newParent.appendChild(newEle);
                this.cuted = true;
                return true;
            }
            newParent.appendChild(newEle);
            this.charCount += K.byteLen(ele.nodeValue);
        }
        return false;
    };
    
    function cutHTML(string, limit, nodeMap, def){
        var cutString = new CutString(string, limit, nodeMap),
            result = cutString.cut();

        if(cutString.cuted && def) {
            result += def;
        }
        return result;
    }
    
    return cutHTML;
});
