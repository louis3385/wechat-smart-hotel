<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 15:27 AM
 */

namespace common\models;

use common\modules\hotel\models\Beacon;
use common\modules\wechat\models\Product;
use yii\behaviors\TimestampBehavior;
use common\modules\hotel\models\Region;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

/**
 * This is the model class for collection "room_type".
 *
 * @property integer $_id ID
 * @property integer $hotelId Hotel ID
 * @property string $name Room Type Name
 * @property float $unitPrice Unit Price
 * @property integer $quota  Quota
 * @property Room[] $rooms ordered Rooms
 * @property int $payingCount paying Count
 * @property int $remainingCount count in Stock
 * @property Hotel $hotel Hotel
 * @property integer $roomsCount Paid Room count
 * @property integer $guestsCount Guests count
 * @property integer $createdAt created At
 * @property integer $updatedAt updated At
 */

class RoomType extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'room_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quota'], 'integer'],
            ['name', 'string'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'hotelId',
            'name',
            'quota',
            'unitPrice',
            'payingCount',
            'remainingCount',
            'state',
            'createdAt',
            'updatedAt',
            'guestsCount',
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
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'hotelId' => 'Hotel ID',
            'name' => 'Room Type Name',
            'quota' => 'Room Quota',
            'unitPrice' => 'Unit Price',
            'payingCount' => 'Paying count',
            'remainingCount' => 'Remaining Count in stock',
            'createdAt' => 'Created at',
            'updatedTime' => 'Updated at',
            'guestsCount' => 'Guests Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getHotel()
    {
        return $this->hasOne(Hotel::className(), ['_id' => 'hotelId']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getRooms()
    {
        return $this->hasMany(Room::className(), ['roomTypeId' => '_id']);
    }


}