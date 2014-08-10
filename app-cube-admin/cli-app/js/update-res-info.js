var argv = process.argv;
if (argv.length < 4 ) {
    var msg = 'useage: node ' + argv[1] + ' src-path out-put-file';
    console.log(msg);
    return;
}

var src_dir = argv[2];
var src_dir_with_slash;
var output_file = argv[3];
var cube_dir_name = 'cube';
var cube_dir_name_with_slash = cube_dir_name + '/';

var fs = require('fs');
var path = require('path');
var core_io = require('./core-io.js');
var Updater = function() {
};

var fs = require('fs');
var Updater = function(){};

Updater.prototype = {

    test_js: /(.+)(?:\/js\/)(.+)\.js/,
    test_css: /(.+)(?:\/css\/)(.+)\.css/,

    jsList: {},
    cssList: {},
    dependenceList: {},

    do_update: function (){

        if (src_dir.endsWith('/')) {
            src_dir_with_slash = src_dir;
            src_dir =  src_dir.substr(0, src_dir.length - 1);
        } else {
            src_dir_with_slash = src_dir + '/';
        }

        var filters = ['.svn', '_examples', 'version.js'];
        var that = this;
        core_io.walk_dir(src_dir, function (dir, file) {
            that.add_file_info(file);
        }, filters);

        this.update_js_info();
    },

    add_dependence_info: function(id, list){
        this.dependenceList[id] = list;
    },

    update_js_info: function(){

        for (id in this.jsList) {
            var d = this.dependenceList[id];
            if (!d)
                d = [];
            this.jsList[id].dependence = d;
        }

        var info = {
            'js' : this.jsList,
            'css': this.cssList,
        };
        fs.writeFile(output_file, JSON.stringify(info));
    },

    add_file_info: function(file){
        var stat = fs.statSync(file);
        var path = file.replace(src_dir_with_slash, '');
        var match = path.match(this.test_js);
        var type = '';
        if (match && match.length == 3){
            type = 'js';
        } else {
            match = path.match(this.test_css);
            if (match && match.length == 3){
                type = 'css';
            }
        }

        if (type != '') {

            var id = match[1] + '/' + match[2];
            id = id.replace(cube_dir_name_with_slash, '');
            var info = {};
            info.module = id;
            info.dir = src_dir_with_slash;
            info.path = path;
            info.mtime = stat.mtime.getTime().toString().slice(0, -3);

            if (type == 'js') {
                this.jsList[id] = info;
                try {
                    require(file);
                } catch (ex) {
                }
            } else {
                this.cssList[id] = info;
            }
        }
    },
};

var updater = new Updater();

define = function(id, list, f){
    if (typeof(list) == 'function')
        list = [];
    updater.add_dependence_info(id, list);
}

K = {}
App = {};
App.define = function(){
};

K.App = function(id, list){
    if (typeof(list) == 'function')
        list = [];
    updater.add_dependence_info(id, list);
    return App;
};

updater.do_update();
