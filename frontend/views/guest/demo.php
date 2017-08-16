<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 1/13/17
 * Time: 2:41 PM
 */
/* @var $this frontend\components\MyView */

use common\modules\wechat\models\WxPlatform;
use common\modules\wechat\components\WeixinJSAPI;
use frontend\assets\guest\DemoAsset;
$platform = WxPlatform::getFromHost();
DemoAsset::register($this);
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
$this->title = 'AirSync Demo';
?>
    <noscript>
        <div id="noscript">当前的浏览器不支持JavaScript脚本</div>
    </noscript>

    <div id="main-box" class="box wordsSelectNone disNone">

        <div class="number-box">
            <div id="number1"><span>Device ID</span></div>
            <div id="number2"><span>Service Time</span></div>
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
            <div class="btn">I see</div>
        </div>
    </div>

