<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 9:30 AM
 */
namespace common\models;

use common\modules\hotel\models\Preference;
use common\modules\hotel\models\Beacon;
use common\modules\wechat\models\WxUser;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;
use common\modules\wechat\models\WxPlatform;

/**
 * This is the model class for table "guest".
 *
 * @property integer $_id
 * @property string $openId  WeChat openId
 * @property string $firstName First Name
 * @property string $lastName Last Name
 * @property string $email Email
 * @property string $mobile Mobile Phone
 * @property string $gender Gender
 * @property string $locale Locale
 * @property string $nationality Nationality
 * @property string $dateofBirthday Date of Birthday
 * @property string $countryCode Country Code
 * @property string $cityName City Name
 * @property string $documentType Document Type
 * @property string $documentNumber Document Number
 * @property string $documentAuthority Document Authority
 * @property string $documentDoe Document Doe
 * @property string $businessAddress Business Address
 * @property string $cityOfBirth City of Birth
 * @property string $countyofBirth Country of Birth
 * @property string $taxIdentificationNumber tax Identification Number
 * @property string $taxOfficeName tax Office Name
 * @property string $nationalityCode nationality Code
 * @property string $type Type
 * @property integer $updatedAt Created at
 * @property integer $createdAt Updated 
 * @property integer $hotelId Hotel ID
 * @property integer $roomId Room ID
 * @property integer $beaconId Id of the Beacon, that recognize this guest
 * @property integer $preferenceId Preference ID
 * @property integer $status Guest Status, it can be 0: not checked-in, 1: waiting for check-in, 2: checked-in, 3: checked-out
 * @property string $guestRoomNumber Guest Room No.
 * @property integer $checkedInTime  Guest checked-In Time
 * @property integer $checkedOutTime Guest checked-Out Time
 * @property integer $loyaltyGrade Loyalty Grade
 * @property integer $disabled disabled
 * @property double $balance Balance
 *
 * @property Preference $preference WeChat Smart Hotel Preference
 * @property Beacon $beacon WeChat Smart Hotel Beacon
 */
class Guest extends ActiveRecord
{
    const STATUS_NOT_CHECKED_IN = 0; // not checked-in
    const STATUS_WAITING_FOR_CHECKED_IN = 1; // checked-in
    const STATUS_CHECKED_IN = 2; // waiting for check-in
    const STATUS_CHECKED_OUT = 3; // checked-out

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'guest';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->status = self::STATUS_NOT_CHECKED_IN;
        $this->disabled = false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstName','lastName', 'mobile', 'openId', 'hotelId', 'roomId'], 'required'],
            [['firstName', 'lastName', 'email'], 'string'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'openId',
            'firstName',
            'lastName',
            'email',
            'mobile',
            'gender',
            'locale',
            'nationality',
            'dateofBirthday',
            'countryCode',
            'cityName',
            'documentType',
            'documentNumber',
            'documentAuthority',
            'documentDoe',
            'businessAddress',
            'cityOfBirth',
            'countyofBirth',
            'taxIdentificationNumber',
            'taxOfficeName',
            'nationalityCode',
            'type',
            'updatedAt',
            'createdAt',
            'hotelId',
            'roomID',
            'preferenceId',
            'status',
            'guestRoomNumber',
            'checkedInTime',
            'checkedOutTime',
            'loyaltyGrade',
            'disabled',
            'balance',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'openId' => 'WeChat openId',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'email' => 'Email',
            'mobile' => 'Mobile Phone',
            'gender' => 'Gender',
            'locale' => 'Locale',
            'nationality' => 'Nationality',
            'dateofBirthday' => 'Date of Birthday',
            'countryCode' => 'Country Code',
            'cityName' => 'City Name',
            'documentType' => 'Document Type',
            'documentNumber' => 'Document Number',
            'documentAuthority' => 'Document Authority',
            'documentDoe' => 'Document Doe',
            'businessAddress' => 'Business Address',
            'cityOfBirth' => 'City of Birth',
            'countyofBirth' => 'Country of Birth',
            'taxIdentificationNumber' => 'Tax Identification Number',
            'taxOfficeName' => 'Tax Office Name',
            'nationalityCode' => 'Nationality Code',
            'type' => 'Type',
            'updatedAt' => 'Updated At',
            'createdAt' => 'Created at',
            'hotelId' => 'Hotel ID',
            'roomId' => 'Room ID',
            'preferenceId' => 'Preference ID',
            'status' => 'Guest status',
            'guestRoomNumber' => 'Guest Room No.',
            'checkedInTime' => 'Guest checked-In Time',
            'checkedOutTime' => 'Guest checked-Out Time',
            'loyaltyGrade' => 'Loyalty Grade',
            'disabled' => 'Disabled',
            'balance' => 'Balance',
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
                'createdAtAttribute' => 'createAt',
                'updatedAtAttribute' => 'updateAt',
            ]
        ];
    }

    /**
     * get status to status map
     * @return array
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_NOT_CHECKED_IN => 'not checked-in',
            self::STATUS_WAITING_FOR_CHECKED_IN => 'checked-in',
            self::STATUS_CHECKED_IN => 'waiting for check-in',
            self::STATUS_CHECKED_OUT => 'checked-out',
        ];
    }

    /**
     * get status info
     * @return string
     */
    public function getStatusInfo()
    {
        $map = self::getStatusMap();
        return $map[$this->status];
    }

    /**
     * create a Guest
     * @param integer $hotelId
     * @param integer $roomId
     * @param integer $beaconId
     * @param string $openId
     * @param string $firstName
     * @param string $lastName
     * @param string $mobile
     * @param integer $status
     * @return Guest|null
     */
    public static function create($hotelId, $roomId, $beaconId, $openId, $firstName, $lastName, $mobile, $status)
    {
        $model = Guest::findOne([
            'hotelId' => $hotelId,
            'roomId' => $roomId,
            'openId' => $openId,
        ]);

        if ($model != null)
        {
            if(($model->status == self::STATUS_WAITING_FOR_CHECKED_IN) || ($model->status == self::STATUS_CHECKED_IN)) {
                return $model;
            }
        }

        $firstName = ltrim($firstName);
        $firstName = chop($firstName);
        $lastName = ltrim($lastName);
        $lastName = chop($lastName);
        $mobile = ltrim($mobile);
        $mobile = chop($mobile);

        $model = new Guest([
            'hotelId' => $hotelId,
            'roomId' => $roomId,
            'beaconId' => $beaconId,
            'openId' => $openId,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'mobile' => $mobile,
        ]);
        if ($model->save(false)) {
            return $model;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * get Guest info by openId and hotelId
     * @param string $openId
     * @param integer $hotelId
     * @return Guest|null
     */
    public static function latestGuestInfo($hotelId, $openId)
    {
        $guest = Guest::find()
            ->where(['openId' => $openId, 'hotelId' => $hotelId])
            ->orderBy(['_id' => SORT_DESC])
            ->one();

        return isset($guest) ? $guest : null;
    }
}