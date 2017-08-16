/**
 * Created by DongpoLiu on 1/17/17.
 */
$(function() {
    wx.ready(function () {

        var UID = "{$uid}";
        var DEVICEID = '';
        var SENDSTR = '';
        var CLICK = '';

        /*******兼容click事件和tap事件**********/
        var UA = window.navigator.userAgent.toLowerCase();
        var CLICK = 'click';
        if(/ipad|iphone|android/.test(UA)){
            CLICK = 'tap';
        }

        function f_alert(str){
            $('#alert').find('.font').html(str);
            $('#alert').show();
        }

        $(function(){
            // 关闭自定义alert
            $('#alert').find('.btn').on(CLICK, function(){
            $('#alert').hide();
            });
        });


        // 初始化设备库
        wx.invoke('openWXDeviceLib', {}, function(res){

            if(res.err_msg == 'openWXDeviceLib:ok'){
                if(res.bluetoothState == 'on'){
                    if(res.isSupportBLE == 'yes'){
                        //获取到设备信息
                        wx.invoke('getWXDeviceInfos', {}, function(res){
                        //alert(JSON.stringify(res));
                        //return false;

                            if(res.err_msg == 'getWXDeviceInfos:ok'){
                            //f_alert(res.deviceInfos.length);
                            //return false;
                              
                                for(var i=0; i<res.deviceInfos.length; i++){
                                    var did = res.deviceInfos[i].deviceId;
                            //f_alert(did);
                            //return false;
                                    var dstate = res.deviceInfos[i].state;
                            //f_alert(dstate);
                            //return false;
                                    if(dstate == 'connected'){
                                        DEVICEID = did;
                                        $('#main-box').show();
                                    }
                                }
                                if(DEVICEID == ''){
                                    f_alert('No Device ID information');
                                    return false;
                                }
                            }else{
                                f_alert('fail to get Device Information');
                                return false;
                            }
                        });

                    }else if(res.isSupportBLE == 'no'){
                        f_alert('your mobile doesnot support Bluetooth');
                        return false;
                    }

                }else if(res.bluetoothState == 'off'){
                    f_alert('Please open the phone Bluetooth');
                    return false;

                }else if(res.bluetoothState == 'unauthorized'){
                    f_alert('Please authorize WeChat to use your Bluetooth');
                    return false;
                }

            }else if(res.err_msg == 'openWXDeviceLib:fail'){
                f_alert('fail to initialize WeChat Device Lib');
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
                    //f_alert('sending successfully');
                }else{
                    f_alert('sending failed');
                    //return false;
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
                    //f_alert('sending successfully');
                }else{
                    f_alert('sending failed');
                    //return false;
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
                    //f_alert('sending successfully');
                }else{
                    f_alert('sending failed');
                    //return false;
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
                    //f_alert('sending successfully');
                }else{
                    f_alert('sending failed');
                    //return false;
                }
            });
        });

        // 接收到设备数据
        wx.on('onReceiveDataFromWXDevice',function(argv) {
            //f_alert('收到数据');
            var reid = argv.deviceId;
            //f_alert(reid);
            var redata = base64decode(argv.base64Data);
            //f_alert(redata);
            if(redata.indexOf('*') > 0 ){
                //redata包含"*"
                var redataArr = redata.split("*");
                if(redataArr[0] == 'NUM'){
                    $('#number1').find('span').html(reid);
                    $('#number2').find('span').html(redataArr[1]);
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
});
