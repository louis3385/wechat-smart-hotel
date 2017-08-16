<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 1/18/17
 * Time: 7:26 PM
 */
namespace console\controllers;

use common\modules\wechat\models\WxNotification;
use common\modules\wechat\models\WxPlatform;
use yii\console\Controller;
use yii\db\Query;

class DeviceController extends Controller
{
    /**
     * authorizeDevice a device, which will return ID and QRCode
     */
    public function actionCreate()
    {
        $platform = WxPlatform::getFromHost();
        $device = $platform->authorizeDevice();
        echo $device->deviceid. ' ', $device->qrticket. '\n';
    }
}
