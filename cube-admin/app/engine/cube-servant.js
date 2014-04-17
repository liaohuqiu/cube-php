var http = require('http');
var fs = require('fs');
var qs = require('querystring');
var path = require('path');
var child_process = require('child_process');

var sys = require('sys');
var current_dir = process.cwd();
var app_root = path.dirname(current_dir);

Dispatcher = function() {};
Dispatcher.prototype = {
    dispatch: function(res, cmd, data) {
        if (cmd == 'update-config') {
            var key = data.key;
            var config = JSON.stringify(data.data);
            this.msg('update config for key: ' + key);
            var cmd = 'php ' + current_dir + '/update-data-config.php -k ' + key + " -d '" + config + "'";
            var child = child_process.exec(cmd, function(error, stdout, stderr){
                sys.print(stdout);
                res.writeHead(200, {'Content-Type': 'text/plain'});
                res.end('ok');
            });
        }
    },

    msg: function(msg) {
        // console.log('[' + moment().format('YYYY-MM-DD H:mm:ss') + '] ' + msg);
        console.log('[' + new Date().toString() + '] ' + msg);
    },

    getDataConfig: function(callback, key, env) {
        var cmd = 'php ' + current_dir + '/get-data-config.php -k ' + key + ' --env ' + env;
        var child = child_process.exec(cmd, function(error, stdout, stderr){
            callback.call(null, JSON.parse(stdout));
        });
    },
}

var dispatcher = new Dispatcher();
dispatcher.getDataConfig(function(config){
    var port = config['cube-servant-port'];
    http.createServer(function (request, res) {
        if (request.method == 'POST') {
            var body = '';
            request.on('data', function (data) {
                body += data;
            });
            request.on('end', function () {
                var post = qs.parse(body);
                dispatcher.dispatch(res, post.cmd, JSON.parse(post.data));
            });
        } else {
            res.writeHead(200, {'Content-Type': 'text/plain'});
            res.end('error');
        }
    }).listen(port);
    dispatcher.msg('servant at port: ' + port);
}, 'mix', 1);
