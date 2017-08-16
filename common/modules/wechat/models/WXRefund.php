<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 11/14/15
 * Time: 9:09 PM
 */

namespace common\modules\wechat\models;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class WXRefund 退款
 * @package common\models
 *
 * @property integer $_id
 * @property int $reason 退款原因
 * @property integer $orderId 订单ID
 * @property string $transactionId 微信订单号
 * @property string $refundId 微信退款单号
 * @property string $refundChannel 退款渠道
 * @property int $refundFee 退款总金额,单位为分
 * @property int $totalFee 订单总金额，单位为分
 * @property int $cashFee 现金支付金额，单位为分
 * @property int $cashRefundFee 现金退款金额，单位为分
 * @property int $couponRefundFee 代金券或立减优惠退款金额
 * @property int $couponRefundCount 代金券或立减优惠使用数量
 * @property string $couponRefundId 代金券或立减优惠ID
 * @property integer $opUserId 主动发起退款时的管理员ID或卖家ID(string类型)
 * @property int $createTime
 * @property int $updateTime
 */
class WXRefund extends ActiveRecord
{
    const REASON_PAID_TIMEOUT = 1; // 已支付但超时的退款
    const REASON_PAID_CANCEL = 2; // 已支付但被卖家取消

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'wx_refunds';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'reason',
            'orderId',
            'transactionId',
            'refundId',
            'refundChannel',
            'refundFee',
            'totalFee',
            'cashFee',
            'cashRefundFee',
            'couponRefundFee',
            'couponRefundCount',
            'couponRefundId',
            'opUserId',
            'createTime',
            'updateTime',
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
                'createdAtAttribute' => 'createTime',
                'updatedAtAttribute' => 'updateTime',
            ]
        ];
    }
}