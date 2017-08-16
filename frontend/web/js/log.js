/**
 * Created by DongpoLiu on 1/3/15.
 */

/**
 * Closure compiler压缩后会替换成true.
 * @define {boolean}
 */
var COMPILED = false;

var activity = {
    tracker: {
        url: '/site/log',

        img: null,

        info: function(msg) {
            this.log({
                type: 'info',
                msg: msg
            });
        },

        warning: function(msg) {
            this.log({
                type: 'warning',
                msg: msg
            });
        },

        error: function(msg) {
            this.log({
                type: 'error',
                msg: msg
            });
        },

        log: function(obj) {
            var params = [], url;
            for (var p in obj) {
                params.push(p + '=' + encodeURIComponent(obj[p]));
            }
            url = this.url + '?' + params.join('&');
            this.get(url);
        },

        get: function(url) {
            var me = this;
            me.img = new Image();
            me.img.onload = me.img.onerror = me.img.onabort = function() {
                me.img = null;
            };
            me.img.src = url.substring(0, 2048);
        }
    }
};

window.onerror = function(msg, url, line) {
    var type = 'error';
    if (msg == 'Uncaught ReferenceError: WeixinJSBridge is not defined' ||
        msg == 'ReferenceError: Can\'t find variable: WeixinJSBridge' ||
        msg == 'ReferenceError: Can\'t find variable: _WXJS') {
        type = 'warning';
    }
    if (typeof msg == 'object') {
        var target = msg.target || msg.srcElement;
        if (target == '[object HTMLScriptElement]') {
            msg = 'Error loading script';
            type = 'warning';
        } else {
            var temp = '';
            for (var x in msg) {
                if (temp) temp += ', ';
                temp += x + ': ' + msg[x];
            }
            msg = '{' + temp + '}';
        }
    }
    activity.tracker.log({
        type: type,
        msg: msg,
        url: url,
        line: line
    });
    return COMPILED;
};