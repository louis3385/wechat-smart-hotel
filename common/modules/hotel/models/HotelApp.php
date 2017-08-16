<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/9/16
 * Time: 5:27 PM
 */

namespace common\modules\hotel\models;

use common\modules\hotel\components\HotelBeaconService;
use common\modules\hotel\components\HotelAppService;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for collection "hotel_app".
 *
 * @property integer $_id
 * @property string $uniqueId Unique ID
 * @property string $name Hotel or company Name
 * @property string $callbackToken Callback Access Token
 * @property string $appId hotel App ID
 * @property string $appSecret hotel App Secret
 * @property string $accessToken Access token by hotel
 * @property int $expiresAt Access Token expired at
 */
class HotelApp extends ActiveRecord
{
    /**
     * @var bool refresh access token by force or not
     */
    private $accessTokenRefreshed = false;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'hotel_apps';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'uniqueId', 'appId', 'appSecret'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'name',
            'uniqueId',
            'number',
            'callbackToken',
            'appId',
            'appSecret',
            'accessToken',
            'expiresAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->callbackToken = md5(microtime());
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return HotelApp
     */
    public static function getFromHost()
    {
        /*$hostInfo = Yii::$app->request->hostInfo;
        if (preg_match('/^http:\/\/([\w\-]+)\.\w+\.\w+$/', $hostInfo, $matches)) {
            return self::findOne(['number' => $matches[1]]);
        }
        return null;*/
        return self::find()->one();
    }

    /**
     * get valid access token of this app
     * @return string
     */
    public function getValidAccessToken()
    {
        if (!$this->accessToken || $this->expiresAt <= time()) {
            $this->refreshAccessToken();
        }
        return $this->accessToken;
    }

    /**
     * refresh access token by force
     */
    private function refreshAccessToken()
    {
        $result = (new HotelAppService())->getAccessToken($this->appId, $this->appSecret);
        if ($result && isset($result['access_token'])) {
            $this->accessToken = $result['access_token'];
            $this->expiresAt = time() + $result['expires_in'];
            $this->save();
        } else {
            \Yii::error('Get access token failed: ' . VarDumper::dumpAsString($result));
        }
    }

    /**
     * get hotel App Info
     * @param string $openId
     * @return array
     */
    public function getAppInfo($openId)
    {
        $result = (new HotelAppService())->getAppInfo($this->getValidAccessToken(), $openId);
        if (isset($result['errcode']) && $result['errcode'] == 40001) {
            \Yii::warning($result['errmsg']);
            $this->refreshAccessToken();
            return $this->getAppInfo($openId);
        }
        return $result;
    }

}
