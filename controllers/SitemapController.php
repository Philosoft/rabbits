<?php

namespace app\controllers;

use \yii\web\Controller;
use Yii;

class SitemapController extends Controller
{
    public $enableCSRFValidation = false;

    public function actionCheck()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $domain = Yii::$app->request->post("domain", null);
        if ($domain !== null) {
            $url = $this->normalizeUrl($domain);
            $parser = new \RobotsTxtParser(file_get_contents("{$url}robots.txt"));
            $sitemap = new \SimpleXMLElement(file_get_contents("{$url}sitemap.xml"));
            $disallowed = [];
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

            return $disallowed;
        }
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
