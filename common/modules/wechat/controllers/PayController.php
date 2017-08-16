<?php

namespace common\modules\wechat\controllers;


use common\modules\wechat\components\WXPayService;
use common\modules\wechat\models\Order;
use common\modules\wechat\models\WxPlatform;
use common\modules\wechat\models\WxUser;
use yii\web\Controller;
use Yii;

class PayController extends Controller
{
    /**
     * @inheritDoc
     */
    public $enableCsrfValidation = false;

    /**
     * 通用通知接口
     */
    public function actionNotify()
    {
        $platform = WxPlatform::getFromHost();
        $wxPayService = new WXPayService($platform);
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        Yii::info('WXPay notify xml: ' . $xml);
        $result = $wxPayService->xmlToArray($xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($wxPayService->checkSign($result)) {
            $return = ['return_code' => 'SUCCESS'];
        } else {
            $return = [
                'return_code' => 'FAIL',
                'return_msg' => '签名失败',
            ];
        }
        echo $wxPayService->arrayToXml($return);

        if ($result['return_code'] == 'FAIL') {
            Yii::error('WXPay notify error message: ' . $result['return_msg']);
            return;
        }
        if ($result['result_code'] == 'FAIL') {
            Yii::error('WXPay notify error code: ' . (isset($result['err_code']) ? $result['err_code'] : '') . ', error message: '
                . (isset($result['err_code_des']) ? $result['err_code_des'] : ''));
            return;
        }

        if (isset($result['is_subscribe'])) { // 更新用户关注状态
            WxUser::updateSubscribe($result['openid'], $result['is_subscribe'] == 'Y');
        }

        $orderId = $result['out_trade_no'];
        // 并发控制，订单上锁
        Yii::$app->mutex->acquire(Order::className() . $orderId, Yii::$app->params['mutexTimeout']);

        $order = Order::findOne($orderId);
        if ($platform->appId != $result['appid'] || $platform->mchId != $result['mch_id'] ||
            !$order || $order->openId != $result['openid'] ||
            (!YII_DEBUG && (int)($order->total * 100) != $result['total_fee'])) {
            return;
        }
        $order->paid($result);
    }

    /**
     * 告警通知
     * @return string
     */
    public function actionWarning()
    {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        Yii::info('WXPay warning xml: ' . $xml);
//        $platform = WxPlatform::getFromHost();
//        $wxPayService = new WXPayService($platform);
//        $result = $wxPayService->xmlToArray($xml);
        Yii::error('WXPay warning message received, however not handled!');
        return 'success';
    }
} 
