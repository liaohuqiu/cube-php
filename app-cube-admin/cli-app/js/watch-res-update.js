var argv = process.argv;
if (argv.length < 3 ) {
    var msg = 'useage: node ' + argv[1] + ' path-to-be-watched';
    console.log(msg);
    return;
}
var src_dir = argv[2];
var tmp_file = __dirname + '/auto-gen-res-list-info.json';

var fs = require('fs');
var child_process = require('child_process');
var core_io = require('./core-io.js');
child_process.env = process.env;

UpdateWatcher = function(){};
UpdateWatcher.prototype = {

    watchList: {},

    watch: function(){
        var filters = ['.svn', '_examples'];
        var that = this;
        core_io.walk_dir_self(src_dir, function(dir) {
            that.watch_dir(dir);
        }, filters);
    },

    watch_dir: function(dir){

        console.log("watch:\t" + dir);
        if (!fs.existsSync(dir))
            return;
        if (!fs.statSync(dir).isDirectory())
            return;
        if (dir.indexOf('.svn') + dir.indexOf('_examples') >= 0)
            return;

        var that = this;

        var watcher =  fs.watch(dir, function(event, filename){
            var path = dir + '/' + filename;
            if (filename.indexOf('version.js') != -1)
                return;
            if (filename.indexOf('.js') == -1 && filename.indexOf('.css') == -1) {

                // When a directory is deleted, if it is watched, event will be triggered twice;
                // After been removed, it will removed from watching list.
                if (!fs.existsSync(dir)) {
                    return;
                }

                if (!fs.existsSync(path)) {
                    if (that.watchList[path] != null) {
                        console.log('dir is been deleted: ' + dir);
                        delete that.watchList[path];
                    }
                } else {
                    // This diretory is created, should been watched.
                    var path_stat = fs.statSync(path);
                    if (path_stat.isDirectory()) {
                        console.log('watch new created dir: ' + path);
                        that.watch_dir(path);
                    }
                }
            }
            else {
                that.do_update();
            }
        });
        this.watchList[dir] = watcher;
    },

    lastTimeoutId : 0,

    do_update: function(){

        clearTimeout(this.lastTimeoutId);
        this.lastTimeoutId = setTimeout(function(){

            var sys = require('sys');
            var cmd = 'node update-res-info.js ' + src_dir + ' ' + tmp_file + "\n";
            cmd += 'php dispatch-res-info.php -f ' + tmp_file + ' -t ' + src_dir;
            console.log(cmd);

            var child = child_process.exec(cmd, function(error, stdout, stderr){
                console.log('update res info');
                sys.print(stdout);
            });
        }, 1000);
    }
};
var watcher = new UpdateWatcher();
watcher.watch();
