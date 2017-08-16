/**
 * 是否正在支付
 * @type {boolean}
 */
var paying = false;

/**
 * 微信支付入口
 * @param orderId
 * @param successCallback
 * @param failCallback
 */
function payOrder(orderId, successCallback, failCallback) {
    /**
     * 微信支付
     * @param param
     */
    function wxPay(param) {
        /**
         * 调用微信JS api 支付
         * @param param
         */
        function jsApiCall(param) {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                param,
                function(res){
                    WeixinJSBridge.log(res.err_msg);
                    if (res.err_msg == 'get_brand_wcpay_request:ok') {
                        paySucceed();
                    }
                }
            );
        }

        if (typeof WeixinJSBridge == "undefined") {
            if(document.addEventListener){
                document.addEventListener('WeixinJSBridgeReady', function() {
                    jsApiCall(param);
                }, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', function() {
                    jsApiCall(param);
                });
                document.attachEvent('onWeixinJSBridgeReady', function() {
                    jsApiCall(param);
                });
            }
        } else {
            jsApiCall(param);
        }
    }

    function paySucceed() {
        var url = '/wechat/order/check-pay';
        $.post(url, {'order_id': orderId})
            .done(function(data) {
                if (data['status'] == 2) {
                    successCallback();
                } else {
                    failCallback('尚未收到微信付款成功通知，请稍后刷新查看付款状态');
                }
            })
            .fail(function() {
                failCallback('获取支付状态出错，请刷新重试');
            });
    }

    /**
     * 微信预支付
     * @param orderId
     */
    function prepay(orderId) {
        if (paying) {
            return;
        }
        paying = true;
        var url = '/wechat/order/prepay';
        $.post(url, {'order_id': orderId})
            .done(function (data) {
                var status = data['status'];
                if (status == 'COMPLETE') {
                    successCallback();
                } else if (status == 'PREPAID') {
                    wxPay(data['param']);
                } else if (status == 'PREPAY_FAIL') {
                    failCallback('微信预支付失败，请稍后再试')
                } else {
                    failCallback('未知支付状态');
                }
            })
            .fail(function () {
                failCallback('支付系统出错，请稍候重试');
            })
            .always(function() {
                paying = false;
            });
    }

    prepay(orderId);
}

/**
 * 倒计时
 * @param total 开始剩余秒数
 * @param callback 每秒回调
 * @param done 计时结束回调
 */
function doCountdown(total, callback, done) {
    var tick = total;
    function doTask() {
        if (tick > 0) {
            tick -= 1;
            callback(tick);
            setTimeout(doTask, 1000);
        } else {
            done();
        }
    }

    setTimeout(doTask, 1000);
}