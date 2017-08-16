<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 12/8/16
 * Time: 11:21 AM
 */


namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\modules\hotel\models\Beacon;
use common\modules\hotel\models\Region;
use common\modules\hotel\models\Company;
use common\modules\hotel\models\Venue;
use common\modules\wechat\models\Order;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "hotel".
 *
 * @property integer $_id 
 * @property integer $venueId hotel venue ID
 * @property string $name Hotel Name
 * @property integer $companyId hotel Company ID
 * @property string $province Province
 * @property string $city City
 * @property string $district District
 * @property string $street Street
 * @property integer $createdAt created at
 * @property integer $updatedAt updated at
 * @property integer $quota Room Number of this hotel
 * @property boolean $enabled enabled or not
 * @property boolean $enabledByBackend enabled by backend or not
 * @property string $hotelDescriptions Hotel Descriptions
 * @property string $logo Hotel Logo
 * @property string $deviceId ID of Device, which is bound to the Hotel
 * @property float $longitude Hotel longitude
 * @property float $latitude Hotel latitude
 * @property Venue $venue venue of hotel
 * @property Beacon[] $beacons Beacons in Hotel
 * @property Area[] $areas Areas in Hotel
 * @property Guest[] $guests Guest
 * @property Device $device Device which is bound to the hotel
 * @property RoomType[] $roomTypes Room Types
 * @property int $guestsCount Guest Count
 * @property int $roomTypesCount Room Type Count
 */
class Hotel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'hotel';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->enabled = true;
        $this->enabledByBackend = false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','province', 'city', 'district', 'street'], 'required'],
            [['name','province', 'city', 'district', 'street'], 'string'],
            [['quota'], 'number'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'venueId',
            'name',
            'companyId',
            'province',
            'city',
            'district',
            'street',
            'logo',
            'deviceID',
            'createdAt',
            'updatedAt',
            'enabled',
            'enabledByBackend',
            'hotelDescriptions',
            'quota',
            'guestsCount',
            'longitude',
            'latitude'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'venueId' => 'WeChat Smart Hotel venue ID',
            'name' => 'Hotel Name',
            'companyId' => 'WeChat Smart Hotel Company ID',
            'province' => 'province',
            'city' => 'city',
            'district' => 'district',
            'street' => 'street',
            'logo' => 'Hotel Logo',
            'deviceID' => 'ID of Device, which is bound to the Hotel',
            'createdAt' => 'created at',
            'updatedAt' => 'updated at',
            'enabled' => 'enabled or not',
            'enabledByBackend' => 'enabled by WeChat Smart Hotel or not',
            'hotelDescriptions' => 'Hotel Descriptions',
            'quota' => 'Room Number of this hotel',
            'guestsCount' => 'Guests Count',
            'longitude' => 'longitude',
            'latitude' => 'longitude'
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
     * @return \yii\db\ActiveQueryInterface
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(),['_id' => 'companyId']);
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getGuests()
    {
        return $this->hasMany(Guest::className(), ['hotelId'=>'_id'])->where(['status' => Guest::STATUS_CHECKED_IN])->orderBy((['checkedInTime' => SORT_DESC]));
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(),['_id' => 'deviceId']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * get City Info
     * @return string
     */
    public function getCityInfo()
    {
        $provinceInfo = $this->province;
        if ($provinceInfo != 'Beijing' &&
            $provinceInfo != 'Shanghai' &&
            $provinceInfo != 'Chongqing' &&
            $provinceInfo != 'Tianjin' &&
            $provinceInfo != 'Hongkong' &&
            $provinceInfo != 'Macau') {
            $provinceInfo .= ' ';
        } else {
            $provinceInfo = '';
        }
        $cityInfo = $this->city;

        return $provinceInfo ." ". $cityInfo ." ". $this->district;
    }


    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->cityInfo ." ".  $this->street;
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getRoomTypes()
    {
        return $this->hasMany(RoomType::className(), ['hotelId'=>'_id']);
    }

    /**
     * Has guest booked this hotel?
     * @param Guest $guest
     * @return bool
     */
    public function hasGuest($guest)
    {
        if (!$guest) {
            return false;
        }

        return Hotel::find()->where(['_id' => $guest->hotelId])->exists();
    }

    /**
     * is Hotel full?
     * @return bool
     */
    public function isFull()
    {
        return $this->quota == $this->guestsCount;
    }

    /**
     * is a checked-in guest?
     * @param Guest $guest
     * @param Hotel $hotel
     * @return bool
     */
    public static function isCheckedInGuest($guest, $hotel)
    {
        return $guest->hotelId == $hotel->_id && $guest->status == Guest::STATUS_CHECKED_IN;
    }

    /**
     * search Hotels by Name/City/District
     * @param string $key
     * @param string $page_num
     * @param string $city
     * @return string|array
     */
    public static function hotelSearch($key, $page_num, $city)
    {
        if ($page_num < 1 || empty($page_num)) {
            $page_num = 1;
        }
        $limit = 15;
        $offset = ($page_num - 1) * $limit;
        $hotels = null;
        $where = ['enabled' => true];
        $andFilterWhere = ['or', ['like', 'name', $key], ['like', 'city', $key], ['like', 'district', $key]];
        $orderBy = ['enabled' => SORT_ASC, 'createdAt' => SORT_ASC];

        if ($city) {
            if (preg_match_all('/^(Shanghai|Beijing|Guangzhou)$/', $city)) {
                $where = ['enabled' => true, 'city' => $city];
            }
        }

        $count = Hotel::find()
            ->where($where)
            ->andFilterWhere($andFilterWhere)
            ->count();

        $hotels = Hotel::find()
            ->where($where)
            ->andFilterWhere($andFilterWhere)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($limit)
            ->all();

        $manager_hotel_list = [];
        $manager_hotel = [];
        foreach($hotels as $hotel){
            $manager_hotel['id'] = (string)$hotel->_id;
            $manager_hotel['name'] = $hotel->name;
            $manager_hotel['area'] = $hotel->city." ".$hotel->district;
            $manager_hotel['quota'] = $hotel->quota;
            $manager_hotel['companyName'] = $hotel->company->name;
            $manager_hotel['enabledByBackend'] = $hotel->enabledByBackend;
            $manager_hotel['companyLogo'] = $hotel->company->logo;
            $manager_hotel_list[] = $manager_hotel;
        }
        if (count($manager_hotel_list) < $limit) {
            $nextState = 0;
        } else {
            $nextState = 1;
        }

        return [
            'next_state' => $nextState,
            'state' => '0',
            'msg' => '',
            'join_count' => $count,
            'manager_hotel_list' => $manager_hotel_list,
        ];

    }

    /**
     * Hotel details 
     * @param Hotel $hotel
     * @param Guest $guest
     * @return string|array
     */
    public static function hotelInfo($hotel, $guest)
    {
        $roomTypes = [];
        $certInfo = [];
        $extraGuestOptions = [];
        $checkedIn = false;

        $bannerInfo = [
            '_title' => $hotel->name,
            '_name' => $hotel->company->name,
            '_companyLogo' => $hotel->company->logo,
        ];

        $address = [
            '_full' => $hotel->address,
            '_logitude' => $hotel->longitude,
            '_latitude' => $hotel->latitude,
        ];

        $remainingRoomsCount = $hotel->quota;
        $payingGuestsCount = 1;
        $lbsInfo = [
            '_leftTicks' => $remainingRoomsCount,
            '_payingTicks' => $payingGuestsCount,
            '_allTicks' => $hotel->quota,
            '_address' => $address,
        ];

        if ($hotel->enabled) {
            $tempRoomTypes = $hotel->roomTypes;
            foreach ($tempRoomTypes as $roomType) {
                $temp = [
                    '_id' => (string)$roomType->_id,
                    '_roomTypePrice' => $roomType->unitPrice,
                    '_name' => $roomType->name,
                    '_remainingCount' => $roomType->remainingCount,
                    '_payingTicks' => $roomType->payingCount,
                    '_allTicks' => $roomType->quota,

                ];
                array_push($roomTypes, $temp);
            }
        }

        $hasRoomType = false;
        if ($roomTypes) {
            $hasRoomType = true;
        }

        $recordInfo = [
            '_name' => $guest ? $guest->name : '',
            '_mobile' => $guest ? $guest->mobile : '',
        ];
        $otherInfo = [
            '_id' => (string)$hotel->_id,
            '_type' => "hotel",
            '_join' => $hotel->hasGuest($guest),
            '_state' => $hotel->enabled,
            '_hasRoomType' => $hasRoomType,
            '_verify' => "0",
            '_mobileVerify' => "0",
            '_appVersion' => "",
            '_isFull' => $hotel->isFull(),
        ];

        if ($guest) {
            $isJoined = $hotel->hasGuest($guest);
            $checkedIn = Hotel::isCheckedInGuest($guest, $hotel);
        }

        $hotelInfo = [
            '_isFull' => $hotel->isFull(),
            '_checkedIn' => $checkedIn,
        ];

        return [
            '_bannerInfo' => $bannerInfo,
            '_lbsInfo' => $lbsInfo,
            '_hotelDescriptions' => $hotel->hotelDescriptions,
            '_extraGuestOptions' => $extraGuestOptions,
            '_roomTypes' => $roomTypes,
            '_recordInfo' => $recordInfo,
            '_otherInfo' => $otherInfo,
            '_hotelInfo' => $hotelInfo,
            '_certInfo' => $certInfo,
        ];
    }

    /**
     * Guest Pay Status
     * @param Hotel $hotel
     * @param string $openId
     * @param array $result
     * @return string|array
     */
    public static function payStatus($hotel, $openId, $result)
    {
        /* @var Room $room */
        $room = Room::find()
            ->where(['hotelId' => $hotel->_id, 'openId' => $openId ])
            ->with('order')
            ->orderBy(['_id' => SORT_DESC])
            ->one();
        if ($room &&
            $room->order &&
            $room->order->status == Order::STATUS_WAIT_TO_PAY) {
            $order = $room->order;
            if ($order->status == Order::STATUS_WAIT_TO_PAY ) {
                $result['orderState'] = '2'; // 已锁定
                $result['roomTypeId'] = (string)$room->roomTypeId; // Room Type ID
                $result['payOrderId'] = (string)$order->_id; // Order IDs
                $result['money'] = $order->total;
                $result['timeLeft'] = $order->getSecondsRemaining();
            } else {
                $result['orderState'] = '1'; // 未锁定
            }
        } else {
            $result['orderState'] = '1'; // 未锁定
        }
    }
}
