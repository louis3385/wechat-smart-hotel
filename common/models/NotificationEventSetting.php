<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 10:55 AM
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

class NotificationEventSetting extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'notification_event_settings';
    }

}
