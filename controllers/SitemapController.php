<?php

namespace app\controllers;

use GuzzleHttp\Client;
use \yii\web\Controller;
use Yii;

class SitemapController extends Controller
{
    const STATUS_OK = "ok";
    const STATUS_ERROR = "error";

    const REASON_ROBOTS = 0;
    const REASON_META_ROBOTS = 1;
    const REASON_NOT_200 = 2;

    public $enableCSRFValidation = false;

    private $disallowed = [];

    public function actionCheck()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $domain = Yii::$app->request->post("domain", null);
        $status = self::STATUS_OK;
        $message = "";
        $guzzle = new Client();
        if ($domain !== null) {
            $url = $this->normalizeUrl($domain);
            try {
                $parser = new \RobotsTxtParser(file_get_contents("{$url}robots.txt"));
                $sitemap = new \SimpleXMLElement(file_get_contents("{$url}sitemap.xml"));
                foreach ($sitemap as $location) {
                    $currentUrl = trim((string)$location->loc);
                    $guzzleClientError = false;
                    try {
                        $response = $guzzle->request(
                            "GET",
                            $currentUrl,
                            [
                                "allow_redirects" => false
                            ]
                        );
                    } catch (\GuzzleHttp\Exception\ClientException $clientException) {
                        $guzzleClientError = true;
                    }

                    if ($guzzleClientError === true || $response->getStatusCode() != 200) {
                        $this->addDisallowedUrl($currentUrl, self::REASON_NOT_200);
                    } else {
                        $content = \phpQuery::newDocumentHTML($response->getBody()->getContents());
                        $metaRobots = $content->find("meta[name='robots']");
                        if (!empty($metaRobots)) {
                            foreach ($metaRobots as $meta) {
                                $metaContent = pq($meta)->attr("content");
                                $metaContent = explode(",", $metaContent);
                                $metaContent = array_map("trim", $metaContent);
                                foreach ($metaContent as $metaValue) {
                                    if (in_array($metaValue, ["noindex", "nofollow"])) {
                                        $this->addDisallowedUrl($currentUrl, self::REASON_META_ROBOTS);
                                    }
                                }
                            }
                        }
                    }

                    if ($parser->isDisallowed($currentUrl)) {
                        $this->addDisallowedUrl($currentUrl);
                    }
                }

            } catch (\Exception $e) {
                $status = self::STATUS_ERROR;
                $message = $e->getMessage();
            }

        }
        return [
            "status" => $status,
            "data" => $this->disallowed,
            "message" => $message
        ];
    }

    public function checkUrl($url = "")
    {
        if (empty($url)) {
            return false;
        }
    }

    private function normalizeUrl($url = "")
    {
        $parts = parse_url($url);
        $host = !empty($parts["host"]) ? $parts["host"] : "";
        $scheme = !empty($parts["scheme"]) ? $parts["scheme"] : "http";
        $path = !empty($parts["path"]) ? "{$parts["path"]}" : "";

        return "{$scheme}://{$host}{$path}/";
    }

    private function addDisallowedUrl($url, $reason = self::REASON_ROBOTS)
    {
        $this->disallowed[] = [
            "url" => $url,
            "reason" => $reason
        ];
    }
}
