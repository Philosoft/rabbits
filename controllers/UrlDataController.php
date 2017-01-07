<?php

namespace app\controllers;

use yii\helpers\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\TransferStats;

class UrlDataController extends \yii\web\Controller
{
    protected $phpQuery = null;

    public function actionUrlInfo($url = '')
    {
        $url = urldecode($url);
        $urlData = [];
        $urlData['url'] = $url;
        
        if (empty($url) === true) {
            $urlData['status'] = 'error';
            $urlData = Json::encode($urlData);
            return $urlData;
        }

        $client = new Client();
        try {
            $resultData = $client->request(
                'GET', 
                $url,
                [
                    'allow_redirects' => true,
                    'http_errors' => false,
                    'timeout' => 5,
                    'decode_content' => false
                ]
            );
            $urlBody = $resultData->getBody()->getContents();
            $contentType = $resultData->getHeader('content-type');
            $textEncoding = 'UTF-8';
            if (empty($contentType) === false && is_array($contentType) === true) {
                preg_match("#charset=(.*)(\s|$)#isU", $contentType[0], $encoding);
                if (is_array($encoding) === true && empty($encoding[1]) === false) {
                    $textEncoding = $encoding[1];
                    $isEncodingNotUtf = (strcasecmp($textEncoding, 'UTF-8') !== 0);
                    if ($isEncodingNotUtf === true) {
                        $urlBody = iconv($textEncoding, 'UTF-8', $urlBody);
                        $urlBody = str_replace($textEncoding, 'UTF-8', $urlBody);
                    }
                }
            }

            $this->phpQuery = \phpQuery::newDocumentHTML($urlBody);
            $urlData['title'] = $this->getContentAttr($this->phpQuery->find('title'));
            $urlData['h1'] = $this->getContentAttr($this->phpQuery->find('h1'));
            $urlData['description'] = $this->getMetaAttr('meta', 'name', 'description', 'content');
            $urlData['keywords'] = $this->getMetaAttr('meta', 'name', 'keywords', 'content');
            $urlData['canonical'] = $this->getMetaAttr('link', 'rel', 'canonical', 'href');
            $urlData['status'] = 'success';
        } catch (RequestException $exception) {
            $urlData['status'] = 'error';
        }

        $urlData = Json::encode($urlData, JSON_UNESCAPED_UNICODE);
        return $urlData;
    }

    public function actionUrlResponse($url = '')
    {
        $url = urldecode($url);
        $urlData = [];
        $urlData['url'] = $url;
        
        if (empty($url) === true) {
            $urlData['status'] = 'error';
            $urlData = Json::encode($urlData);
            return $urlData;
        }

        $client = new Client();
        try {
            $resultData = $client->request(
                'GET', 
                $url,
                [
                    'allow_redirects' => false,
                    'http_errors' => false,
                    'timeout' => 5,
                ]
            );
            $urlData['status'] = 'success';
            $urlData['statusCode'] = $resultData->getStatusCode();
            $urlData['redirectsNumber'] = -1;
            $resultData = $client->request(
                'GET', 
                $url,
                [
                    'allow_redirects' => true,
                    'http_errors' => false,
                    'timeout' => 5,
                    'on_stats' => function (TransferStats $stats) use (&$urlData) {
                        $urlData['finalUri'] = (string) $stats->getEffectiveUri();
                        $urlData['redirectsNumber']++;
                    }
                ]
            );
            $urlData['finalStatusCode'] = $resultData->getStatusCode();
        } catch (RequestException $exception) {
            $urlData['status'] = 'error';
        }

        $urlData = Json::encode($urlData);
        return $urlData;
    }

    protected function getContentAttr($attrArray) 
    {
        if (empty($attrArray) === true) {
            return [];
        }

        $contentArray = [];
        foreach ($attrArray as $attr) {
            $attrText = strip_tags(pq($attr)->text());
            if (preg_match("#(var|script)#iusU", $attrText) === 0) {
                $contentArray[] = $attrText;
            }
        }

        return $contentArray;
    }

    protected function getMetaAttr($metaName, $attrName, $attrValue, $needAttr)
    {
        if (empty($this->phpQuery) === true) {
            return [];
        }
        
        $metaContent = [];
        $metaArray = $this->phpQuery->find("{$metaName}[{$attrName}={$attrValue}]");
        if (empty($metaArray) === true) {
            return [];
        }

        foreach ($metaArray as $meta) {
            $metaText = strip_tags(pq($meta)->attr($needAttr));
            if (preg_match("#(var|script)#iusU", $metaText) === 0) {
                $metaContent[] = $metaText;
            }
        }

        return $metaContent;
    }
}
