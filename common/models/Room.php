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
use common\modules\wechat\models\Order;
use common\modules\wechat\models\WxUser;
use yii\behaviors\TimestampBehavior;
use common\modules\hotel\models\Region;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

/**
 * This is the model class for collection "room".
 *
 * @property integer $_id ID
 * @property integer $hotelId Hotel ID
 * @property integer $roomTypeId Room Type ID
 * @property string $openId  Guest openId
 * @property string $name Room Name
 * @property float $unitPrice Unit Price
 * @property int $sellCount sell count
 * @property RoomType $roomType Room Type
 * @property Guest $guest Room Guest
 * @property Order $order Order
 * @property Hotel $hotel Hotel
 * @property integer $createdAt created at
 * @property integer $updatedAt updated at
 */

class Room extends Product
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'room';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'hotelId',
            'roomTypeId',
            'openId',
            'name',
            'unitPrice',
            'sellCount',
            'createdAt',
            'updatedAt',
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
     * @return \yii\db\ActiveQueryInterface
     */
    public function getRoomType()
    {
        return $this->hasOne(RoomType::className(), ['_id' => 'roomTypeId']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getGuest()
    {
        return $this->hasOne(Guest::className(),['roomId' => '_id']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['productId' => '_id']);
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
    public function getWxuser()
    {
        return $this->hasOne(WxUser::className(), ['openId' => 'openId']);
    }       
    
    /**
     * 用户下单后锁定, 库存需减一, 正在支付人数加一(如有)
     */
    public function lock()
    {
        // TODO: Implement lock() method.
    }

    /**
     * 用户订单取消或超时未支付时解锁, 库存需加一, 正在支付人数减一(如有)
     */
    public function unlock()
    {
        // TODO: Implement unlock() method.
    }

    /**
     * 用户支付完成, 正在支付人数减一(如有)
     */
    public function sold()
    {
        // TODO: Implement sold() method.
    }

    /**
     * 用户退货或卖家取消已支付订单, 库存需加一
     */
    public function refund()
    {
        // TODO: Implement refund() method.
    }

}