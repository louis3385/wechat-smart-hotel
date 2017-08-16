<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 1/17/17
 * Time: 10:30 PM
 */

namespace common\modules\wechat\models;

use common\modules\wechat\models\WxUser;
use common\modules\wechat\models\WxPlatform;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\Hotel;

/**
 * This is the base model class for collection "device".
 *
 * @property integer $_id
 * @property integer $platformId WeChat Platform ID
 * @property integer $productId Product ID
 * @property string $deviceType Device Type (currently is WeChat Public Account Original ID)
 * @property string $deviceId WeChat Device ID
 * @property string $state Device State, device state can be: connecting, connected or disconnected
 * @property string $connType Connection Type, can be blue (default), lan or null
 * @property string $base64Data Data between Device and H5 page
 * @property string $openId associated WeChat User ID
 * @property string $hotelId ID of Hotel, which is bound to the Device
 * @property int $updatedTime Updated Time
 * @property int $createdTime Created Time
 * @property WxUser $wxUser associated WxUser
 * @property WxPlatform $platform WeChat Platform
 */
class Device extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'device';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->deviceType = self::getPlatform()->uniqueId;;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'platformId',
            'productID',
            'deviceType',
            'deviceId',
            'state',
            'connType',
            'base64Data',
            'openId',
            'hotelId',
            'updatedTime',
            'createdTime'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'platformId' => 'WeChat Platform ID',
            'productId' => 'Product ID',
            'deviceType' => 'Device Type',
            'deviceId' => 'Device ID',
            'state' => 'Device State',
            'connType' => 'Connection Type',
            'base64Data' => 'Data between Device and H5 page',
            'openId' => 'associated WeChat User ID',
            'hotelId' => 'Hotel ID',
            'updatedTime' => 'Updated Time',
            'createdTime' => 'Created Time',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdTime',
                'updatedAtAttribute' => 'updatedTime',
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlatform()
    {
        return $this->hasOne(WxPlatform::className(), ['_id' => 'platformId']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getWxUser()
    {
        return $this->hasOne(WxUser::className(), ['openId' => 'openId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotel()
    {
        return $this->hasOne(Hotel::className(), ['_id' => 'hotelId']);
    }

    /**
     * get Device Message from AirSync Beacon
     * @param string $deviceId
     * @param string $openId
     */
    public static function receiveDeviceHelloMsg($deviceId, $openId)
    {

    }
}