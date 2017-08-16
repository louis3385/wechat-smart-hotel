<?php

namespace common\modules\hotel\components;


use common\modules\hotel\models\HotelApp;
use yii\httpclient\Client;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class HotelAppService
{
    const MAX_RETRY_COUNT = 2;

    /**
     * 请求url并返回JSON
     * @param string $url
     * @param array  $data
     * @param int $retryCount 已重试次数
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
     * get access_token of hotel App by App ID and App secret
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public function getAccessToken($appId, $appSecret)
    {
        $params = [
            'grant_type' => 'client_credential',
            'app_id' => $appId,
            'secret' => $appSecret,
        ];
        $url = 'https://app.hotel.com/api/app/token?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }

    /**
     * get App account info
     * @param string $accessToken
     * @param string $appId
     * @return array
     */
    public function getAppInfo($accessToken, $appId)
    {
        $params = [
            'access_token' => $accessToken,
            'app_id' => $appId,
        ];
        $url = 'https://app.hotel.com/api/app/info?' . http_build_query($params);
        return $this->getJSONOfUrl($url);
    }
    
}
