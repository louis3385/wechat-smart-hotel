<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 10:54 AM
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\modules\hotel\models\PushNotification;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

class Notification extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'notifications';
    }

}