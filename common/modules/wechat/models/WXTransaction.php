<?php

namespace common\modules\wechat\models;


use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for collection "wx_transactions".
 *
 * @property integer $_id
 * @property string $transactionId 微信支付订单号
 * @property string $bankType 付款银行(银行类型,采用字符串类型的银行标识)
 * @property int $totalFee 订单总金额,单位为分
 * @property string $feeType 货币种类(货币类型,符合 ISO 4217 标 准的三位字母代码,默认人 民币:CNY)
 * @property string $timeEnd 支付完成时间(支付完成时间,格式为yyyyMMddhhmmss，该时间取自微信支付服务器)
 * @property string $tradeState 交易状态，通过微信订单查询接口更新(
 * SUCCESS:支付成功;
 * REFUND:转入退款;
 * NOTPAY:未支付(输入密码或确认支付超时);
 * CLOSED:已关闭;
 * REVOKED:已撤销;
 * USERPAYING:用户支付中;
 * PAYERROR:支付失败(其他原因,如银行返回失败)
 * )
 * @property bool $refunded 是否已退款到账户
 * @property integer $orderId 所属订单ID
 * @property Order $order 所属订单
 */
class WXTransaction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'wx_transactions';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'transactionId',
            'bankType',
            'totalFee',
            'feeType',
            'timeEnd',
            'tradeState',
            'refunded',
            'orderId',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'transactionId' => '微信支付订单号',
            'timeEnd' => '支付完成时间',
            'bankType' => '付款银行',
            'feeType' => '货币种类',
            'tradeState' => '交易状态',
            'orderId' => '关联订单',
        ];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['_id' => 'orderId']);
    }
}
