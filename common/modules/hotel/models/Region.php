<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 10:43 AM
 */

namespace common\modules\hotel\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "venues".
 *
 * @property integer $_id
 * @property integer $venueId Venue ID
 * @property string $name Name
 * @property string $requiredRole required Role
 * @property boolean $isCheckinEnabled is Checkin Enabled or not
 * @property integer $updatedAt Created at
 * @property integer $createdAt Updated at
 */

class Region extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'regions';
    }

}