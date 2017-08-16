<?php

namespace common\modules\hotel\components;


use common\modules\hotel\models\HotelApp;
use common\modules\hotel\models\Beacon;
use yii\httpclient\Client;
use yii\base\Exception;
use yii\helpers\Url;
use \Yii;
use yii\helpers\VarDumper;

class HotelOauthClient {

    /**
     * @var HotelApp
     */
    private $app;

    /**
     * @param HotelApp $app
     */
    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * 请求url并返回JSON
     * @param $url
     * @return array
     */
    private function getJSONOfUrl($url) {
        \Yii::info('URL: ' . $url);
        $client = new Client();
        try {
            $response = $client->get($url)->send();
        } catch (Exception $ex) {
            Yii::error('Request failed, exception: '. VarDumper::dumpAsString($ex));
            return null;
        }
        if ($response->isOk) {
            $body = $response->data;
            Yii::info('Response: ' . VarDumper::dumpAsString($body));
            return $body;
        }
        return null;
    }


} 
