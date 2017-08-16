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
 * This is the model class for table "company".
 *
 * @property integer $_id
 * @property string $firstName First Name
 * @property string $lastName Last Name
 * @property string $email Email
 * @property string $name Name
 * @property string $phone Phone
 * @property string $countryCode Country Code
 * @property string $logo Company Logo
 * @property string $zipCode Zip Code
 * @property string $cityName City Name
 * @property string $streetName Street Name
 * @property string $vatId Vat ID
 * @property string $accountInformation Account Information
 * @property string $accountEmail Account Email
 * @property string $directorInfo Director Info
 * @property string $otherStakeholdersPresent Other Stakeholders Present
 * @property integer $updatedAt Created at
 * @property integer $createdAt Updated at
 */
class Company extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstName', 'lastName'], 'required'],
            [['name', 'lastName'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'firstName',
            'lastName',
            'email',
            'name',
            'phone',
            'countryCode',
            'logo',
            'zipCode',
            'cityName',
            'streetName',
            'vatId',
            'accountInformation',
            'accountEmail',
            'directorInfo',
            'otherStakeholdersPresent',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'email' => 'Email',
            'name' => 'Company Name',
            'phone' => 'Phone',
            'countryCode' => 'Country Code',
            'logo' => 'Company Logo',
            'zipCode' => 'ZIP Code',
            'cityName' => 'City Name',
            'streetName' => 'Street Name',
            'vatId' => 'Vat ID',
            'accountInformation' => 'Account Information',
            'accountEmail' => 'Account Email',
            'directorInfo' => 'Director Info',
            'otherStakeholdersPresent' => 'Other Stakeholders Present',
            'createdAt' => 'created at',
            'updatedAt' => 'updated at',
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

}