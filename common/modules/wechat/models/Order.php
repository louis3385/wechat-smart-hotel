<?php

namespace common\modules\wechat\models;

use yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

use common\modules\wechat\components\WXPayService;
use common\modules\wechat\components\Util;

/**
 * This is the model class for collection "order".
 *
 * @property integer $_id
 * @property string $openId 微信用户ID
 * @property integer $productId 所购商品ID
 * @property integer $platformId 公众号ID
 * @property float $total 总金额
 * @property int $payEndTime 微信支付完成时间(本地服务器时间)
 * @property int $status 订单状态
 * @property string $prepayId 微信预支付ID
 * @property int $prepayEndTime 微信预支付完成时间(本地服务器时间)
 * @property string $wxTransactionNum 微信支付订单号
 * @property integer $wxRefundId 退款成功后的WXRefund的ID
 * @property int $createTime
 * @property int $updateTime
 * @property WxUser $wxUser 微信用户
 * @property Product $product 所购商品
 * @property string $statusInfo 状态信息
 * @property WXTransaction $wxTransaction 微信支付交易
 * @property WXRefund $wxRefund 微信退款记录
 * @property WxPlatform $platform 所属公众号
 */
class Order extends ActiveRecord
{
    const STATUS_WAIT_TO_PAY = 1;       // 待支付
    const STATUS_PAID_SUCCESS = 2;      // 已支付（成功）
    const STATUS_PAID_TIMEOUT = 5;      // 已支付（超时）
    const STATUS_PAID_CANCEL = 8;       // 已付款（但在通知到来前取消）
    const STATUS_TIMEOUT = 7;           // 超时未支付
    const STATUS_CANCELED = 6;          // 已取消
    const STATUS_REFUNDED = 9;          // 已退款

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->status = self::STATUS_WAIT_TO_PAY;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'productId',
            'total',
            'openId',
            'platformId',
            'status',
            'source',
            'prepayId',
            'prepayEndTime',
            'payEndTime',
            'wxTransactionNum',
            'wxRefundId',
            'createTime',
            'updateTime',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'productId' => 'Product Id',
            'total' => '总金额',
            'openId' => '微信用户Id',
            'platformId' => '微信公众号',
            'prepayId' => 'Prepay Id',
            'prepayEndTime' => 'Prepay EndTime',
            'status' => '订单状态',
            'source' => '订单来源',
            'payEndTime' => '微信支付完成时间',
            'wxTransactionNum' => '微信支付订单号',
            'wxRefundId' => '微信退款Id',
            'createTime' => '创建时间',
            'updateTime' => '更新时间',
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

    /**
     * @return \yii\db\ActiveQueryInterface
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
     * @return \yii\db\ActiveQueryInterface
     */
    public function getWxTransaction()
    {
        return $this->hasOne(WXTransaction::className(), ['orderId' => '_id']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getWxRefund()
    {
        return $this->hasOne(WXRefund::className(), ['orderId' => '_id']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getProduct()
    {
        if($this->source == "vote"){
            return $this->hasOne(Voter::className(), ['_id' => 'productId']);
        }
        $product = Yii::$container->get('common\modules\wechat\models\Product');
        return $this->hasOne($product::className(), ['_id' => 'productId']);
    }

    /**
     * @inheritdoc
     */
    /*public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // 三个入口：微信后台通知接口、微信支付成功
        if (array_key_exists('status', $changedAttributes)) {
            if ($this->status == self::STATUS_PAID_SUCCESS) {
                
            }
        }
    }*/

    /**
     * 获取状态到状态描述map
     * @return array
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_WAIT_TO_PAY => '待支付',
            self::STATUS_PAID_SUCCESS => '已支付',
            self::STATUS_CANCELED => '已取消',
            self::STATUS_PAID_TIMEOUT => '已付款已超时',
            self::STATUS_PAID_CANCEL => '已付款已取消',
            self::STATUS_TIMEOUT => '超时未支付',
        ];
    }

    /**
     * 获取状态信息
     * @return string
     */
    public function getStatusInfo()
    {
        $map = self::getStatusMap();
        return $map[$this->status];
    }

    /**
     * 与微信同步订单支付状态
     */
    public function syncPayStatus()
    {
        $wxPlayService = new WXPayService($this->platform);
        $result = $wxPlayService->queryOrder((string)$this->_id);
        if (!$result || !isset($result['trade_state'])) {
            return;
        }
        if ($result['trade_state'] == 'SUCCESS') {
            $this->paid($result);
        }
    }

    /**
     * 根据微信支付结果(支付成功)更新订单
     * @param array $result
     */
    public function paid($result)
    {
        if ($this->status == Order::STATUS_WAIT_TO_PAY ||
            $this->status == Order::STATUS_CANCELED ||
            $this->status == Order::STATUS_TIMEOUT) {
            $wxTransaction = new WXTransaction();
            // 通知接口没有trade_state字段
            $wxTransaction->tradeState = isset($result['trade_state']) ? $result['trade_state'] : 'SUCCESS';
            $wxTransaction->bankType = $result['bank_type'];
            $wxTransaction->totalFee = $result['total_fee'];
            if (isset($result['fee_type'])) {
                $wxTransaction->feeType = $result['fee_type'];
            }
            $wxTransaction->transactionId = $result['transaction_id'];
            $wxTransaction->timeEnd = $result['time_end'];
            $wxTransaction->orderId = $this->_id;
            $wxTransaction->save();

            $this->payEndTime = time();
            $this->wxTransactionNum = $wxTransaction->transactionId;
            if ($this->status == Order::STATUS_WAIT_TO_PAY) {
                if (($this->payEndTime - $this->createTime) > Yii::$app->params['payTimeout'] * 60) { // 支付成功但超时
                    $this->status = Order::STATUS_PAID_TIMEOUT;
                    $this->product->unlock();
                    if (!$this->refund(WXRefund::REASON_PAID_TIMEOUT)) {
                        Yii::error('订单' . (string)$this->_id . '的支付超时退款失败');
                    }
                } else {
                    $this->status = self::STATUS_PAID_SUCCESS;
                    $this->product->sold();
                }
            } else if ($this->status == Order::STATUS_TIMEOUT) {
                $this->status = Order::STATUS_PAID_TIMEOUT;
                if (!$this->refund(WXRefund::REASON_PAID_TIMEOUT)) {
                    Yii::error('订单' . (string)$this->_id . '的支付超时退款失败');
                }
            } else { // $this->status == Order::STATUS_CANCELED
                $this->status = Order::STATUS_PAID_CANCEL;
                if (!$this->refund(WXRefund::REASON_PAID_CANCEL)) {
                    Yii::error('订单' . (string)$this->_id . '的用户取消退款失败');
                }
            }
            if (!$this->save()) {
                Yii::error('Order update failed.');
            }
        }
    }

    /**
     * 免单
     * @return bool
     */
    public function freePay()
    {
        if ($this->total > 0) {
            return false;
        }
        $this->product->sold();
        $this->status = Order::STATUS_PAID_SUCCESS;
        return $this->save();
    }

    /**
     * 向微信预支付并获取前端支付所需的JS参数
     * @param string $openId
     * @param string $notifyUrl
     * @return array|null
     */
    public function prepay($openId, $notifyUrl)
    {
        $platform = $this->platform;
        $wxPayService = new WXPayService($platform);
        if (!$this->prepayId) {
            $amount = YII_DEBUG ? 0.01 : $this->total; // 测试用户只要1分钱
            $this->prepayId = $wxPayService->unifiedOrder($openId, $amount, $this->product->description,
                (string)$this->_id, $notifyUrl);
            $this->prepayEndTime = time();
            $this->save(false);
        }
        if ($this->prepayId) {
            $jsApiParams = [
                'appId' => $platform->appId,
                'timeStamp' => (string)time(),
                'nonceStr' => Util::createNonceStr(),
                'package' => 'prepay_id=' . $this->prepayId,
                'signType' => 'MD5',
            ];
            $jsApiParams['paySign'] = $wxPayService->getSign($jsApiParams);
            return $jsApiParams;
        } else {
            return null;
        }
    }

    /**
     * 微信支付退款
     * @param string $reason
     * @param integer $opUserId
     * @return bool
     */
    public function refund($reason, $opUserId = null)
    {
        if ($this->total <= 0) {
            return true;
        }
        /* @var WXRefund $wxRefund */
        $wxRefund = WXRefund::findOne(['orderId' => $this->_id]);
        if (!$wxRefund) {
            $wxRefund = new WXRefund();
            $wxRefund->reason = $reason;
            $wxRefund->orderId = $this->_id;
            $wxRefund->opUserId = $opUserId;
            $wxRefund->save(false);
        }
        $wxPayService = new WXPayService($this->platform);
        $result = $wxPayService->refund((string)$this->_id, (string)$wxRefund->_id,
            $this->total * 100, $this->total * 100, $this->platform->mchId);
        if ($result) {
            $wxRefund->transactionId = $result['transaction_id'];
            $wxRefund->refundId = $result['refund_id'];
            $wxRefund->refundChannel = isset($result['refund_channel']) ? $result['refund_channel'] : null;
            $wxRefund->refundFee = $result['refund_fee'];
            $wxRefund->totalFee = $result['total_fee'];
            $wxRefund->cashFee = $result['cash_fee'];
            $wxRefund->cashRefundFee = isset($result['cash_refund_fee']) ? $result['cash_refund_fee'] : null;
            $wxRefund->couponRefundFee = isset($result['coupon_refund_fee']) ? $result['coupon_refund_fee'] : null;
            $wxRefund->couponRefundCount = isset($result['coupon_refund_count']) ? $result['coupon_refund_count'] : null;
            $wxRefund->couponRefundId = isset($result['coupon_refund_id']) ? $result['coupon_refund_id'] : null;
            $wxRefund->save(false);

            $this->wxRefundId = $wxRefund->_id;
            $this->status = self::STATUS_REFUNDED;
            $this->save(false);

            return true;
        }
        return false;
    }

    /**
     * 用户支付成功后, 管理员或卖家取消订单
     * @param integer $opUserId
     * @return bool
     */
    public function cancel($opUserId)
    {
        if ($this->status != self::STATUS_PAID_SUCCESS) {
            return false;
        }

        $this->status = self::STATUS_CANCELED;
        if (!$this->refund(WXRefund::REASON_PAID_CANCEL, $opUserId)) {
            Yii::error('订单' . (string)$this->_id . '的取消退款失败');
        }
        if ($this->save()) {
            $this->product->refund();
            return true;
        }
        return false;
    }

    /**
     * 获取支付剩余时间，单位秒
     * @return int
     */
    public function getSecondsRemaining()
    {
        $seconds = $this->createTime + \Yii::$app->params['payTimeout'] * 60 - time();
        return max($seconds, 0);
    }

    /**
     * 检查超时未支付的订单，更改状态为超时并退款（如有）
     */
    public static function checkTimeout()
    {
        $orders = self::findAll([
            'status' => self::STATUS_WAIT_TO_PAY,
            'createTime' => ['$lt' => (time() - Yii::$app->params['payTimeout'] * 60)],
        ]);

        foreach ($orders as $order) {
            Yii::$app->mutex->acquire(Order::className() . (string)$order->_id, Yii::$app->params['mutexTimeout']);
            $order->status = self::STATUS_TIMEOUT;
            if ($order->save()) {
                $order->product->unlock();
            }
            Yii::$app->mutex->release(Order::className() . (string)$order->_id);
        }
    }

    /**
     * 创建订单
     * @param integer $platformId
     * @param string $openId
     * @param \Closure $productCallback
     * @param string $source
     * @return Order|null
     */
    public static function create($platformId, $openId, $productCallback,$source = "party")
    {
        if (!($productCallback instanceof \Closure)) {
            return null;
        }
        $product = $productCallback();
        if (!($product instanceof Product)) {
            return null;
        }

        $order = new Order();
        $order->platformId = $platformId;
        $order->openId = $openId;
        $order->productId = $product->_id;
        $order->total = $product->unitPrice * $product->sellCount;
        $order->source = $source;
        if ($order->save()) {
            $product->lock();
            return $order;
        }
        return null;
    }
}

