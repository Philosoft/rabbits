<?php


namespace app\assets;


use yii\web\AssetBundle;

class LoadersAsset extends AssetBundle
{
    public $sourcePath = "@app/node_modules/loaders.css";

    public $css = [
        "loaders.min.css"
    ];
}