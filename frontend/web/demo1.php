<?php
use common\modules\wechat\models\WxPlatform;
use common\modules\wechat\components\WeixinJSAPI;
use yii\helpers\Html;
$platform = WxPlatform::getFromHost();
$this->registerJs(WeixinJSAPI::getPlatformConfigJS([
    'openWXDeviceLib',
    'getWXDeviceInfos',
    'startScanWXDevice',
    'connectWXDevice',
    'sendDataToWXDevice',
    'getWXDeviceTicket'
]));
?>
<?php $this->beginPage() ?>

<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <title>AirSync Demo</title>
    <link rel="stylesheet" href="css/demo/demo.css" />
    <script>
        var _pub_client = "mobile_ios";
        var _imgCdn="<?= \Yii::$app->request->hostInfo ?>";
        var _domain="<?= \Yii::$app->request->hostInfo ?>/";
        var _fmShezhi;
        var dataForShare={
            weixin_icon: _imgCdn + "/images/share/other_weixin_msg_3.png",
            weixin_tl_icon: _imgCdn + "/images/share/other_weixin_tl_3.png",
            weixin_url: _domain,
            qq_icon: _imgCdn + "/images/share/other_qq_3.png",
            weibo_icon: _imgCdn + "/images/share/other_weibo_3.png",
            url: _domain,
            title: "",
            description: "",
            sms: "",
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

<div id="main-box" class="box wordsSelectNone disNone">

    <div class="number-box">
        <div id="number1"><span>1234</span></div>
        <!-- <div id="number2">电量 <span>30</span>%</div> -->
        <div class="clr"></div>
    </div>

    <div class="led-box">
        <div id="led1" class="led off">
            <div class="icon"></div>
            <div class="font">LED1 off</div>
        </div>
        <div id="led2" class="led even on">
            <div class="icon"></div>
            <div class="font">LED2 on</div>
        </div>
        <div id="led3" class="led off">
            <div class="icon"></div>
            <div class="font">LED3 off</div>
        </div>
        <div id="led4" class="led even off">
            <div class="icon"></div>
            <div class="font">LED3 off</div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="switch-box">
        <div id="switch1" class="switch off">SwitchA off</div>
        <div id="switch2" class="switch off">SwitchB off</div>
        <div class="clr"></div>
    </div>

</div>

<div id="alert" class="disNone wordsSelectNone">
    <div class="bg"></div>
    <div class="content">
        <div class="fontbox"><div class="font"></div></div>
        <div class="btn">知道了</div>
    </div>
</div>
<script src="js/jweixin-1.0.0.js"></script>
<script src="js/zepto/zepto.js"></script>
<script src="js/zepto/endecode.js"></script>
<script src="js/zepto/event.js"></script>
<script src="js/zepto/fx.js"></script>
<script src="js/zepto/fx_methods.js"></script>
<script src="js/zepto/selector.js"></script>
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
                                        $('#main-box').show();
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
        $('#led1').on(CLICK,function(){

            if($(this).hasClass('off')){
                $(this).removeClass('off').addClass('on');
                $(this).find('.font').html('LED1 on');
                SENDSTR = 'LED1ON';
            }else{
                $(this).removeClass('on').addClass('off');
                $(this).find('.font').html('LED1 off');
                SENDSTR = 'LED1OFF';
            }
            SENDSTR = base64encode(SENDSTR);
            wx.invoke('sendDataToWXDevice', {"deviceId":DEVICEID,"base64Data":SENDSTR}, function(res){
                if(res.err_msg == 'sendDataToWXDevice:ok'){
                    // f_alert('发送成功');
                }else{
                    f_alert('发送失败');
                }
            });
        });
        $('#led2').on(CLICK,function(){
            if($(this).hasClass('off')){
                $(this).removeClass('off').addClass('on');
                $(this).find('.font').html('LED2 on');
                SENDSTR = 'LED2ON';
            }else{
                $(this).removeClass('on').addClass('off');
                $(this).find('.font').html('LED2 off');
                SENDSTR = 'LED2OFF';
            }
            SENDSTR = base64encode(SENDSTR);
            wx.invoke('sendDataToWXDevice', {"deviceId":DEVICEID,"base64Data":SENDSTR}, function(res){
                if(res.err_msg == 'sendDataToWXDevice:ok'){
                    // f_alert('发送成功');
                }else{
                    f_alert('发送失败');
                }
            });
        });
        $('#led3').on(CLICK,function(){
            if($(this).hasClass('off')){
                $(this).removeClass('off').addClass('on');
                $(this).find('.font').html('LED3 on');
                SENDSTR = 'LED3ON';
            }else{
                $(this).removeClass('on').addClass('off');
                $(this).find('.font').html('LED3 off');
                SENDSTR = 'LED3OFF';
            }
            SENDSTR = base64encode(SENDSTR);
            wx.invoke('sendDataToWXDevice', {"deviceId":DEVICEID,"base64Data":SENDSTR}, function(res){
                if(res.err_msg == 'sendDataToWXDevice:ok'){
                    // f_alert('发送成功');
                }else{
                    f_alert('发送失败');
                }
            });
        });
        $('#led4').on(CLICK,function(){
            if($(this).hasClass('off')){
                $(this).removeClass('off').addClass('on');
                $(this).find('.font').html('LED4 on');
                SENDSTR = 'LED4ON';
            }else{
                $(this).removeClass('on').addClass('off');
                $(this).find('.font').html('LED4 off');
                SENDSTR = 'LED4OFF';
            }
            SENDSTR = base64encode(SENDSTR);
            wx.invoke('sendDataToWXDevice', {"deviceId":DEVICEID,"base64Data":SENDSTR}, function(res){
                if(res.err_msg == 'sendDataToWXDevice:ok'){
                    // f_alert('发送成功');
                }else{
                    f_alert('发送失败');
                }
            });
        });

        // 接收到设备数据
        wx.on('onReceiveDataFromWXDevice',function(argv) {
            // f_alert('收到数据');
            // var reid = argv.deviceId;
            var redata = base64decode(argv.base64Data);
            if(redata.indexOf('*') > 0 ){
                //redata包含"*"
                var redataArr = redata.split("*");
                if(redataArr[0] == 'NUM'){
                    $('#number1').find('span').html(redataArr[1]);
                    // $('#number2').find('span').html(redataArr[2]);
                }
            }else{
                if(redata == 'KEY1ON'){
                    $('#switch1').removeClass('off').addClass('on');
                    $('#switch1').html('SwitchA on');
                }else if(redata == 'KEY1OFF'){
                    $('#switch1').removeClass('on').addClass('off');
                    $('#switch1').html('SwitchA off');
                }else if(redata == 'KEY2ON'){
                    $('#switch2').removeClass('off').addClass('on');
                    $('#switch2').html('SwitchB on');
                }else if(redata == 'KEY2OFF'){
                    $('#switch2').removeClass('on').addClass('off');
                    $('#switch2').html('SwitchB off');
                }
            }
        });

    });
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

