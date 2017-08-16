<?php

namespace common\modules\wechat\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "wx_notification".
 *
 * @property integer $_id
 * @property string $toUserName
 * @property string $fromUserName
 * @property integer $createTime
 * @property string $msgType
 * @property string $deviceType
 * @property string $deviceId
 * @property string $msgId
 * @property string $sessionId
 * @property string $openId
 * @property string $content
 * @property WxUser $wxUser
 */
class WxNotification extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['toUserName', 'fromUserName', 'createTime', 'msgType', 'deviceType', 'deviceId'], 'required'],
            [['createTime'], 'integer'],
            [['toUserName', 'fromUserName', 'msgType'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'toUserName' => 'To User Name',
            'fromUserName' => 'From User Name',
            'createTime' => 'Create Time',
            'msgType' => 'Msg Type',
            'deviceType' => 'Device Type',
            'deviceId' => 'Device ID',
            'msgId' => 'Msg ID',
            'sessionId' => 'Session ID',
            'openId' => 'WeChat OpenID',
            'content' => 'Content'
        ];
    }

    /**
     * @return WxUser|null
     */
    public function getWxUser()
    {
        $wxPlatform = WxPlatform::findOne(['uniqueId' => $this->toUserName]);
        return $wxPlatform->fetchWxUser($this->fromUserName);
    }
}
