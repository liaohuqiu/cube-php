var fs = require('fs');
var walk_dir = exports.walk_dir = function (dir, callback, filters){
    if (!fs.existsSync(dir))
        return;
    fs.readdirSync(dir).forEach(function(item){
        for (index in filters) {
            var filter = filters[index];
            if (item.indexOf(filter) != -1)
                return;
        }
        var path = dir + '/' + item;
        var stat = fs.statSync(path);
        if (stat.isDirectory()){
            walk_dir(path, callback, filters);
        }else{
            callback.call(null, dir, path);
        }
    });
}

var walk_dir_self = exports.walk_dir_self = function (dir, callback, filters) {
    if (!fs.existsSync(dir))
        return;
    if (!fs.statSync(dir).isDirectory())
        return;
    callback.call(null, dir);
    fs.readdirSync(dir).forEach(function(item){
        for (index in filters) {
            var filter = filters[index];
            if (item.indexOf(filter) != -1)
                return;
        }
        var path = dir + '/' + item;
        walk_dir_self(path, callback, filters);
    });

};

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
}

String.prototype.beginWith = function(tag) {
    return this.indexOf(tag) === 0;
}

