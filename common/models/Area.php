<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 11:27 AM
 */

namespace common\models;

use common\modules\hotel\models\Beacon;
use yii\behaviors\TimestampBehavior;
use common\modules\hotel\models\Region;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;


/**
 * This is the model class for table "areas".
 *
 * @property integer $_id
 * @property integer $regionId  Region ID
 * @property integer $hotelId  Hotel ID
 * @property integer $updatedAt Created at
 * @property integer $createdAt Updated at
 * @property Hotel $hotel Hotel
 */


class Area extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'areas';
    }

}