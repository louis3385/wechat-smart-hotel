/**
 * Created by DongpopoLiu on 4/27/16.
 */

$(function() {
    /*function openLocation(mapJing,mapWei,mapName) {
        wx.openLocation({
            latitude: mapWei,
            longitude: mapJing,
            name: '地点:',
            address: mapName,
            scale: 15,
            infoUrl: ''
        });
    }*/
    wx.ready(function() {
        var $body = $('body');
        document.title = 'WeChat Smart Hotel';
       /* var $iframe = $('<iframe src="/favicon.ico"></iframe>');
        $iframe.on('load',function() {
            setTimeout(function() {
                $iframe.off('load').remove();
            }, 0);
        }).appendTo($body);*/


        wx.onMenuShareAppMessage({
            title: dataForShare.title,
            desc: dataForShare.description,
            link: dataForShare.weixin_url,
            imgUrl: dataForShare.weixin_icon,
            trigger: function (res) {_shareInWeixin._hideFromJsBridge();/*alert('用户点击发送给朋友');*/},
            success: function (res) {
                (dataForShare.callback)();
            },
            cancel: function (res) {/*alert('已取消');*/},
            fail: function (res) {
            }
        });
        wx.onMenuShareTimeline({
            title: dataForShare.title,
            link: dataForShare.weixin_url,
            imgUrl: dataForShare.weixin_tl_icon,
            trigger: function (res) {_shareInWeixin._hideFromJsBridge();/*alert('用户点击分享到朋友圈');*/},
            success: function (res) {
                (dataForShare.callback)();
            },
            cancel: function (res) {/*alert('已取消');*/},
            fail: function (res) {
            }
        });
        wx.onMenuShareQQ({
            title: dataForShare.title,
            desc: dataForShare.description,
            link: dataForShare.weixin_url,
            imgUrl: dataForShare.weixin_tl_icon,
            trigger: function (res) {_shareInWeixin._hideFromJsBridge();},
            complete: function (res) {
            },
            success: function (res) {(dataForShare.callback)();},
            cancel: function (res) {/*alert('已取消');*/},
            fail: function (res) {
            }
        });
        wx.onMenuShareWeibo({
            title: dataForShare.title,
            desc: dataForShare.description,
            link: dataForShare.weixin_url,
            imgUrl: dataForShare.weixin_icon,
            trigger: function (res) {_shareInWeixin._hideFromJsBridge();/*alert('用户点击分享到微博');*/},
            complete: function (res) {
            },
            success: function (res) {(dataForShare.callback)();},
            cancel: function (res) {/*alert('已取消');*/},
            fail: function (res) {
            }
        });
    });
});