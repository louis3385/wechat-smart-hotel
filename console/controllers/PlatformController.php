<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 2/10/17
 * Time: 4:08 PM
 */

namespace console\controllers;

use common\modules\wechat\models\WxPlatform;
use yii\console\Controller;

class PlatformController extends Controller
{
    public function actionClearQuota()
    {
        $platform = WxPlatform::getFromHost();
        $ret = $platform->clearQuota();
        echo $ret ? 'SUCCEED' : 'FAILED';
    }
}