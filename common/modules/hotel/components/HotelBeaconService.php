<?php

namespace common\modules\hotel\components;


use common\modules\hotel\models\Company;
use common\modules\hotel\models\HotelApp;
use common\modules\hotel\models\Venue;
use yii\httpclient\Client;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class HotelBeaconService
{
    const MAX_RETRY_COUNT = 2;


    /**
     * request by URL for JSON return
     * @param string $url
     * @param array $data
     * @param int $retryCount retry count
     * @return array
     */
    private function getJSONOfUrl($url, $data = null, $retryCount = 0)
    {
        \Yii::info('Request: ' . $url);
        $client = new Client();
        $request = $client->createRequest()->setUrl($url);
        if ($data) {
            $request->setMethod('POST');
            $request->setFormat(Client::FORMAT_XML);
            $request->setHeaders(['Content-Type' => 'application/json']);
            \Yii::info('Post data: ' . VarDumper::dumpAsString($data));
            if (is_string($data)) {
                $request->setContent($data);
            } else {
                $request->setData($data);
            }
        }
        try {
            $response = $request->send();
        } catch (Exception $ex) {
            if ($retryCount < self::MAX_RETRY_COUNT) {
                \Yii::warning('Request failed, retry count ' . $retryCount . ': ' . VarDumper::dumpAsString($ex));
                return self::getJSONOfUrl($url, $data, $retryCount + 1);
            } else {
                \Yii::error('Request failed, exceed max retry count: ' . VarDumper::dumpAsString($ex));
                return null;
            }
        }

        if ($response->isOk) {
            $data = $response->data;
            \Yii::info('Response: ' . VarDumper::dumpAsString($data));
            return $data;
        }
        return null;
    }

    /**
     * get access_token of this hotel App
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public function getAccessToken($appId, $appSecret)
    {
        $params = [
            'grant_type' => 'client_credential',
            'appid' => $appId,
            'secret' => $appSecret,
        ];
        $url = 'https://app.hotel.com/api/app/token?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * get Beacon info by UUID
     * @param string $accessToken
     * @param string $uuid
     * @param string $major
     * @param string $minor
     * @return array (detailed Beacon Info)
     */
    public function getBeaconInfo($accessToken, $uuid, $major, $minor)
    {
        $params = [
            'uuid' => $uuid,
            'major' => $major,
            'minor' => $minor
        ];
        $url = "https://app.hotel.com/api/beacon/beacon/get?access_token=" . $accessToken;
        return $this->getJSONOfUrl($url, Json::encode($params));
    }

    /**
     * api interface to get company info by beacon ID
     * @param string $access_token
     * @param string $beaconId
     * @return array (detailed Company Info)
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function getCompanyInfo($access_token, $beaconId)
    {
        $url = "https://app.hotel.com/api/beacon/companyinfo/get?access_token=" . $access_token;
        $data = [
            'beacon_id' => $beaconId,
        ];
        $res = $this->getJSONOfUrl($url, $data);
        return json_encode($res);
    }

    /**
     * get Venue info by Beacon ID
     * @param string $access_token
     * @param string $beaconId
     * @return array (detailed Venue Info)
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function getVenueInfo($access_token, $beaconId)
    {
        $url = "https://app.hotel.com/api/beacon/venueinfo/get?access_token=" . $access_token;
        $data = [
            'beacon_id' => $beaconId,
        ];
        return  $this->getJSONOfUrl($url, $data);
    }

    /**
     * get Region info by Beacon ID
     * @param string $access_token
     * @param string $beaconId
     * @return array (detailed Region info)
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function getRegionInfo($access_token, $beaconId)
    {
        $url = "https://app.hotel.com/api/beacon/regioninfo/get?access_token=" . $access_token;
        $data = [
            'beacon_id' => $beaconId,
        ];
        return  $this->getJSONOfUrl($url, $data);
    }
} 
