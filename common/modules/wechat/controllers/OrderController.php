<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 4/15/16
 * Time: 3:09 PM
 */

namespace common\modules\wechat\controllers;


use common\modules\wechat\components\Util;
use common\modules\wechat\models\Order;
use common\modules\wechat\models\WxUser;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class OrderController extends Controller
{
    /**
     * @inheritDoc
     */
    public $enableCsrfValidation = false;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Ajax预支付
     * @throws \yii\base\Exception
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\UnauthorizedHttpException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\BadRequestHttpException
     * @return array
     */
    public function actionPrepay()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $orderId = \Yii::$app->request->post('order_id');

        // 并发控制，订单上锁
        \Yii::$app->mutex->acquire(Order::className() . $orderId, \Yii::$app->params['mutexTimeout']);

        $order = $this->findModel($orderId);

        if ($order->getSecondsRemaining() <= 0) {
            throw new BadRequestHttpException('订单支付已超时');
        }

        if ($order->total <= 0) {
            if (!$order->freePay()) {
                throw new ServerErrorHttpException('免单失败');
            }
            return [
                'status' => 'COMPLETE'
            ];
        } else {
            if (!Util::canWXPay(\Yii::$app->request->userAgent)) {
                throw new MethodNotAllowedHttpException('你的微信版本过低不支持微信支付，请升级到5.0以上版本后再试');
            }
            $jsApiParams = $order->prepay(\Yii::$app->user->id, Url::toRoute('pay/notify', true));
            if ($jsApiParams) {
                return [
                    'status' => 'PREPAID',
                    'param' => $jsApiParams
                ];
            } else {
                return [
                    'status' => 'PREPAY_FAIL',
                ];
            }
        }
    }

    /**
     * 检查订单是否支付成功
     * @return array
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCheckPay()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $orderId = \Yii::$app->request->post('order_id');

        // 并发控制，订单上锁
        \Yii::$app->mutex->acquire(Order::className() . $orderId, \Yii::$app->params['mutexTimeout']);

        $order = $this->findModel($orderId);

        if ($order->status === Order::STATUS_WAIT_TO_PAY) {
            // 支付通知接口尚未通知支付成功
            $order->syncPayStatus();
        }

        return [
            'status' => $order->status,
        ];
    }

    /**
     * Find order model by id.
     * @param $id
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @return Order
     */
    private function findModel($id)
    {
        $order = Order::findOne($id);
        if (!$order) {
            throw new NotFoundHttpException('订单不存在');
        }

        if ($order->openId != \Yii::$app->user->id) {
            throw new ForbiddenHttpException('不能查看他人的订单');
        }
        return $order;
    }
}