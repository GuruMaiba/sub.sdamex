<?php

namespace app\assets;

use yii\web\AssetBundle;

class PersonalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/normalize.css',
        'css/icons.css',
        'css/personal.min.css',
    ];
    public $js = [
        'scripts/common.js',
        // 'scripts/libs/jquery.mCustomScrollbar.concat.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        // 'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
    ];
}
