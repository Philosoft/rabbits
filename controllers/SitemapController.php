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
            $parser = new \RobotsTxtParser(file_get_contents("{$domain}/robots.txt"));
            $sitemap = new \SimpleXMLElement(file_get_contents("{$domain}/sitemap.xml"));
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
}
