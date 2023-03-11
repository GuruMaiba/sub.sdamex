<?php

namespace app\assets;

use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/normalize.css',
        'css/icons.css',
        'css/main.min.css',
        'css/jquery.mCustomScrollbar.min.css',
    ];
    public $js = [
        // 'scripts/libs/jquery.mCustomScrollbar.concat.min.js',
        'scripts/common.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        // 'yii\web\YiiAsset',
    ];
}
