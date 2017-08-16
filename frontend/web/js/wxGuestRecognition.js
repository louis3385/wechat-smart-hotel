/**
 * Created by DongpoLiu on 2/03/17.
 */
$(function() {
    wx.ready(function () {

        var UID = "{$uid}";
        var DEVICEID = '';
        var SENDSTR = '';

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
            //wx.invoke('openWXDeviceLib', {'brandUserName':'gh_c90f968a53c8'}, function(res){

            if(res.err_msg == 'openWXDeviceLib:ok'){
                if(res.bluetoothState == 'on'){
                    if(res.isSupportBLE == 'yes'){
                        //获取到设备信息
                        /*
                         wx.invoke('startScanWXDevice', {}, function(res){
                         //alert(JSON.stringify(res));
                         //return false;

                         if(res.err_msg == 'startScanWXDevice:ok'){
                         //alert("scan OK here");

                         //for(var i=0; i<res.deviceInfos.length; i++){
                         //var did = res.deviceInfos[i].deviceId;
                         //var dstate = res.deviceInfos[i].state;
                         //if(did){
                         //DEVICEID = did;
                         //$('#box_info_nearby').show();
                         //}
                         //}
                         if(DEVICEID == ''){
                         //f_alert('No Device ID information');
                         //return false;
                         }
                         }else{
                         //f_alert('fail to get Device Information');
                         //return false;
                         }
                         });

                         wx.invoke("stopScanWXDevice",{},function(res){});
                         */

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

        wx.on('onScanWXDeviceResult',function(res){
            //console.log('扫描设备Result：', res);
            //alert("扫描到1个设备");
            alert(JSON.stringify(res));
            return false;

            //自己解析一下res，里面会有deviceid,扫描设备的目的就是为了得到这个
            //然后就可以开始绑定了
        });


        //设备绑定状态改变事件（解绑成功，绑定成功的瞬间，会触发）
        wx.on('onWXDeviceBindStateChange',function(res){
            //把res输出来看吧
            f_alert(JSON.stringify(res));
        });

        //设备连接状态改变
        wx.on('onWXDeviceStateChange',function(res){
            //有3个状态：connecting连接中,connected已连接,unconnected未连接
            //每当手机和设备之间的状态改变的瞬间，会触发一次
            f_alert(JSON.stringify(res));
        });

        wx.invoke('connectWXDevice', {'deviceId': '', 'connType':'blue'}, function(res) {
            //f_alert(JSON.stringify(res));
        });

        wx.invoke('startScanWXDevice', {}, function(res){
            //alert(JSON.stringify(res));
            //return false;
            //console.log('扫描设备Result：', res);

            if(res.err_msg == 'startScanWXDevice:ok'){
                //alert("scan OK here");
                //return false;

                //for(var i=0; i<res.deviceInfos.length; i++){
                //var did = res.deviceInfos[i].deviceId;
                //var dstate = res.deviceInfos[i].state;
                //if(did){
                //DEVICEID = did;
                //$('#box_info_nearby').show();
                //}
                //}
                if(DEVICEID == ''){
                    //f_alert('No Device ID information');
                    //return false;
                }
            }else{
                //f_alert('fail to get Device Information');
                //return false;
            }
        });

        wx.invoke("stopScanWXDevice",{},function(res){
            //alert("quiet");
            //return false;
        });

        // 接收到设备数据
        wx.on('onReceiveDataFromWXDevice',function(argv) {
            //alert(JSON.stringify(argv));
            //return false;
            var reid = argv.deviceId;
            if (reid == '') {
                f_alert("no reid");
            } else {
                $.ajax({
                    type : "post",
                    url : "post/api:7",
                    data : {
                        "deviceId" : reid
                    },
                    dataType : "json",
                    success : function(data) {
                        var state = data.state;
                        if (state == "0") {
                            hotelName = data.hotelName;
                            $('#number1').find('span').html(hotelName);
                            $('#hotelName').find('span').html(hotelName);
                            hotelId = data.hotelId;
                            $('#number2').find('span').html(data.companyName);
                            document.getElementById('hotelId').innerHTML=hotelId;
                        }
                    },
                    error : function(e) {
                        //alert("fail to get hotel info");
                    }
                });
            }

        });
    });
});
