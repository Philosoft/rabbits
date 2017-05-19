<?php

namespace app\controllers;

use \yii\web\Controller;
use Yii;

class SitemapController extends Controller
{
    const STATUS_OK = "ok";
    const STATUS_ERROR = "error";

    public $enableCSRFValidation = false;

    public function actionCheck()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $domain = Yii::$app->request->post("domain", null);
        $disallowed = [];
        $status = self::STATUS_OK;
        $message = "";
        if ($domain !== null) {
            $url = $this->normalizeUrl($domain);
            try {
                $parser = new \RobotsTxtParser(file_get_contents("{$url}robots.txt"));
                $sitemap = new \SimpleXMLElement(file_get_contents("{$url}sitemap.xml"));
                foreach ($sitemap as $location) {
                    if ($parser->isDisallowed($location->loc)) {
                        $disallowed[] = $location->loc;
                    }
                }

                if (!empty($disallowed)) {
                    $disallowed = array_map(
                        function ($url) {
                            return (string)$url;
                        },
                        $disallowed
                    );
                }
            } catch (\Exception $e) {
                $status = self::STATUS_ERROR;
                $message = $e->getMessage();
            }

        }
        return [
            "status" => $status,
            "data" => $disallowed,
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
}
