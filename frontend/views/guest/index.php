<?php

/* @var $this frontend\components\MyView */
/* @var $model common\models\Guest */
/* @var $wxUser common\modules\wechat\models\WxUser */

use common\modules\wechat\components\WeixinJSAPI;
use common\modules\wechat\components\Util;
use frontend\assets\guest\IndexAsset;
use yii\helpers\Html;

IndexAsset::register($this);
$this->registerJs(WeixinJSAPI::getPlatformConfigJS([
    'onMenuShareTimeline',
    'onMenuShareAppMessage',
    'onMenuShareQQ',
    'onMenuShareWeibo',
    'onMenuShareQZone',
    // WeChat AirSync APIs
    'openWXDeviceLib',
    'getWXDeviceInfos',
    'startScanWXDevice',
    'connectWXDevice',
    'sendDataToWXDevice',
    'onReceiveDataFromWXDevice',
    'getWXDeviceTicket'
]));
$this->title = 'WeChat Smart Hotel';
$this->bodyClass = 'index_re vueContent';
?>
    <style>
        /*alert*/
        #alert{
            z-index: 10;
            position: fixed;top:0px;left:0px;right: 0px;
            width: 100%;height: 100%;
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        }
        #alert .bg{
            /*z-index: 11;*/
            position: absolute;top:0px;left:0px;right: 0px;
            width: 100%;height: 100%;background: #1b1311;
            /*filter:alpha(opacity:80);
            -moz-opacity:0.8;*/
            opacity:0.8;
        }
        #alert .content{
            /*z-index: 12;*/
            word-wrap:break-word;word-break:break-all;
            width:80%;background: #F2F2F2;
            border-radius: 7px;
            position: absolute;top: 50%;left: 50%;
            -webkit-transform: translateX(-50%) translateY(-50%);
            -moz-transform: translateX(-50%) translateY(-50%);
            -ms-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
        }

        #alert .content .fontbox{
            position: relative;
            width: 100%;
            height: 70px;
        }
        #alert .content .fontbox .font{
            color: #585858;
            width: 90%;padding:0px 5%;text-align: center;
            position: absolute;top: 50%;left: 50%;
            -webkit-transform: translateX(-50%) translateY(-50%);
            -moz-transform: translateX(-50%) translateY(-50%);
            -ms-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
        }
        #alert .content .btn{
            width:80%;padding:10px 10%;
            text-align: center;color: #FF6839;
            border-top: 0.5px solid #FF6839;
        }

        .number-box{
            margin: 10px auto 10px auto;
            width: 90%;
            line-height: 60px;text-align: center;font-size: 16px;color:#fff;
        }
        #number1{
            float: left;
            width: 50%;height: 60px;
            text-align: left;
        }
        #number2{
            float: right;
            width: 50%;height: 60px;
            text-align: right;
        }

        .txt_nearby_hotel {
            margin: 35px 0 170px 0;
            color: rgb(51, 51, 51);
            font-size:16px;
            line-height:26px;
        }

        .btn_nearby_hotel,
        .btn_cancel_hotel {
            padding: 11px 87px;
            border-radius: 3px;
            box-sizing: border-box;
            text-align: center;
            background-color: rgb(133, 154, 230);
            color: white;
            font-size: 16px;
            margin: 0 auto;
        }

        .btn_nearby_hotel:active {
            background-color: rgb(105, 133, 234);
        }

        .btn_cancel_hotel {
            margin-top: 10px;
            background-color: rgb(235, 236, 242);
            border: 1px solid rgb(133, 154, 230);
            color: rgb(133, 154, 230);
            border: 1px solid rgba(0, 0, 0, 0.12);
        }

        .btn_cancel_hotel:active {
            background-color: rgb(211, 211, 212);
            color: rgb(135, 145, 183);
        }

        .disNone{
            display: none;
        }
        .wordsSelectNone{
            -moz-user-select: none; /*火狐*/
            -webkit-user-select: none;  /*webkit浏览器*/
            -ms-user-select: none;   /*IE10*/
            user-select: none;
        }

        #box_info_nearby {
            position: fixed;
            width: 100%;
            left: 50%;
            top: 50%;
            transform: translateX(-50%)translateY(-50%);
            text-align: center;
            background-color: rgb(200, 200, 200);
        }

        .head_nearby_hotel {
            margin-top: 20%;
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        .name_nearby_hotel {
            font-size: 16px;
            color: rgb(128,130,140);
            margin-top: 15px;
        }

        .fail_box_nearby {
            font-size: 18px;
            color: rgb(61, 61, 61);
        }
        .txt_declare_hotel{
            font-size:12px;
            line-height:26px;
            color:rgb(128,130,140);
            margin-top:21px;
        }
    </style>
    <div @click="selectingCity=false" v-show="firstShow" style="display:none">
        <!--吸顶-->
        <div class="toolbar_index"  v-if="'normal'==searchState" >
            <div class="location_tb_index" @click.stop="selectingCity=!selectingCity" :class="{ 'hide':searching==true}">
                <span class="txt_location_tbi" v-text="currentCity"></span>
                <span class="icon_location_tbi"></span>
            </div>
            <div class="search_tb_index">
                <input type="text" class="input_search_tbi" placeholder="Hotel / City / District" readonly v-model=searchWord @click="goToSearchInput" />
                <div class="icon_search_tbi"></div>
            </div>
        </div>
        <div class="toolbar_realSearch" :class="{'hide':start2Search}">
            <div class="icon_return_trs" @click="closeSearchBox"></div>
            <div class="box_search_trs">
                <div class="txt_search_trs" v-text="currentCity"></div>
                <input class="search_trs" placeholder="Search Hotel name / city / district" v-model=searchWord @keyup.enter=redirectToSearchResult v-el:search />
                <span class="icon_cancel_strs" @click="clearNstayfocus" v-if="searchWord.length>0"></span>
            </div>
            <div class="icon_search_trs" @click=redirectToSearchResult></div>
        </div>
        <div class="noDataFind" v-if="(!loading||!searching)&&0==actData.length">No Hotels here~</div>
        <div style="position:relative;">
            <div class="shadeScreen" style="height:calc( 100vh - 46px );" :class="{'show':selectingCity||start2Search}" @click="closeSearchBox">
            </div>
            <div class="city_select_pop" :class="{'show':selectingCity}" @click.stop>
                <div class="leaf_city_sp" @click="selectCity('China')" :class="{'selected':currentCity=='China'}">China</div>
                <div class="leaf_city_sp" @click="selectCity('Beijing')" :class="{'selected':currentCity=='Beijing'}">Beijing</div>
                <div class="leaf_city_sp" @click="selectCity('Shanghai')" :class="{'selected':currentCity=='Shanghai'}">Shanghai</div>
                <div class="leaf_city_sp" @click="selectCity('Guangzhou')" :class="{'selected':currentCity=='Guangzhou'}">Guangzhou</div>
            </div>
            <div class="outSide">
                <!--吸顶-->
                <div class="index_re_outSide">
                    <ul class="hotel_ul hotel_ul_liu">
                        <li v-for="hotel in actData">
                            <a @click="go2url('guest/'+ hotel.id)">
                                <h3> {{hotel.name}} </h3>
                                <div class="box_d">
                                    <div class="right">
                                        <p class="text_one"> {{hotel.companyName}} </p>
                                        <p class="text_two"> {{hotel.area}}</p>
                                        <p class="text_three">{{hotel.quota}} Rooms</p>
                                        <div v-if="hotel.enabledByBackend == true">
                                            <div class="sf">WeChat Smart Hotel</div>
                                        </div>
                                        <div class="tx"><img :src=hotel.companyLogo alt=""></div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <input id="use4DownKeyBoard" type="checkbox" />
                </div>
                <div v-if="!loading&&!hasNextIndex&&actData.length>=6" style="text-align:center;margin:15px 0">
                    No Hotels here
                </div>
            </div>
        </div>
    </div>

    <div id="box_info_nearby" class="box wordsSelectNone disNone">
        <?php if($wxUser['state']){?>
            <div class="number-box">
                <div id="number1"><span>Hotel</span></div>
                <div id="number2"><span>Service Time</span></div>
                <div class="clr"></div>
            </div>

            <div class="beacon_box_nearby">
                <div class="info_nearby_hotel">
                    <img class="head_nearby_hotel" src="<?= $wxUser['headImage']  ?>" />
                    <div class="name_nearby_hotel"> <?= $wxUser['nickName'] ?> </div>
                </div>
                <div class="txt_nearby_hotel">You are nearby <div id='hotelName'><span></span></div></div>
<!--
                <button class="btn_nearby_hotel" @click="goTo('/guest/' + hotel_id)">Confirm</button>
-->
                </div>
            </div>
        <?php }?>
    </div>

    <div id="alert" class="disNone wordsSelectNone">
        <div class="bg"></div>
        <div class="content">
            <div class="fontbox"><div class="font"></div></div>
            <div class="btn">I see</div>
        </div>
    </div>

    <script>
var hotel_id = document.getElementById("hotelId").innerHTML.toString();
alert(hotel_id);
    </script>

    <script>
        var _info = {
            _type: "index",
            _area_id: "qa",
            _area_name: "Shanghai"
        }
    </script>
