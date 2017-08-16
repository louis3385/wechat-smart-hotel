<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 3/23/16
 * Time: 10:30 PM
 */

namespace common\modules\wechat\models;

use yii\db\ActiveRecord;

/**
 * This is the base model class for collection "products".
 *
 * @property integer $_id
 * @property string $name 商品名称
 * @property string $description 商品描述
 * @property float $unitPrice 单价
 * @property int $sellCount 数量
 */
abstract class Product extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'unitPrice',
            'sellCount',
        ];
    }

    /**
     * 商品描述
     * @return string
     */
    public function getDescription()
    {
        return $this->name;
    }

    /**
     * 用户下单后锁定, 库存需减一, 正在支付人数加一(如有)
     */
    public abstract function lock();

    /**
     * 用户订单取消或超时未支付时解锁, 库存需加一, 正在支付人数减一(如有)
     */
    public abstract function unlock();

    /**
     * 用户支付完成, 正在支付人数减一(如有)
     */
    public abstract function sold();

    /**
     * 用户退货或卖家取消已支付订单, 库存需加一
     */
    public abstract function refund();
}