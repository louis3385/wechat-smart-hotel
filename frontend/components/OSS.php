<?php
/**
 * Created by PhpStorm.
 * User: DongpoLiu
 * Date: 4/26/16
 * Time: 12:09 PM
 */

namespace frontend\components;


use OSS\Core\OssException;
use OSS\OssClient;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\httpclient\Client;
use yii\web\UploadedFile;

class OSS extends Component
{
    public $accessKeyId;

    public $accessKeySecret;

    public $endpoint;

    public $isCName = false;

    public $bucket;

    /**
     * @return OssClient
     */
    public function getClient()
    {
        try {
            return new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, $this->isCName);
        } catch (OssException $e) {
            \Yii::error($e->getErrorMessage());
        }
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveUploadedFile($file)
    {
        if (!($file instanceof UploadedFile)) {
            return null;
        }
        return $this->putFile($file->tempName);
    }

    /**
     * 保存远程文件
     * @param $url
     * @return null|string
     */
    public function saveRemoteFile($url)
    {
        $client = new Client();
        try {
            $response = $client->get($url)->send();
        } catch (Exception $ex) {
            \Yii::error($ex->getMessage());
            return null;
        }
        $content = $response->content;
        $contentType = $response->headers['Content-Type'];
        return $this->putContent($content, $contentType);
    }

    /**
     * @param string $filename
     * @return string
     */
    public function putFile($filename)
    {
        $content = file_get_contents($filename);
        $contentType = FileHelper::getMimeType($filename);
        return $this->putContent($content, $contentType);
    }

    /**
     * @param string $content
     * @param string $contentType
     * @return string
     */
    public function putContent($content, $contentType)
    {
        $object = \Yii::$app->getSecurity()->generateRandomString();
        $expires = (60 * 60 * 24 * 365);
        $headers = [
            'Expires' => gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT',
            'Cache-Control' => "max-age=" . $expires . ', public',
            'Content-Type' => $contentType,
        ];
        try {
            $this->getClient()->putObject($this->bucket, $object, $content, ['headers' => $headers]);
        } catch (OssException $e) {
            \Yii::error($e->getErrorMessage());
        }
        if ($this->isCName) {
            return "http://{$this->endpoint}/$object";
        } else {
            return "http://{$this->bucket}.{$this->endpoint}/$object";
        }
    }
}