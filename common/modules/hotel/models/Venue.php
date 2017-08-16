<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 10:41 AM
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
 * @property string $name Name
 * @property string $phone Phone
 * @property string $major Major
 * @property string $welcomeMessage Welcome Message
 * @property string $farewellMessage Farewell Message
 * @property string $status Status
 * @property integer $companyId Company ID
 * @property string $accountType Account Type
 * @property string $photo Photo
 * @property string $website Website
 * @property string $email Email
 * @property integer $starNumber Star Number
 * @property float $latititude latititude
 * @property float $longitude longitude
 * @property string $cityName City Name
 * @property string $streetName Street Name
 * @property string $zip ZIP
 * @property string $countryCode Country Code
 * @property string $fastCheckinEmail Fast Check-in Email
 * @property string $paylevenMerchantToken Payleven Merchant Token
 * @property boolean $paymentEnabled Payment Enabled or not
 * @property string $logo Logo
 * @property boolean $public Public or not
 * @property boolean $cityTaxReady City Tax Ready or not
 * @property integer $offlineAlarmSentAt offline Alarm Sent At
 * @property boolean $pmsIntegrated pms Integrated or not
 * @property string $currency Currency
 * @property boolean $expressCheckin express Check-in or not
 * @property string $keyProvider Key Provider
 * @property string $timeZone Time Zone
 * @property integer $expiredCheckoutEmailHour expired Checkout Email Hour
 * @property integer $precheckinEmailHour pre-checkin Email Hour
 * @property integer $updatedAt Created at
 * @property integer $createdAt Updated
 */

class Venue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'venues';
    }

}