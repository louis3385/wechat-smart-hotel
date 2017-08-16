<?php

namespace common\modules\wechat\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auto_reply".
 *
 * @property integer $_id
 * @property integer $platformId
 * @property string $content
 * @property integer $updateTime
 * @property integer $createTime
 *
 * @property WxPlatform $platform
 */
class AutoReply extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_reply';
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['platformId', 'content'], 'required'],
            [['platformId'], 'integer'],
            [['content'], 'string'],
            [['platformId'], 'exist', 'skipOnError' => true, 'targetClass' => WxPlatform::className(), 'targetAttribute' => ['platformId' => '_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'platformId' => 'Platform ID',
            'content' => 'Content',
            'updateTime' => 'Update Time',
            'createTime' => 'Create Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlatform()
    {
        return $this->hasOne(WxPlatform::className(), ['_id' => 'platformId']);
    }
}
