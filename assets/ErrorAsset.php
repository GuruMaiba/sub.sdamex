<?php

namespace app\assets;

use yii\web\AssetBundle;

class ErrorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/error.min.css',
    ];
    public $js = [
        'scripts/libs/parallax.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
