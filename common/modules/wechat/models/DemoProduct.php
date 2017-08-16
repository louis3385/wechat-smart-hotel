<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 4/20/16
 * Time: 12:20 PM
 */

namespace common\modules\wechat\models;


class DemoProduct extends Product
{
    public static function collectionName()
    {
        return 'demo_products';
    }

    /**
     * @param $name
     * @param $unitPrice
     * @param $sellCount
     * @return DemoProduct|null
     */
    public static function create($name, $unitPrice, $sellCount)
    {
        $model = new self([
            'name' => $name,
            'unitPrice' => $unitPrice,
            'sellCount' => $sellCount
        ]);
        if ($model->save(false)) {
            return $model;
        }
        return null;
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