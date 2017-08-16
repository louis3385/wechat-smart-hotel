<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 10:58 AM
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

class NotificationTimeSetting extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'notification_time_settings';
    }

}