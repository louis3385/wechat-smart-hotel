<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/9/16
 * Time: 10:12 AM
 */

namespace frontend\controllers;

use common\models\Hotel;
use common\models\Guest;
use common\modules\wechat\models\Device;
use common\modules\wechat\components\Util;
use common\modules\wechat\models\Order;
use common\modules\wechat\models\WxUser;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\Cookie;

class GuestController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
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
     * Index Page for hotels
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest && !\Yii::$app->session->get('BYPASS_LOGIN') && strpos(\Yii::$app->request->getUserAgent(), 'MicroMessenger') !== false ) {
            \Yii::$app->session->set('BYPASS_LOGIN', true);
            $this->redirect(['wechat/oauth/login', 'returnUrl' => \Yii::$app->request->absoluteUrl, 'silent' => 1]);
        }

        $openId = \Yii::$app->user->id;
        $wxUser = WxUser::findOne(['openId' => $openId]);
        $deviceId = \Yii::$app->params['deviceId'];
        $wxUser->platform->bindDeviceToOpenidByForce($deviceId, $openId);

        return $this->render('index', [
            'wxUser' => [
                'state' => 1,
                'headImage' => $wxUser->headImgUrl,
                'nickName' => $wxUser->nickname,
            ],
            'hotel' => [
                'hotelName' => ''
            ],
        ]);
    }

    public function actionDebug()
    {
        $this->layout = false;
        return $this->render('debug');
    }

    public function actionDemo()
    {
        return $this->render('demo');
    }

    public function actionHotelList()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $key = urldecode(\Yii::$app->request->get('key', ''));
        $page_num = (int)\Yii::$app->request->get('page_num', 1);
        $city = urldecode(\Yii::$app->request->get('city', 'China'));

        $hotelsArray = Hotel::hotelSearch($key, $page_num, $city);

        return $hotelsArray;
    }


    /**
     * Hotel Details for guest book and check-in
     * @param string $id Hotel ID
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($id)
    {
        if (\Yii::$app->user->isGuest && !\Yii::$app->session->get('BYPASS_LOGIN') && strpos(\Yii::$app->request->getUserAgent(), 'MicroMessenger') !== false ) {
            \Yii::$app->session->set('BYPASS_LOGIN', true);
            $this->redirect(['wechat/oauth/login', 'returnUrl' => \Yii::$app->request->absoluteUrl, 'silent' => 1]);
        }

        $hotel = $this->findHotelModel($id);
        $openId = \Yii::$app->user->isGuest ? null : \Yii::$app->user->id;
        $guest = null;
        if ($openId) {
            //$guest = Guest::latestGuestInfo($id, $openId);
        }
        
        $info = Hotel::hotelInfo($hotel, $guest);

        return $this->render('detail',[
            'hotel' => $hotel,
            //'guest' => $guest,
            'info' => $info,
        ]);
    }

    /**
     * Pay Status
     * @return array
     */
    public function actionPayStatus()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $hotelId = \Yii::$app->request->post('hotel_id');
        $hotel = $this->findHotelModel($hotelId);
        $openId = \Yii::$app->user->id;

        $result = [
            'state' => '0',
            'isjoinhotel' => '2', // TBD
        ];
        
        Hotel::payStatus($hotel, $openId, $result);

        return $result;
    }

    /**
     * Hotel ID
     * @return array
     */
    public function actionHotelId()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $deviceId = \Yii::$app->request->post('deviceId');
        $hotel = $this->findHotelModelByDeviceId($deviceId);

        $result = [
            'state' => '0',
            'hotelName' => $hotel->name,
            'hotelId' => $hotel->_id,
            'companyName' => $hotel->company->name
        ];

        return $result;
    }

    public function actionJoin()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;


        return ;
    }


    /**
     * @param string $id
     * @return null|Hotel
     * @throws NotFoundHttpException
     */
    private function findHotelModel($id)
    {
        $model = Hotel::find()
            ->where(['_id' => $id])
            ->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested hotel does not exist.');
        }
    }

    /**
     * @param string $deviceId
     * @return null|Hotel
     * @throws NotFoundHttpException
     */
    private function findHotelModelByDeviceId($deviceId)
    {
        $hotel = Hotel::find()
            ->where(['deviceId' => $deviceId])
            ->with('company')
            ->one();

        if ($hotel !== null) {
            return $hotel;
        } else {
            throw new NotFoundHttpException('The requested hotel does not exist.');
        }
    }

    /**
     * @param $id
     * @return null|Guest
     * @throws NotFoundHttpException
     */
    private function findGuestModel($id)
    {
        $model = Guest::find()
            ->where(['_id' => $id])
            ->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested guest does not exist.');
        }
    }
}
