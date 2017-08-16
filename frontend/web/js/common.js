var _postLink = {
    _appDownload: "/download?from=post",
    _map: "http://map.baidu.com"
};
var _link = {
    _appIntro: _domain,
    _appDownload: "/download?from=detail",
    _downloadGuide: "/download_guide",
    _followMp: "/post",
    _hot: "/find",
    _post: "/post",
    _login: "/login",
    _loginWithWeixin: "/go_wechat_oauth",
    _loginWithQq: "/go_qq_oauth",
    _loginWithWeibo: "/go_weibo_oauth",
    _loginWithWeixinMp: "/login/mp",
    _loginBackDefault: "/post",
    //_alipay: "/alipay_submit",
    _showMap: "/show_map",
    _checkOrder: "/pay_check_pay_order",
    _fieldCacheHandle: _domain + "field_cache_handle"
};
var _api3 = {
    _infoHintShare: "/post/api:51",
    _like: "/post/api:36",
    _guess: "/post/api:55",
    //_likeList: "/post/api:52",
    //_reviewList: "/post/api:54",
    _review: "/post/api:11",
    _joinList: "/post/api:53",
    //_joinList: "/json/api_53.json",
    _joinProperty: "/post/api:56",
    _joinParty: "/post/api:18",
    _joinRecruit: "/post/api:40",
    _getQr: "/get/api:qr",
    _getQr2: "/get/api:qr2",
    _loginWithQr: "/post/api:24",
    _loginWithMp: "/post/api:6",
    _login: "/post/api:2",
    //_getCode: "/post/api:4",
    _getCode: "/json/api_4.json",
    _checkCode: "/post/api:3",
    _register: "/post/api:1",
    _registerNew: "/post/api:8",
    _vote: "/post/api:13",
    _downloadQr: "/get/api:qrdownload",
    _downloadJoinQr: "/get/api:joinqrdownload",
    _joinCancel: "/post/api:57",
    _joinQr: "/qrcode_sign_up",
    _shareCount: "/post/api:7",
    _timelineList: "/post/api:23",
    _checkInviteCode: "/get/api:45",
    _findList: "/post/api:60",
    _report: "/post/api:10",
    _feedback: "/post/api:35",
    _reset_from_phone: "/post/api:5",
    _reset: "/post/api:21",
    _checkPayOrder: "/post/api:62",
    _recommendList: "/post/api:83",
    _pc_recommendList: "/post/api:84",
    _activity_recommend: "/post/api:85",
    _activity_review:"/post/api:88",
    _delhd: "/post/api:86",
    _mess: "/post/api:90",
    _recommend: "/post/api:91",
    _getWeiXinQr: "/post/api:93",
    _download: "/post/api:94",
    _stock: "/post/api:96",
    _isPayOrder: "/post/api:97",
    _cancelOrder: "/post/api:100",
    _leanCloud_chat: "/post/api:102",
    _timelineInfoList: "/post/api:106",
    _shopNoBindInfoList: "/post/api:107",
    _ifJoinZero: "/post/api:104",
    _shopHits: "/post/api:105",
    _saveShopInfo: "/post/api:110",
    _editShop: "/post/api:111",
    _ifPass: "/post/api:112",
    _pzImgUp: "/post/api:img_base64",
    _footerInfo: "/post/api:123",
    _shopInfo: "/post/api:124",
    //_shopInfo: "/json/api_124.json",
    _shopAttent: "/post/api:125",
    _verifyCode: "/post/api:129",
    _createPrepayOrder: "/post/api:130",
    _createSmsOrder: "/post/api:131",
    _checkOrderIsPay: "/post/api:132",
    _sponsorList: "/post/api:135",
    _createTmpl:'/post/api:140',
    _getActivity:'/post/api:141',
    _search:'/post/api:6'
}; /**跳转链接*/
var _g = function(url) {
        location.href = url;
    };

/**cookie操作*/
var _cookie = {
    _set: function(name, value, expires) {
        var _end = new Date();
        if (expires) {
            _end.setTime(_end.getTime() + (expires * 1000));
        }
        document.cookie = name + "=" + escape(value) + (expires ? (";expires=" + _end.toGMTString()) : "") + ";path=/;domain=." + document.domain;
    },
    _get: function(name) {
        var _cookie = document.cookie;
        var _start = _cookie.indexOf(name + "=");
        if (_start != -1) {
            _start += name.length + 1;
            var _end = _cookie.indexOf(";", _start);
            if (_end == -1) {
                _end = _cookie.length;
            }
            return unescape(_cookie.substring(_start, _end));
        }
        return "";
    }
};
var _$asyn = function(url, param, fun) {
        var _flag = false;
        $.ajax({
            type: "post",
            url: url,
            async: false,
            data: param,
            cache: false,
            dataType: "json",
            success: function(data) {
                if (data != null) {
                    var _state = data.state;
                    //请求正常
                    if (_state == '0') {
                        _flag = true;
                        fun(data, 200);
                    }
                    //请求异常
                    else {
                        var _error = data.error;
                        _toast._show(_error);
                        fun(data, 500);
                    }

                }
            },
            error: function() {
                _loading._hide();
                _toast._show("网络错误，请重试");
            }
        });
        return _flag;
    }; /**公用的异步*/
var _$ = function(url, param, fun) {
        $.ajax({
            type: "post",
            url: url,
            // timeout:2000,
            data: param,
            cache: false,
            dataType: "json",
            success: function(data) {
                if (data != null) {
                    var _state = data.state;
                    //请求正常
                    if (_state == '0') {
                        fun(data, 200);
                    }
                    //请求异常
                    else {
                        var _error = data.error;
                        _toast._show(_error);
                        fun(data, 500);
                    }
                }
            },
            error: function() {
                _loading._hide();
                url = url + ":";
                if (url.indexOf(":51:") > -1 || url.indexOf(":52:") > -1 || url.indexOf(":53:") > -1 || url.indexOf(":54:") > -1 || url.indexOf(":55:") > -1) {
                    return;
                }
                _toast._show("网络错误，请重试");
            }
        });
    };

/**本地缓存*/
var _t = {
    _set: function(key, value) {
        if (navigator.userAgent.indexOf("MSIE") > 0) { //是否是IE浏览器  ：navigator.userAgent是描述用户代理信息。ie11已经不支持了，ie11不在包含MSIE字段
        } else {
            if (window.localStorage) {
                localStorage[key] = value;
            }
        }
    },
    _get: function(key) {
        return window.localStorage ? (localStorage[key] || "") : "";
    }
};

function get36From10(num) {

    var ary = ["0", "u", "b", "2", "n", "a", "5", "7", "t", "p", "k", "e", "3", "j", "6", "o", "r", "8", "l", "m", "y", "4", "q", "c", "g", "z", "i", "1", "h", "v", "9", "d", "w", "f", "s", "x"];
    var result = "";
    while (num != 0) {
        var index = num % 36;
        result += ary[index];
        num = (num - index) / 36;
    }

    return result;
}

function get10From36(numStr) {

    numStr = String(numStr).toLowerCase();

    var str = "0ub2na57tpke3j6or8lmy4qcgzi1hv9dwfsx";

    var t = 0;
    var c = 1;
    for (var i = 0; i < numStr.length; i++) {

        var item = numStr.charAt(i);
        var itemValue = str.indexOf(item);
        t = t + c * (itemValue);
        c = c * 36;
    }

    return t;
}

/**公用*/
var _ = {
    _trim: function(text) {
        return text.replace(/(^\s*)|(\s*$)/g, "");
    },
    _len: function(text) {
        return text.replace(/[^\x00-\xff]/g, "aa").length;
    },
    _decode:function(text){
        return decodeURIComponent(text);
    },
    _encode: function(text) {
        return encodeURIComponent(text);
    },
    _htmlencode: function(text) {
        return text.replace(/\'/g, "&#39;").replace(/\"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/ /g, "&nbsp;").replace(/\n\r/g, "<br>").replace(/\r\n/g, "<br>").replace(/\n/g, "<br>");
    },
    _htmlencodeReturn: function(text) {
        return text.replace(/&#39;/g, "\'").replace(/&quot;/g, "\"").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&nbsp;/g, " ").replace(/&amp;/g, "&");
    },
    _zero: function(n) {
        return n < 0 ? 0 : n;
    },
    _scroll: function() {
        return {
            x: $(document).scrollLeft() + $(window).scrollLeft(),
            y: $(document).scrollTop() + $(window).scrollTop()
        };
    },
    _client: function() {
        return {
            w: document.documentElement.scrollWidth,
            h: document.documentElement.scrollHeight,
            bw: $(window).width(),
            bh: $(window).height()
        };
    },
    _center: function(id) {
        var _top = _._zero(_._client().bh - $("#" + id).outerHeight()) / 2;
        var _left = _._zero(_._client().bw - $("#" + id).outerWidth()) / 2;

        $("#" + id).css({
            "top": _top + "px",
            "left": _left + "px"
        });
    },
    _isHide: function(id) {
        $("#" + id).css("display") == "none";
    }
};

var _scroll = {
    _to: function(y) {
        var _clientHeight = _._client().h;
        y = _clientHeight > y ? y : _clientHeight;
        window.scrollTo(0, y);
    }
};

/**弹框*/
var _toast = {
    _center: function() {
        var _left = _._zero(_._client().bw - $("#toast").outerWidth()) / 2 + "px";
        $("#toast").css({
            "bottom": "80px",
            "left": _left
        });
    },
    _show: function(text, fun) {
        $("#toast").html(text);
        _toast._center();
        $("#toast").show();
        $("#toast").bind("resize", _toast._center);
        setTimeout(function() {
            _toast._hide(fun);
        }, 3 * 1000);
    },
    _hide: function(fun) {
        $("#toast").hide();
        $("#toast").unbind("resize");
        if (fun) {
            (fun)();
        }
    }
};

/**后面的蒙板*/
var _cover = {
    _flag: false,
    _resize: function(id) {
        var _width = (_._client().w > _._client().bw ? _._client().w : _._client().bw) + "px";
        var _height = (_._client().h > _._client().bh ? _._client().h : _._client().bh) + "px";
        $("#" + id).css({
            "width": _width,
            "height": _height
        });
    },
    _resizeAll: function() {
        if ($("#cover2")) {
            _cover._resize("cover2");
        }
        _cover._resize("cover");
    },
    _show: function(id) {
        _cover._flag = true;
        $("#" + id).show();
        if (_user._useIOs()) {
            _cover._resizeAll();
            $(window).bind("resize", "", _cover._resizeAll);
            $(window).bind("scroll", "", _cover._resizeAll);
        } else {
            $("#" + id).css({
                "position": "fixed",
                "width": "100%",
                "height": "100%"
            });
        }
    },
    _hide: function(id) {
        $("#" + id).hide();
        _cover._flag = false;
        if (($("#cover2") && !_._isHide("cover2")) || !_._isHide("cover")) {
            return;
        };
        if (!_user._useIOs()) {
            return;
        }
        $(window).unbind("resize");
        $("#" + id).unbind("click");
    }
};

/**类似alter如发布页的提示*/
var _alert = {
    _center: function() {
        _._center("alert");
    },
    _show: function(title, text, buttonText, fun, cancelText) {
        _cover._show("cover2");
        if (title != "") {
            $("#alert_title").html(title);
            $("#alert_title").show();
        } else {
            $("#alert_title").hide();
        }
        $("#alert_text").html(text);
        $("#alert_button_ok").html(buttonText);
        $("#alert_button_ok").bind("click", function() {
            _alert._hide();
            if (fun) {
                (fun)();
            }
        });
        if (cancelText) {
            $("#alert_button_ok").attr("class", "button_1");
            $("#alert_cancel").show();
            $("#alert_cancel").html(cancelText);
        }
        $("#alert").show();
        _alert._center();
        $(window).bind("resize", _alert._center);
        $("#cover").bind("click", _alert._hide);
    },
    _hide: function() {
        _cover._hide("cover2");
        $("#alert").hide();
    }
};

/**加载*/
var _loading = {
    _center: function() {
        var y = $(window).height();
        var w = $("body").width();
        $(".loadingDiv").css({
            /*"left": (w - 120) / 2 + "px",
            "top": (y - 120) / 2 + "px",*/
            "z-index": 33
        });
    },
    _show: function(text) {
        _loading._center();
        $("#cover").css({
            "background-color": "#ffffff",
            "opacity": 0
        });
        _cover._show("cover");
        $(".loadingDiv .pTxt").html(text);
        $(".loadingDiv").show();
        $(window).bind("resize", "", _loading._center);
    },
    _hide: function() {
        _cover._hide("cover");
        $(".loadingDiv").hide();
        $("#cover").css({
            "background-color": "#000000",
            "opacity": 0.7
        });
    }
};

/**登录前判断*/
var _beforeLogin = {
    _mark: function(value) {
        _t._set("before_login_" + _info._type + "_" + _info._id, value);
    },
    _continue: function(fun) {
        if (_t._get("before_login_" + _info._type + "_" + _info._id) != "") {
            (fun)();
        }
    }
};

/**登录*/
var _login = {
    _center: function() {
        _._center("login");
    },
    _show: function() {
        _tc._show('login');
        $("#cover2").unbind("click");
    },
    _hide: function() {
        _tc._hide("login");
    }
}; /**用户*/
var _user = {
    _id: function() {
        return _cookie._get("W_U_L_I");
    },
    _login: function() {
        return _user._id() != "";
    },
    _loginWithSnp: function(snp) {
        var wulbu = location.href.toString();

        if (wulbu.indexOf("/login") == -1) {
            _cookie._set("W_U_L_B_U", wulbu);
        }
        switch (snp) {
        case "weixin":
            _g(_user._inWeixin() ? _link._loginWithWeixin : _link._loginWithWeixinMp);
            break;
        case "qq":
            _g(_link._loginWithQq);
            break;
        case "weibo":
            _g(_link._loginWithWeibo);
            break;
        case 1:
            _g(_user._inWeixin() ? _link._loginWithWeixin : _link._loginWithWeixinMp);
            break;
        }
    },
    _setLogin: function(i, s) {
        _cookie._set("W_U_L_I", i, 60 * 60 * 24 * 365 * 10);
        _cookie._set("W_U_L_S", s, 60 * 60 * 24 * 365 * 10);
    },
    _error: function(state, type) {
        switch (state.toString()) {
        case "1004":
        case "1006":
        case "2310":
        case "2509":
        case "2709":
        case "2809":
        case "3014":
        case "5303":
        case "5910":
            _user._toLogin('');
            break;
        case "2304":
        case "2504":
        case "2704":
            _beforeLogin._mark("");
            break;
        case "2804":
            _option._setTemp("");
            break;
        case "5304":
            _likeBeforeLogin._mark("");
            break;
        }
    },
    _toLogin: function(id) {
        if (id == undefined) {
            id = "";
        }
        /*if (_user._inHudongba()) {
            HudongbaJsBridge["showLoginPage"]();
            return;
        }*/
        var wulbu = location.href.toString();
        //发布页链接单独保留参数，保存具体的发布类型
        if (wulbu.indexOf("/post/") > 0) {
            var postParam = wulbu.substring(wulbu.lastIndexOf("?"), wulbu.length);
            _cookie._set("postParam", postParam);
        }
        wulbu = wulbu.indexOf("#") > 0 ? wulbu.substring(0, wulbu.indexOf("#")) : wulbu;
        if (wulbu.indexOf("pay/paysuccess") == -1) {
            wulbu = wulbu.indexOf("?") > 0 ? wulbu.substring(0, wulbu.indexOf("?")) : wulbu;
        }
        if (wulbu.indexOf("/login") == -1 && wulbu.indexOf("/register") == -1) {
            _cookie._set("W_U_L_B_U", wulbu);
        } else {
            _cookie._set("W_U_L_B_U", "");
        }
        /*if (_user._inWeixin() || _user._inQq()) {
            _login._show();
            return;
        }*/
        _g(_link._loginWithWeixin + '?returnUrl=' + location.href);
    },
    _toRegister: function() {
        var wulbu = location.href.toString();
        wulbu = wulbu.indexOf("#") > 0 ? wulbu.substring(0, wulbu.indexOf("#")) : wulbu;
        wulbu = wulbu.indexOf("?") > 0 ? wulbu.substring(0, wulbu.indexOf("?")) : wulbu;

        if (wulbu.indexOf("/register") == -1 && wulbu.indexOf("/login") == -1) {
            _cookie._set("W_U_L_B_U", wulbu);
        } else {
            _cookie._set("W_U_L_B_U", "");
        }
        window.location.href = "/register";
    },
    _toLoginUrl: function(url) {
        if (_user._login()) {
            _g(url);
            return false;
        }
        _cookie._set("W_U_L_B_U", url);
        _g(_link._login);
    },
    _loginBack: function() {
        var url = _cookie._get("W_U_L_B_U");
        if (url.indexOf("/post/") > 0) {
            url = url + _cookie._get("postParam");
        }
        _g(url == "" ? _link._loginBackDefault : url);
    },
    _toLogout: function() {
        if (window.localStorage) {
            window.localStorage.clear();
        }
        _cookie._set("W_U_L_I", "");
        _cookie._set("W_U_L_S", "");
        var wulbu = location.href.toString();
        wulbu = wulbu.indexOf("#") > 0 ? wulbu.substring(0, wulbu.indexOf("#")) : wulbu;
        if (wulbu.indexOf("pay/paysuccess") == -1) {
            wulbu = wulbu.indexOf("?") > 0 ? wulbu.substring(0, wulbu.indexOf("?")) : wulbu;
        }
        _cookie._set("W_U_L_B_U", wulbu);
        //_g(_link._login);
        _g('/user/logout');
    },
    _inWeixin: function() {
        return navigator.userAgent.match(/micromessenger/i) != null;
    },
    _inQq: function() {
        return /(iPad|iPhone|iPod).*?QQ/g.test(navigator.userAgent) || /\bV1_AND_SQ_/.test(navigator.userAgent);
    },
    _inWeibo: function() {
        return navigator.userAgent.match(/weibo/i) != null;
    },
    /*_inHudongba: function() {
        return navigator.userAgent.match(/jootun\.hudongba/i) != null;
    },*/
    _inMobile: function() {
        // return appInfo.pub_system == "mb";
        return true;
    },
    _useIOs: function() {
        return navigator.userAgent.match(/ipad|iphone|ipod/i) != null;
    },
    _useAndroid: function() {
        return navigator.userAgent.match(/android/i) != null;
    },
    _isappinstalled: function() {
        return _cookie._get("IS") == "1";
    },
    _init: function() {
        if (null != $("#post_page_unionstate")) {
            if (!_user._login()) {
                $("#a_login").hide();
                $("#a_top_login").show();
                $("#a_top_register").show();
            } else {
                $("#a_top_register").hide();
                $("#a_top_login").hide();
                $("#a_login").show();
            }
        }
        if (_user._inWeixin()) {
            if (location.href.toString().match(/isappinstalled=1/i) != null) {
                _cookie._set("IS", "1", 60 * 60 * 24 * 30);
            }
            $("#login_button").html("<div class='button_5' ontouchstart='' onclick='_user._loginWithSnp(1)'><p><img width='25' height='20' src='" + _imgCdn + "/images/icon_weixin_2.png'><span>一键登录</span></p></div>");
        } else if (_user._inQq()) {
            $("#login_button").html("<div class='button_6' ontouchstart='' onclick='_g(_link._loginWithQq)'><p><img width='20' height='20' src='" + _imgCdn + "/images/icon_qq.png'><span>QQ登录</span></p></div>");
        }
        if (_user._login()) {
            $(".mem_r").show();
            $(".mem_r2").hide();
            $(".mem_l").css("margin-right", 155 + "px");
            $("#dt_review").find("div[name='discuss_icon_1']").find("a").each(function() {
                $(this).attr("href", "/timeline/" + _user._id());
            });
            $("#dt_review").find("div[name='discuss_icon_1']").show();
        } else {
            $("#dt_review").find("div[name='discuss_icon_0']").show();
            $("#a_top_login").html("登录");
            $("#a_top_register").html("注册");
            $("#a_top_user,#a_top_login_out,#loginAfter,#loginAfter_a").hide();
            $("#loginBefore_a").css({
                "display": "block"
            });
            $(".mem_l").css("margin-right", 0 + "px");
        }
        $('body>*').each(function(index, element) {
            var item = $(element);
            item.css('zIndex') > 15000 && item.remove();
        });
    }
};

/**表情图片*/
var _emo = {
    _text: ["[笑]", "[感冒]", "[流泪]", "[发怒]", "[爱慕]", "[吐舌]", "[发呆]", "[可爱]", "[调皮]", "[寒]", "[呲牙]", "[闭嘴]", "[害羞]", "[苦闷]", "[难过]", "[流汗]", "[犯困]", "[惊恐]", "[咖啡]", "[炸弹]", "[西瓜]", "[爱心]", "[心碎]"],
    _indexOf: function(text) {
        if (_emo._text.indexOf) {
            return _emo._text.indexOf(text);
        }
        for (var i = 0, _len = _emo._text.length; i < _len; i++) {
            if (_emo._text[i] == text) {
                return i;
            }
        }
        return -1;
    },
    _insertFun: null,
    _show: function(id, fun) {
        _emo._insertFun = fun;
        if ($("#" + id).children().length == 0) {
            var _html = "<ul>";
            for (var i = 0; i < 23; i++) {
                _html += "<li class='emo' ontouchstart='' onclick='_emo._insert(" + i + ")'><img src='" + _imgCdn + "/images/emo/" + (i + 1) + ".png'></li>";
            }
            _html += "</ul>";
            $("#" + id).html(_html);
        }
        $("#" + id).show();
    },
    _hide: function(id) {
        $("#" + id).hide();
    },
    _insert: function(index) {
        (_emo._insertFun)(index);
    },
    _toCode: function(content) {
        return content.replace(/\[[\u4e00-\u9fa5]{1,2}\]/g, function(a) {
            var _code = _emo._indexOf(a) + 1;
            return _code == 0 ? a : "[/" + _code + "]";
        });
    }
};


/**默认文字*/
var _placeholder = {
    _support: function() {
        return "placeholder" in document.createElement("input");
    },
    _add: function(o) {
        var _ph = o.getAttribute("placeholder") || "";
        if (_ph == "" || o.getAttribute("noplaceholder")) {
            return;
        }
        var _holder = document.createElement("div");
        _holder.className = "form_placeholder";
        _holder.innerHTML = _ph;
        o.parentNode.parentNode.insertBefore(_holder, o.parentNode);
        o.parentNode.style.marginTop = "0";
    },
    _init: function(formId) {
        if (_placeholder._support()) {
            return;
        }
        var _input = $("#" + formId + " input");
        for (var i = 0, _len = _input.length; i < _len; i++) {
            _placeholder._add(_input[i]);
        }
        var _textarea = $("#" + formId + " textarea");
        for (var i = 0, _len = _textarea.length; i < _len; i++) {
            _placeholder._add(_textarea[i]);
        }
    }
};

/**详情查看二维码*/
var _qr = {
    _id: "",
    _center: function() {
        var _top = _._zero(_._client().bh - $("#" + _qr._id).outerHeight()) / 2 + "px";
        var _left = _._zero(_._client().bw - $("#" + _qr._id).outerWidth()) / 2 + "px";
        $("#" + _qr._id).css({
            "left": _left,
            "top": _top,
            "z-index": "3000",
            "position": "fixed"
        });
    },
    _show: function(id) {
        _qr._id = id;
        if (id == "share_qr_1") {
            $("#" + _qr._id + " .tc_c_btn").show();
            $("#" + _qr._id + " .tc_c_btn_load").hide();
            if (_user._inMobile()/* && !_user._inHudongba()*/) {
                $("#" + _qr._id + " .tc_c_ts").show();
                $("#" + _qr._id + " .tc_c_btn").hide();
            }
        }
        //详情页二维码
        $("#" + _qr._id).show();
        var _url = _api3._getQr + "?info_id=" + _info._id + "&info_type=" + _info._type;
        $("#" + _qr._id + "_img").attr("src", _url);
        $("#" + _qr._id + "_img").bind("error", function() {
            if ($("#" + _qr._id).css("display") == "none") {
                return;
            }
            _qr._hide();
            _toast._show("网络错误，请稍后重试");
        });
        _cover._show("cover2");
        $("#cover2").bind("click", _qr._hide);
        _qr._center();
        $(window).bind("resize", _qr._center);
        _cover._show("cover2");
    },
    _hide: function() {
        _cover._hide("cover2");
        $("#" + _qr._id).hide();
    },
    _download: function() {
        // if (_user._inHudongba()) {
        //     HudongbaJsBridge["downQr"]("1", _api3._downloadQr + "?info_id=" + _info._id + "&info_type=" + _info._type);
        //     return;
        // }
        location.href = _api3._downloadQr + "?info_id=" + _info._id + "&info_type=" + _info._type;
    },
    _ok: function() {
        $("#" + _qr._id + " .tc_c_btn").show();
        $("#" + _qr._id + " .tc_c_btn_load").hide();
    }
};

/**报名后二维码*/
var _joinQr = {
    _id: "tc_2weima",
    _center: function() {
        var _top = _._zero(_._client().bh - $("#tc_2weima").outerHeight()) / 2 + "px";
        var _left = _._zero(_._client().bw - $("#tc_2weima").outerWidth()) / 2 + "px";
        $("#tc_2weima").css({
            "left": _left,
            "top": _top,
            "z-index": "3000",
            "position": "fixed"
        });
    },
    _show: function() {
        $("#tc_2weima .tc_c_btn").show();
        $("#tc_2weima .tc_c_btn_load").hide();
        if (_user._inMobile()/* && !_user._inHudongba()*/) {
            $("#tc_2weima .tc_c_ts").show();
            $("#tc_2weima .tc_c_btn").hide();
        }
        var _url = _api3._joinQr + "?user_id=" + _user._id() + "&info_id=" + _info._id + "&info_type=" + _info._type;
        $("#join_qr_img").attr("src", _url);
        $("#join_qr_img").bind("error", function() {
            if ($("#tc_2weima").css("display") == "none") {
                return;
            }
            _joinQr._hide();
            _toast._show("网络错误，请稍后重试");
        });
        _cover._show("cover2");
        $("#cover2").bind("click", _joinQr._hide);
        $("#p_dt_title").html($("#dt_title").html());
        _joinQr._center();
        $("#tc_2weima").show();
        $(window).bind("resize", _joinQr._center);
    },
    _hide: function() {
        _cover._hide("cover2");
        $("#tc_2weima").hide();
    },
    _download: function() {
        // if (_user._inHudongba()) {
        //     HudongbaJsBridge["downQr"]("2", _api3._downloadJoinQr + "?user_id=" + _user._id() + "&info_id=" + _info._id + "&info_type=" + _info._type);
        //     return;
        // }
        location.href = _api3._downloadJoinQr + "?user_id=" + _user._id() + "&info_id=" + _info._id + "&info_type=" + _info._type;
    },
    _ok: function() {
        $("#" + _joinQr._id + " .tc_c_btn").show();
        $("#" + _joinQr._id + " .tc_c_btn_load").hide();
    }
};

var _shareInWeixin = {
    _after: null,
    _show: function(fun) {
        _cover._show("cover2");
        $("#share_weixin").show();
        $("#cover2").bind("click", _shareInWeixin._hide);
        _shareInWeixin._after = fun || null;
    },
    _hide: function() {
        _cover._hide("cover2");
        $("#share_weixin").hide();
        if (_shareInWeixin._after) {
            (_shareInWeixin._after)();
        }
    },
    _hideFromJsBridge: function() {
        if ($("#cover2").attr("id") != undefined && $("#share_weixin").attr("id") != undefined) {
            _shareInWeixin._hide();
        }
    }
};
var _shareInQq = {
    _after: null,
    _timer: null,
    _show: function(fun) {
        _cover._show("cover2");
        $("#share_qq").show();
        $("#cover2").bind("click", _shareInQq._hide);
        _shareInQq._timer = setTimeout(_shareInQq._hide, 8 * 1000);
        _shareInQq._after = fun || null;
    },
    _hide: function() {
        _cover._hide("cover2");
        $("#share_qq").hide();
        clearTimeout(_shareInQq._timer);
        if (_shareInQq._after) {
            (_shareInQq._after)();
        }
    }
};

/**下载*/
var _download = function(link) {
        if (_user._useAndroid() && _user._inWeixin()) {
            setTimeout(function() {
                _g(_link._downloadGuide);
            }, 500);
        }
        _g(link);
    };

/**成功*/
var _sucess = {
    _show: function(text, fun) {
        _cover._show("cover");
        $("#sucess_text").html(text);
        $("#tc_bmSuccess").show();
        setTimeout(function() {
            _sucess._hide();
            if (fun) {
                (fun)();
            }
        }, 2 * 1000);
    },
    _hide: function() {
        _cover._hide("cover");
        $("#tc_bmSuccess").hide();
    }
};

/**展开更多*/
var _loadingBottom = {
    _init: function(text, fun, id) {
        $("#" + id).html("<a>" + text + "<img src=\"http://img1.hudongba.com/images3/loading_bottom.png\"></a>");
        $("#" + id).bind("click", fun);
        $("#" + id).show();
    },
    _loading: function(id) {
        $("#" + id).html("<p class='jz_More'><img src='http://img1.hudongba.com/static_v4/images/other/loading2.gif'><span>加载中…</span></p>");
        $("#" + id).unbind("click");
        $("#" + id).show();
    },
    _hide: function(id) {
        $("#" + id).hide();
    },
    _noMore: function(text, id) {
        $("#" + id).html("<a>" + text + "</a>");
        $("#" + id).attr("onclick", "");
        $("#" + id).show();
    },
    _initTime: function(text, fun, id) {
        $("#" + id).html(text);
        $("#" + id).bind("click", fun);
        $("#" + id).show();
    }
};

var _backToRefresh = {
    _mark: function() {
        _t._set("join_sucess", "/" + _info._type + "/" + _info._id);
    },
    _should: function() {
        return _t._get("join_sucess") != "" && document.referrer.toString().indexOf(_t._get("join_sucess")) != -1;
    },
    _clear: function() {
        _t._set("join_sucess", "");
    }
}; /**发布*/

function _post_alert() {
    if (!_user._login() && !window.localStorage) {
        _user._toLogin('');
        return;
    }
}

/**发布页的活动多*/
var _followMp = function() {
        if (_user._inWeixin()) {
            _g(_link._followMp);
        } else {
            _hdbQr._show();
        }
    }; /*公众号二维码*/
var _hdbQr = {
    _center: function() {
        var _top = _._zero(_._client().bh - $("#indexTc").outerHeight()) / 2 + "px";
        var _left = _._zero(_._client().bw - $("#indexTc").outerWidth()) / 2 + "px";
        $("#indexTc").css({
            "left": _left,
            "top": _top,
            "z-index": "3000",
            "position": "fixed"
        });
    },
    _show: function() {
        _cover._show("cover2");
        _hdbQr._center();
        _cover._show("cover2");
        $("#indexTc .guanbiQu a").bind("click", _hdbQr._hide);
        $("#indexTc").show();
        $(window).bind("resize", _hdbQr._center);
        $("#cover2").bind("click", _hdbQr._hide);
    },
    _hide: function() {
        $("#indexTc").hide();
        _cover._hide("cover2");
    },
    _tab: function(x, y) {
        $("#" + x).addClass("thisOver").siblings().removeClass("thisOver");
        $("#" + y).show().siblings().hide();
    }
};


/** 给 app 分享数据*/
var _setData = function() {
        var _invite = "false",
            _infoId = "",
            _infoType = "";
        var pageTitle = $("#div_topbar_title").html();
        if (typeof _info != "undefined" && typeof _info._id != "undefined" && typeof _info._type != "undefined") {
            _infoId = _info._id;
            _infoType = _info._type;
            if ((_info._type == "party" || _info._type == "recruit") && _user._login() && _user._id() == _info._postUid) {
                _invite = "true";
            }
        }
        //区分安卓和ios
        // if (_user._inHudongba() && _user._useAndroid()) {
        //     HudongbaJsBridge["setData"](pageTitle, dataForShare.weixin_icon, dataForShare.weixin_tl_icon, dataForShare.weixin_url, dataForShare.qq_icon, dataForShare.weibo_icon, dataForShare.url, dataForShare.title, dataForShare.description, dataForShare.sms, _invite, _infoId, _infoType);
        // } else {
        //     HudongbaJsBridge["setData"](pageTitle, dataForShare.weixin_icon, dataForShare.weixin_tl_icon, dataForShare.weixin_url, dataForShare.qq_icon, dataForShare.weibo_icon, dataForShare.url, dataForShare.title, dataForShare.description, dataForShare.sms, _invite, _infoId, _infoType);
        // }
    };

/** 给 app 经纬度地址数据 */
var _goMap = function(lon, lat, desc) {
        HudongbaJsBridge["goMap"](lon, lat, desc);
    };

/**是否显示关闭按钮*/
var _setConfig = function() {
        HudongbaJsBridge["setConfig"](_config._showBtn);
    };

/*base64编码*/
window.encode = {
    base64Key: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_=",
    utf8Encode: function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    },
    utf8Decode: function(utftext) {
        var string = "",
            i = 0,
            c = 0,
            c2 = 0,
            c3 = 0;
        while (i < utftext.length) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    },
    base64Encode: function(input) {
        var keyStr = this.base64Key,
            output = "",
            chr1, chr2, chr3, enc1, enc2, enc3, enc4, i = 0;
        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output + keyStr.charAt(enc1) + keyStr.charAt(enc2) + keyStr.charAt(enc3) + keyStr.charAt(enc4);
        }
        return output;
    },
    base64Decode: function(input) {
        var keyStr = this.base64Key,
            output = "",
            chr1, chr2, chr3, enc1, enc2, enc3, enc4, i = 0;
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length) {
            enc1 = keyStr.indexOf(input.charAt(i++));
            enc2 = keyStr.indexOf(input.charAt(i++));
            enc3 = keyStr.indexOf(input.charAt(i++));
            enc4 = keyStr.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 !== 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 !== 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = window.utf8Decode(output);
        return output;
    },
    _Str2Hex: function(text) {
        var _c = "";
        var _n;
        var _s = "0123456789ABCDEF";
        var _digS = "";
        for (var i = 0, _len = text.length; i < _len; i++) {
            _c = text.charAt(i);
            _n = _s.indexOf(_c);
            _digS += this._Dec2Dig(eval(_n));
        }
        return _digS;
    },
    _Dec2Dig: function(n1) {
        var _s = "";
        var _n2 = 0;
        for (var i = 0; i < 4; i++) {
            _n2 = Math.pow(2, 3 - i);
            if (n1 >= _n2) {
                _s += "1";
                n1 = n1 - _n2;
            } else {
                _s += "0";
            }
        }
        return _s;
    },
    _Dig2Dec: function(s) {
        var _retV = 0;
        if (s.length == 4) {
            for (var i = 0; i < 4; i++) {
                _retV += eval(s.charAt(i)) * Math.pow(2, 3 - i);
            }
            return _retV;
        }
        return -1;
    },
    _Hex2Utf8: function(s) {
        var _retS = "";
        var _tempS = "";
        var _s = "";
        if (s.length == 16) {
            _tempS = "1110" + s.substring(0, 4);
            _tempS += "10" + s.substring(4, 10);
            _tempS += "10" + s.substring(10, 16);
            var __s = "0123456789ABCDEF";
            for (var i = 0; i < 3; i++) {
                _retS += "%";
                _s = _tempS.substring(i * 8, (eval(i) + 1) * 8);
                _retS += __s.charAt(this._Dig2Dec(_s.substring(0, 4)));
                _retS += __s.charAt(this._Dig2Dec(_s.substring(4, 8)));
            }
            return _retS;
        }
        return "";
    },
    _deCode: function(text) {
        text = text.replace(/\·/g, " ");
        //if(text != null){text = text.replace(/\·/g, " ") ;}else{ return false;}//如果text为undefined，replace会出错
        var _text = escape(text);
        var _t = _text.split("%");
        var _v = "";
        if (_t[0] != "") {
            _v = _t[0];
        }
        for (var i = 1, _len = _t.length; i < _len; i++) {
            if (_t[i].substring(0, 1) == "u") {
                _v += this._Hex2Utf8(this._Str2Hex(_t[i].substring(1, 5)));
                if (_t[i].length >= 6) {
                    _v += _t[i].substring(5);
                }
            } else {
                _v += "%" + _t[i];
            }
        }
        return _v;
    }
}; /*登录弹窗*/
var _login4 = {
    _id: "",
    _Uid: "",
    _cha: "",
    _post: function() {
        if (_t._get("partyJoinlogin") == 9) {
            _t._set("partyJoinlogin", 10);
        }
        var _account = _._trim($("#lg_form_account").val()),
            _password = _._trim($("#lg_form_password").val());
        if (_account == "") {
            _toast._show("请输入邮箱或手机号");
            return;
        }
        if (!_account.match(/^[\w\.\-]+@[\w\.\-]+\.[a-zA-Z]+$/) && !_account.match(/^1\d{10}$/)) {
            _toast._show("请输入正确的邮箱或手机号");
            return;
        }
        if (_._len(_account) > 100) {
            _toast._show("邮箱超出了长度限制");
            return;
        }
        if (_password == "") {
            _toast._show("请输入密码");
            return;
        }
        if (_._len(_password) > 16 || _._len(_password) < 6) {
            _toast._show("密码请输入6-16个字符");
            return;
        }
        _loading._show("请稍候");
        _$(_api3._login, "login_name=" + _account + "&login_password=" + _._encode(_password), _login4._ok);
    },
    _ok: function(json, code) {
        _loading._hide();
        if (code != 200) {
            return;
        }
        $("#lg_form_account").val("");
        $("#lg_form_password").val("");
        $(".mem_r,#a_top_user,#a_top_login_out").show();
        $(".mem_r2").hide();
        $(".mem_l").css("margin-right", 155 + "px");
        var name = json.login_user_name;
        var pic = json.login_user_pic;
        $("#a_top_user").html(name);
        $("#userPic").attr("src", pic);
        var way = _t._get('login_Way');
        if (way == 1) {
            _like._post(); //赞
        } else if (way == 2) {
            _review._post(); //评论
        } else if (way == 3) {
            _review._post2(); //回复
        } else if (way == 4) {
            _articlePostPC._post(_t._get("login_Uid"), _t._get("login_Cha")); //发布文章
        } else if (way == 5) {
            _partyPostPC._post(_t._get("login_Uid"), _t._get("login_Cha")); //发布活动
        } else if (way == 6) {
            _votePostPC._post(_t._get("login_Uid"), _t._get("login_Cha")); //发布投票
        } else if (way == 7) {
            _payItemBox._show(_info._type); // 我要报名
        } else if (way == 8) {
            _votePostPC._post(); //我要投票
        }
        _t._set("login_Way", 0);
        if (_t._get("partyJoinlogin") == 10) {
            _t._set("partyJoinlogin", "");
            _joinForm._post(_info._type);
        }
        _login4._hide('tc_login');
    },
    _hdb: function() {
        $(".login_HDB,.login_icon_Wx").show();
        $(".login_Weixin,.login_icon_Hd").hide();
    },
    _wx: function() {
        $(".login_Weixin,.login_icon_Hd").show();
        $(".login_HDB,.login_icon_Wx").hide();
        //加载二维码 插入数据
        _login4._getWxLoginQr();
    },
    _getWxLoginQr: function() {
        _$(_api3._getWeiXinQr, "", function(json, code) {
            var _state = json.state;
            if (code != 200 || (code == 200 && _state != "0")) {
                _toast._show("加载微信二维码异常");
                return;
            }
            var sign = json.sign;
            _sid = sign;
            $("#lm_qr_qr_pic").attr("src", "/get/api:qr2?sid=" + _sid).attr("style", "");
        });
    },
    _center: function() {
        var _top = _._zero(_._client().bh - $("#" + _login4._id).outerHeight()) / 2 + "px";
        var _left = _._zero(_._client().bw - $("#" + _login4._id).outerWidth()) / 2 + "px";
        $("#" + _login4._id).css({
            "left": _left,
            "top": _top,
            "z-index": "3000",
            "position": "fixed"
        });
    },
    _show: function(id, Uid, cha) {
        _login4._id = id;
        _login4._Uid = Uid;
        _login4._cha = cha;
        _t._set("login_Uid", _login4._Uid);
        _t._set("login_Cha", _login4._cha);
        $("#" + _login4._id).show();
        _cover._show("cover2");
        $("#cover2").bind("click", _login4._hide);
        _login4._center();
        $(window).bind("resize", _login4._center);
        _cover._show("cover2");
    },
    _hide: function() {
        _cover._hide("cover2");
        $("#" + _login4._id).hide();
        _t._set("partyJoinlogin", "");
    }
}; /*公共弹窗*/
var _tc = {
    _id: "",
    _center: function() {
        var _top = _._zero(_._client().bh - $("#" + _tc._id).outerHeight()) / 2 + "px";
        var _left = _._zero(_._client().bw - $("#" + _tc._id).outerWidth()) / 2 + "px";
        $("#" + _tc._id).css({
            "left": _left,
            "top": _top,
            "z-index": "3000",
            "position": "fixed"
        });
    },
    _show: function(id) {
        _tc._id = id;
        $("#" + _tc._id).show();
        _cover._show("cover2");
        $("#cover2").bind("click", _tc._hide);
        _tc._center();
        $(window).bind("resize", _tc._center);
        _cover._show("cover2");
    },
    _hide: function() {
        _cover._hide("cover2");
        $("#" + _tc._id).hide();
    }
};

/**通过扫一扫登录*/
var _loginWithQr = {
    _post: function() {
        _loading._show("请稍候");
        _$(_api3._loginWithQr, "sid=" + _sid, _loginWithQr._ok);
    },
    _ok: function(json, code) {
        _loading._hide();
        if (code != 200) {
            return;
        }
        _login4._hide('tc_login');
        $(".mem_r,#a_top_user,#a_top_login_out").show();
        $(".mem_r2").hide();
        $(".mem_l").css("margin-right", 155 + "px");
        var name = json.login_user_name;
        var pic = json.login_user_pic;
        $("#a_top_user").html(name);
        $("#userPic").attr("src", pic);
        var way = _t._get('login_Way');
        if (way == 1) {
            _like._post(); //赞
        } else if (way == 2) {
            _review._post(); //评论
        } else if (way == 3) {
            _review._post2(); //回复
        } else if (way == 4) {
            _article._post(_t._get("login_Uid"), _t._get("login_Cha")); //发布文章
        } else if (way == 5) {
            _party._post(_t._get("login_Uid"), _t._get("login_Cha")); //发布活动
        } else if (way == 6) {
            _vote._post(_t._get("login_Uid"), _t._get("login_Cha")); //发布投票
        } else if (way == 7) {
            _payItemBox._show('party'); //我要报名
        } else if (way == 8) {
            _vote._post(); //我要投票
        }
    }
};

var _share_hdb = {
    _shareqzone: function() {
        var p = {
            url: dataForShare.url,
            desc: dataForShare.description,
            summary: "",
            title: dataForShare.title,
            site: _domain,
            pics: dataForShare.share_big_img == "" ? dataForShare.qq_icon : "http://img1.hudongba.com" + dataForShare.share_big_img,
            style: '201',
            width: 25,
            height: 25
        };
        var s = [];
        for (var i in p) {
            s.push(i + '=' + encodeURIComponent(p[i] || ''));
        }
        var url = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?" + s.join('&');
        window.open(url);
        return false;
    },
    _sharesina: function() {
        var param = {
            url: dataForShare.url,
            title: dataForShare.title,
            searchPic: true,
            pic: dataForShare.share_big_img == "" ? dataForShare.weibo_icon : "http://img1.hudongba.com" + dataForShare.share_big_img,
            language: 'zh_cn'
        };
        var temp = "http://service.weibo.com/share/share.php?";
        for (var p in param) {
            temp += (p + '=' + encodeURIComponent(param[p] || '') + "&");
        }
        window.open(temp);
        return false;
    }
}; /**懒加载插件*/
var _hdbLoad = {
    _timer: 0,
    _windowOffset: 0,
    _windowSize: 0,
    _lazys: [],
    _imgs: [],
    _getWindowOffset: function() {
        var offset = {
            x: 0,
            y: 0
        };
        if (typeof window.pageXOffset != 'undefined' || typeof window.pageYOffset != 'undefined') {
            offset.x = window.pageXOffset;
            offset.y = window.pageYOffset;
        } else if (typeof document.compatMode != 'undefined' && document.compatMode == 'CSS1Compat') {
            offset.x = document.documentElement.scrollLeft;
            offset.y = document.documentElement.scrollTop;
        } else if (typeof document.body != 'undefined' && (document.body.scrollLeft || document.body.scrollTop)) {
            offset.x = document.body.scrollLeft;
            offset.y = document.body.scrollTop;
        }
        return offset;
    },
    _getObjOffset: function(element) {
        var objOffset = {
            x: 0,
            y: 0
        };
        if (arguments.length != 1 || element == null) {
            return null;
        }
        objOffset.x = element.offsetLeft;
        objOffset.y = element.offsetTop;
        while (element = element.offsetParent) {
            objOffset.x += element.offsetLeft;
            objOffset.y += element.offsetTop;
        }
        return objOffset;
    },
    // 3. 获取视窗大小
    _getWindowSize: function() {
        var client = {
            x: 0,
            y: 0
        };
        if (typeof document.compatMode != 'undefined' && document.compatMode == 'CSS1Compat') {
            client.x = document.documentElement.clientWidth;
            client.y = document.documentElement.clientHeight;
        } else if (typeof document.body != 'undefined' && (document.body.scrollLeft || document.body.scrollTop)) {
            client.x = document.body.clientWidth;
            client.y = document.body.clientHeight;
        }
        return client;
    },
    _getObjSize: function(element) {
        var objSize = {
            x: 0,
            y: 0
        };
        if (arguments.length != 1 || element == null) {
            return null;
        }
        objSize.x = element.offsetWidth;
        objSize.y = element.offsetHeight;
        return objSize;
    },
    _getLazys: function() {
        $(_hdbLoad._id).each(function() {
            var _tmp = new Object;
            _tmp.element = this;
            // 获取图片的大小
            _tmp.w = _hdbLoad._getObjSize(this).x;
            _tmp.h = _hdbLoad._getObjSize(this).y;
            _tmp.datasrc = $(this).attr("data-src");
            _hdbLoad._lazys.push(_tmp);
            _hdbLoad._imgs.push($(this).attr("data-src"));
        });
        if (_user._inMobile()) {
            $(_hdbLoad._id).each(function(i) {
                if ($(this).parent().parent().attr("class") != 'act_topic_t_r_A' && $(this).parent().parent().parent().attr("class") != 'act_topic_b_A') {
                    $(this).bind("click", function() {
                        if (_info._type != "index" && _info._type != "find") {
                            var srcTemp = _hdbLoad._imgs[i];
                            _hdbLoad._bind(srcTemp);
                        }
                    });
                }
            });
        }
    },
    _loadimg: function() {
        if (typeof _hdbLoad._lazys == 'undefined') return;
        for (var i = 0; i < _hdbLoad._lazys.length; i++) {
            var tmp = _hdbLoad._lazys[i];
            // 获取相对页面的位置
            tmp.x = _hdbLoad._getObjOffset(tmp.element).x;
            tmp.y = _hdbLoad._getObjOffset(tmp.element).y;
            if (_hdbLoad._windowOffset.x < 0) {
                if (tmp.element.getAttribute("data-src") != null) {
                    _hdbLoad._initSize($(tmp.element));
                    $(tmp.element).hide();
                    tmp.element.src = _hdbLoad._lazys[i].datasrc;
                    $(tmp.element).fadeIn(500);
                    tmp.element.removeAttribute("data-src");
                    _hdbLoad._lazys.splice(i, 1);
                    i--;
                }
            }
            if (tmp.x + tmp.w > _hdbLoad._windowOffset.x & tmp.x < _hdbLoad._windowOffset.x + _hdbLoad._windowSize.x && tmp.y + tmp.h + 200 > _hdbLoad._windowOffset.y && tmp.y < _hdbLoad._windowOffset.y + _hdbLoad._windowSize.y + 200) {
                if (tmp.element.getAttribute("data-src") != null) {
                    _hdbLoad._initSize($(tmp.element));
                    $(tmp.element).hide();
                    tmp.element.src = _hdbLoad._lazys[i].datasrc;
                    $(tmp.element).fadeIn(500);
                    tmp.element.removeAttribute("data-src");
                    _hdbLoad._lazys.splice(i, 1);
                    i--;
                }
            }
        }
    },
    _bind: function(srcTemp) {
        // if (_user._inHudongba()) {
        //     HudongbaJsBridge["showPicPreview"](srcTemp, _hdbLoad._imgs.join("|"));
        //     return;
        // }
        if (typeof window.WeixinJSBridge != 'undefined') {
            WeixinJSBridge.invoke('imagePreview', {
                'current': srcTemp,
                'urls': _hdbLoad._imgs
            });
        }
    },
    _getWidth: function(s) {
        with(new Image()) {
            src = s;
            return parseInt(width);
        }
    },
    _initSize: function(self) {
        var _parentWidth = self.parent().width();
        var _imgWidth = _hdbLoad._getWidth(self.attr("data-src"));
        if (_imgWidth > _parentWidth) {
            _imgWidth = _parentWidth;
        }
        if (_imgWidth > 0) {
            self.css({
                "width": _imgWidth + "px"
            });
        }
    },
    _init: function() {
        _hdbLoad._getLazys();
        _hdbLoad._lazyLoad();
    },
    // 执行图片加载
    _lazyLoad: function() {
        _hdbLoad._windowOffset = _hdbLoad._getWindowOffset();
        _hdbLoad._windowSize = _hdbLoad._getWindowSize();
        _hdbLoad._loadimg();
        if (_hdbLoad._lazys.length == 0) {
            clearTimeout(_hdbLoad._timer);
        }
    },
    _run: function(id) {
        _hdbLoad._id = id;
        _hdbLoad._init();
        _hdbLoad._timer = setInterval(_hdbLoad._lazyLoad, 200);
    }
};

var _coverUp = {
    _wait: 30,
    _init: function() {
        if (_coverUp._wait != 0) {
            _coverUp._wait = _coverUp._wait - 1;
            setTimeout(function() {
                _coverUp._init();
                hdsScroll.refresh();
            }, 1000);
        }
    }
};

/**
 * 点击加群后 给腾讯发送通知 用于统计活动多端加群的数据
 * type 1 pc  2 wap
 */

function sendJoinGroupNotice(type) {
    var sendUrl = "";
    if (type == 1) {
        //pc端通知cgi
        sendUrl = 'http://cgi.connect.qq.com/report/tdw/report?table=dc00141&fields=["opername","module","uin","action","obj1","obj2","ver1","ver2","ver4"]&datas=[["Grp_ac","open",,"click_wpa",0,"PC_WEB_1.0","","",""]]&pr_ip=obj3&t=' + (+new Date());
    } else if (type == 2) {
        //wap端通知cgi
        sendUrl = 'http://cgi.connect.qq.com/report/tdw/report?table=dc00141&fields=["opername","module","action"]&datas=[["Grp_ac_mobile","hudongba","click_wpa"]]&pr_ip=obj3&pr_t=ts&t=' + (+new Date());
    }
    var a = new Image();
    a.src = sendUrl;
}

var _guide_DownloadClose = {
    _init: function() {
        $("#guide_Download").remove();
        $(".outSide_down").css("padding-bottom", 20 + "px");
        $(".recommend_content").css("padding-bottom", 10 + "px");
        $("#post_page_unionstate,.index_re_More,.zt_outSide").css("padding-bottom", 0 + "px");
    }
}

/**公共*/
$(document).ready(function() {
    _user._init();

    //有footer_page_type参数 说明引用了 public <#--21.PC端公共灰色底部&登录弹窗--> 需要初始化底部栏数据：活动多简介、友情链接、热门城市、友情链接
    if (typeof(footer_page_type) != "undefined") {
        getFooterInfo(footer_page_type);
    }

    //bi统计
    // var Biurl = _domain + "hdb-bi?url=" + document.URL + "&t=" + +new Date() + "&refer=" + document.referrer;
    // var a = new Image();
    // a.src = Biurl;


    $(".header_public_search a").on("click", function() {
        doSearch($("#pub_search").val());
    });

    $('#pub_search').bind("keydown", function(e) {

        var curKey = e.which;

        if (curKey == 13) {
            doSearch($(this).val());
        }

    });
});

var addShopHits = function() {

        _$(_api3._shopHits, "shop_id=" + _info._shopId, function() {});

    };

//异步ajax初始化底部栏数据：活动多简介、友情链接、热门城市、友情链接

function getFooterInfo(page_type) {
    _$(_api3._footerInfo, "page_type=" + page_type, getFooterInfoOk);
}

function getFooterInfoOk(obj) {

    //活动多简介
    if (obj.hdb_desc) {
        var html = "<div class=\"footer_gray_li jj\"><span>活动多简介：</span><p>" + obj.hdb_desc + "</p></div>"
        $("#footer_info").append(html);
    }

    //活动多类目
    if (obj.categorylist) {
        var html = "";

        html += "<div class=\"footer_gray_li lm\">";
        html += "<span>活动多类目：</span>";
        html += "<p>";

        //循环输出每个类目
        for (var i = 0; i < obj.categorylist.length; i++) {
            var category = obj.categorylist[i];

            if (category.category_id != -1) {
                html += ("<a target=\"_blank\" href=\"" + _domain + "find/" + obj.area_code + "-fl" + category.category_id + "-sjbx-p1/\">" + category.category_name + "活动</a>");
            }
        }

        html += "</p>";
        html += "</div>";

        $("#footer_info").append(html);
    }

    //热门城市
    if (obj.area_list) {

        var html = "";

        html += "<div class=\"footer_gray_li rm\">";
        html += "<span>热门城市：</span>";
        html += "<p id=\"hotArea\">";

        for (var i = 0; i < obj.area_list.length; i++) {

            var area = obj.area_list[i];

            html += "<a id=\"" + area.area_id + "\" name=\"" + area.area_code + "\" href=\"" + _domain + area.area_code + "/\">" + area.area_name + "活动</a>";

        }

        html += "</p>";
        html += "</div>";

        $("#footer_info").append(html);

    }

    //友情链接
    if (obj.linkList) {

        var html = "";

        html += "<div class=\"footer_gray_li rm\">";
        html += "<span>友情链接：</span>";
        html += "<p>"

        for (var i = 0; i < obj.linkList.length; i++) {
            var link = obj.linkList[i];

            if (link.link_url.indexOf("http://") == -1) {
                link.link_url = "http://" + link.link_url;
            }

            html += "<a target='_blank' href='" + link.link_url + "'>" + link.link_title + "</a>";
        }

        html += "</p>"
        html += "</div>"

        $("#footer_info").append(html);

    }

}
var _downloadObj = {
    _android: function() {
        var _agent = navigator.userAgent;
        if (_agent.match(/micromessenger/i) != null && _agent.match(/android/i) != null) {
            setTimeout(function() {
                location.href = "/download_guide";
            }, 500);
        }
        location.href = "/download/android/5.3/hudongba.apk?from=website";
    },
    _iPhone: function() {
        location.href = "/download/iphone/5.3?from=website";
    }
};

$(".header_public_search input").focus(function() {
    $(this).next('a').attr("class", "thisOver");
});
$(".header_public_search input").blur(function() {
    $(this).next('a').attr("class", "");
});

/**
 * 搜索活动
 * @param searchWord
 */

function doSearch(searchWord) {

    if (searchWord == "") {
        _toast._show("请输入搜索内容");
        return;
    }

    location.href = "/info_search?word=" + _._encode(searchWord);

};

/**
 * 创建微信扫码支付
 * @param orderNum 订单号
 * @param 拿到支付二维码后 回调的function
 */

function _createWechatScanPay(orderNum, callback) {

    _$(_api3._createPrepayOrder, "order_num=" + orderNum + "&trade_type=NATIVE", function(json) {

        var qr_url = json.qr_url;
        callback(qr_url);

    })

}


function ih(e){
    var $e=$(e);
    if(0==$e.attr('changewidth')||!$e.attr('changewidth')){
        $e.attr('changewidth',1);
        e.width=$(e).parent().width()*0.9;
        e.height=e.width*9/16;
        var src=e.src;
        var id=src.match(/vid=([^&]*)/)[1];
        e.src='http://v.qq.com/iframe/player.html?vid=' + id + '&width=' + e.width + '&height=' + e.height + '&auto=0';
    }
}

