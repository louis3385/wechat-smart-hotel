<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 10:39 AM
 */

namespace common\modules\hotel\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "beacons".
 *
 * @property integer $_id
 * @property string $name Name
 * @property string $uuid UUID
 * @property string $majorId Major ID
 * @property string $minorId Minor ID
 * @property string $batteryLevel Battery Level
 * @property string $welcomeMessage Welcome Message
 * @property string $farewellMessage Farewell Message
 * @property string $seriesNumber series Number
 * @property integer $regionId Region ID
 * @property string $status Beacon Status, 1: online, 0:offline
 * @property string $password Beacon password 
 * @property Region $region Region of beacon
 */

class Beacon extends ActiveRecord
{
    const STATUS_ONLINE = 0; // online
    const STATUS_OFFLINE = 1; // offline

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'beacons';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->status = self::STATUS_ONLINE;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['UUID','majorId', 'minorId', 'openId', 'hotelId', 'roomId'], 'required'],
            [['firstName', 'lastName', 'email'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'UUID',
            'majorId',
            'minorId',
            'batteryLevel',
            'welcomeMessage',
            'farewellMessage',
            'seriesNumber',
            'regionId',
            'status',
            'password',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'UUID' => 'UUID',
            'majorId' => 'Major ID',
            'minorId' => 'Minor ID',
            'batteryLevel' => 'Battery Level',
            'welcomeMessage' => 'Welcome Message',
            'farewellMessage' => 'Farewell Message',
            'seriesNumber' => 'Series Number',
            'regionId' => 'Region ID',
            'status' => 'status',
            'password' => 'password',
        ];
    }

    /**
     * get status to status map
     * @return array
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_ONLINE => 'Online',
            self::STATUS_OFFLINE => 'Offline',

        ];
    }

    /**
     * get status info
     * @return string
     */
    public function getStatusInfo()
    {
        $map = self::getStatusMap();
        return $map[$this->status];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getApp()
    {
        return $this->hasOne(ConchiApp::className(), ['_id' => 'appId']);
    }

}
