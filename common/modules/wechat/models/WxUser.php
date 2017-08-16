<?php

namespace common\modules\wechat\models;

use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for collection "wx_user".
 *
 * @property integer $_id
 * @property integer $platformId 所属公众账号ID
 * @property string $openId 用户的标识，对当前公众号唯一
 * @property string $accessToken 接口调用凭证
 * @property int $expiresAt access_token 接口调用凭证超时时间
 * @property string $refreshToken 用户刷新 access_token
 * @property string $authKey "remember me" authentication key
 * @property string $nickname 用户的昵称
 * @property int $sex 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
 * @property string $city 用户所在城市
 * @property string $country 用户所在国家
 * @property string $province 用户所在省份
 * @property string $language 用户的语言，简体中文为zh_CN
 * @property string $headImgUrl 用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
 * @property bool $subscribe 用户是否已关注
 * @property int $subscribeTime 用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
 * @property string $mobile 手机号
 * @property int $updateTime 更新时间
 * @property int $createTime 创建时间
 * @property WxPlatform $platform
 * @property string $sexInfo 性别信息
 */
class WxUser extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'wx_user';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->subscribe = false;

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'platformId',
            'openId',
            'accessToken',
            'expiresAt',
            'refreshToken',
            'authKey',
            'nickname',
            'sex',
            'city',
            'country',
            'province',
            'language',
            'headImgUrl',
            'subscribe',
            'subscribeTime',
            'mobile',
            'updateTime',
            'createTime',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'platformId' => '所属公众号',
            'openId' => '微信OpenID',
            'nickname' => '昵称',
            'sexInfo' => '性别',
            'city' => '所在城市',
            'country' => '所在国家',
            'province' => '所在省份',
            'language' => '语言',
            'headImgUrl' => '头像',
            'subscribe' => '已关注',
            'subscribeTime' => '关注时间',
            'mobile' => '手机号',
            'updateTime' => '更新时间',
            'createTime' => '创建时间',
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
     * @return string
     */
    public function getSexInfo()
    {
        switch ($this->sex) {
            case 1:
                return '男';
            case 2:
                return '女';
            default:
                return '未知';
        }
    }

    /**
     * 获取当前已登录的用户
     * @return null|WxUser
     */
    public static function current()
    {
        return static::findIdentity(\Yii::$app->user->id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['openId' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->openId;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->authKey = \Yii::$app->getSecurity()->generateRandomString();
                $this->updateUserInfo();
            }
            return true;
        }
        return false;
    }

    /**
     * 更新用户基本信息
     */
    public function updateUserInfo()
    {
        $userInfo = $this->platform->getUserInfo($this->openId);
        if ($userInfo && isset($userInfo['subscribe']) && $userInfo['subscribe']) {
            $this->nickname = $userInfo['nickname'];
            $this->sex = $userInfo['sex'];
            $this->city = $userInfo['city'];
            $this->country = $userInfo['country'];
            $this->province = $userInfo['province'];
            $this->language = $userInfo['language'];
            $this->headImgUrl = $userInfo['headimgurl'];
            $this->subscribe = true;
            if (isset($userInfo['subscribe_time'])) {
                $this->subscribeTime = $userInfo['subscribe_time'];
            }
        }
    }

    /**
     * 更新手机号
     * @param string $mobile
     */
    public function updateMobile($mobile)
    {
        if (!$mobile || $this->mobile == $mobile) {
            return;
        }
        $this->mobile = $mobile;
        $this->save();
    }

    /**
     * 更新某用户的关注状态
     * @param string $openid
     * @param bool $subscribe
     * @return bool
     */
    public static function updateSubscribe($openid, $subscribe)
    {
        $user = self::findIdentity($openid);
        if (!$user) {
            return false;
        }
        $user->subscribe = $subscribe;
        return $user->save(false);
    }
}
