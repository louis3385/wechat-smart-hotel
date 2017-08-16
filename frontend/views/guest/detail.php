<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 2016-12-26
 * Time: 17:27
 */

/* @var $this frontend\components\MyView */
/* @var $hotel common\models\Hotel */
/* @var $guest common\models\Guest */
/* @var $info array */
/* @var $share array */

use common\modules\wechat\components\WeixinJSAPI;
use frontend\assets\guest\DetailAsset;
use yii\bootstrap\Alert;
use common\modules\wechat\components\Util;

DetailAsset::register($this);
$this->registerJs(WeixinJSAPI::getPlatformConfigJS([
    'onMenuShareTimeline',
    'onMenuShareAppMessage',
    'onMenuShareQQ',
    'onMenuShareWeibo',
    'onMenuShareQZone',
    'openLocation',
    'getLocation',
    'scanQRCode'
]));
$this->title = $hotel->name;
$roomTypes = $hotel->roomTypes;
$absoluteUrl = Yii::$app->urlManager->createAbsoluteUrl(['guest/detail', 'id' => (string)$hotel->_id]);
?>
<body v-show="show" style="display:none" ontouchstart="">

<div class="container_detail_vue" v-if="!enabled">
    <div class="header_detail_v">
        <div class="title_head_dv" v-text=bannerInfo._title></div>
        <div class="other_head_dv">
            <div class="left_other_hdv">
                <span class="head_left_ohdv"><img class="icon_head_lohdy" :src=getHeadIcon(bannerInfo._companyLogo) /></span>
                <span class="name_left_ohdv" >{{bannerInfo._name}}</span>
            </div>
        </div>
    </div>
    <div class="info_detail_v" v-if="!enabled">
        <div class="remain_info_dv">
            <span class="icon_remain_idv"></span>
            <span class="txt_remain_idv">{{getRoomStatus(lbsInfo)}}</span>
        </div>
        <?php
        $longitude=$info['_lbsInfo']['_address']['_logitude'];
        $latitude=$info['_lbsInfo']['_address']['_latitude'];
        $dFlag= $longitude&&$latitude;
        $html=$dFlag?"wx.openLocation({longitude:".$longitude.",latitude:".$latitude.'})':"";
        ?>
        <a ontouchstart="" onclick="<?= $html ?>" href="javascript:void(0);" style="display:block;" >
            <div class="location_info_dv" ontouchstart="">
                <span class="left_location_idv">
                    <span class="icon_left_lidv"></span>
                    <span class="txt_left_lidv">{{lbsInfo._address._full}}</span>
                </span>
                <span class="arrow_location_idv" v-if="lbsInfo._address._latitude&&lbsInfo._address._logitude"></span>
            </div>
        </a>
    </div>
    <div class="content_detail_v">
        <div class="banner_content_dv">
            <span class="icon_banner_cdv"></span>
            <span class="txt_banner_cdv">Hotel Introductions</span>
        </div>
        <div class="forShareDes">
            {{{hotelDescriptions}}}
        </div>
    </div>
    <!-- Room Type List -->
    <cmp-rt></cmp-rt>
</div>
<!-- Function Menu -->
<cmp-fm :os.sync="orderState" :tl.sync="timeLeft" :ir.sync="enabled"></cmp-fm>
<!-- Book and Payment -->
<cmp-bc :os.sync="orderState" :oi.sync="orderInfo" :tl.sync="timeLeft" ></cmp-bc>
</body>
<script>var _info = <?= json_encode($info) ?>;</script>