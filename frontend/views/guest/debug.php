<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 1/13/17
 * Time: 2:41 PM
 */
use common\modules\wechat\models\WxPlatform;
use common\modules\wechat\components\WeixinJSAPI;
use yii\helpers\Html;
$platform = WxPlatform::getFromHost();
$this->registerJs(WeixinJSAPI::getPlatformConfigJS([
    'onMenuShareTimeline',
    'onMenuShareAppMessage',
    'onMenuShareQQ',
    'onMenuShareWeibo',
    'onMenuShareQZone',
    // 所有要调用的 API 都要加到这个列表中//要调用的js函数，必须把函数名字写入数组
    'openWXDeviceLib',
    'getWXDeviceInfos',
    'startScanWXDevice',
    'connectWXDevice',
    'sendDataToWXDevice',
    'getWXDeviceTicket'
]));
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <title>AirSync Debug</title>
    <script>
        var _pub_client = "mobile_ios";
        var _imgCdn="<?= \Yii::$app->request->hostInfo ?>";
        var _domain="<?= \Yii::$app->request->hostInfo ?>/";
        var _fmShezhi;
        var dataForShare={
            weixin_icon: '' ,
            weixin_tl_icon:'' ,
            weixin_url: _domain,
            qq_icon: _imgCdn + "/images/share/other_qq_3.png",
            weibo_icon: _imgCdn + "/images/share/other_weibo_3.png",
            url: _domain,
            title: '' ,
            description: '' ,
            sms:''  ,
            appId: "<?= $platform->appId ?>",
            callback: function() {
                _$(_api3._shareCount, "info_id=0&info_type=other", function() {});
            }
        };
    </script>
</head>


<body>
<?php $this->beginBody() ?>
    <noscript>
        <div id="noscript">当前的浏览器不支持JavaScript脚本</div>
    </noscript>

<div id="debug-box" class="box wordsSelectNone disNone">
    <div class="top">
        <div class="receive">
            <div class="title">接收到的数据</div>
            <div id="receivedata" class="content"></div>
        </div>
    </div>

    <div class="bottom">
        <div class="input"><input id="sendinput" type="text" placeholder="请输入要发送的数据" /></div>
        <div id="send" class="btn">发送数据</div>
    </div>

</div>

<div id="alert" class="disNone wordsSelectNone">
    <div class="bg"></div>
    <div class="content">
        <div class="fontbox"><div class="font"></div></div>
        <div class="btn">知道了</div>
    </div>
</div>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script>
        $(function(){

        });
    </script>

        <script type="text/javascript">
            wx.ready(function () {

                var UID = "{$uid}";
                var DEVICEID = '';
                var SENDSTR = '';

                // 初始化设备库
                wx.invoke('openWXDeviceLib', {}, function(res){

                    if(res.err_msg == 'openWXDeviceLib:ok'){

                        if(res.bluetoothState == 'on'){

                            if(res.isSupportBLE == 'yes'){
                                //获取到设备信息
                                wx.invoke('getWXDeviceInfos', {}, function(res){
                                    if(res.err_msg == 'getWXDeviceInfos:ok'){

                                        for(var i=0; i<res.deviceInfos.length; i++){
                                            var did = res.deviceInfos[i].deviceId;
                                            var dstate = res.deviceInfos[i].state;
                                            if(dstate == 'connected'){
                                                DEVICEID = did;
                                                $('#debug-box').show();
                                            }
                                        }
                                        if(DEVICEID == ''){
                                            f_alert('没有设备信息');
                                            return false;
                                        }
                                    }else{
                                        f_alert('获取设备信息失败');
                                        return false;
                                    }
                                });

                            }else if(res.isSupportBLE == 'no'){
                                f_alert('手机不支持BLE');
                                return false;
                            }

                        }else if(res.bluetoothState == 'off'){
                            f_alert('手机蓝牙没有打开');
                            return false;

                        }else if(res.bluetoothState == 'unauthorized'){
                            f_alert('用户没有授权微信使用蓝牙功能');
                            return false;
                        }

                    }else if(res.err_msg == 'openWXDeviceLib:fail'){
                        f_alert('初始化设备库失败');
                        return false;
                    }
                });

                // 发送数据
                $('#send').on('tap',function(){
                    SENDSTR = $('#sendinput').val().trim();
                    if(SENDSTR == ''){
                        f_alert('发送的数据不能为空');
                        return false;
                    }else{
                        SENDSTR = base64encode(SENDSTR);
                        wx.invoke('sendDataToWXDevice', {"deviceId":DEVICEID,"base64Data":SENDSTR}, function(res){
                            if(res.err_msg == 'sendDataToWXDevice:ok'){
                                // f_alert('发送成功');
                            }else{
                                f_alert('发送失败');
                            }
                        });
                    }

                });

                // 接收到设备数据
                wx.on('onReceiveDataFromWXDevice',function(argv) {
                    // f_alert('收到数据');
                    // var reid = argv.deviceId;
                    var redata = base64decode(argv.base64Data);
                    $('#receivedata').html(redata);
                });

            });
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

