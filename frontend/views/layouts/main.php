<?php

/* @var $this frontend\components\MyView */
/* @var $content string */

use common\modules\wechat\models\WxPlatform;
use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);
$platform = WxPlatform::getFromHost();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="HandheldFriendly" content="true" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="apple-touch-icon" href="/images/other/icon_72x72.png" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico" size="16x16 24x24 32x32" />
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <?php $this->head() ?>
    <style>[ms-controller],[ms-important]{display:none}</style>
    <script>
        var _pub_client = "mobile_ios";
        var _imgCdn="<?= \Yii::$app->request->hostInfo ?>";
        var _domain="<?= \Yii::$app->request->hostInfo ?>/";
        var _HDB_SOURCE_KEY = "hdb_source";
        var _HDB_POS_KEY = "hdb_pos";
        var _isNoAlertUser = "";// 详情页外链点击是否不弹出提示框
        var _fmShezhi;
        var dataForShare={
            weixin_icon: _imgCdn + "/images/share/other_weixin_msg_3.png",
            weixin_tl_icon: _imgCdn + "/images/share/other_weixin_tl_3.png",
            weixin_url: _domain,
            qq_icon: _imgCdn + "/images/share/other_qq_3.png",
            weibo_icon: _imgCdn + "/images/share/other_weibo_3.png",
            url: _domain,
            title: "WeChat Smart Hotel",
            description: "WeChat Smart Hotel",
            sms: "WeChat Smart Hotel",
            appId: "<?= $platform->appId ?>",
            callback: function() {
                _$(_api3._shareCount, "info_id=0&info_type=other", function() {});
            }
        };
    </script>
    <?php if (YII_ENV_PROD && $platform->hmId) { ?>
        <script>
            var _hmt = _hmt || [];
            (function() {
                var hm = document.createElement("script");
                hm.src = "//hm.baidu.com/hm.js?<?= $platform->hmId ?>";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
        </script>
    <?php } ?>
</head>
<body<?= $this->bodyClass ? " class=\"$this->bodyClass\"" : ''?><?= $this->msController ? " ms-controller=\"$this->msController\"" : ''?>>
<?php $this->beginBody() ?>
    <?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
