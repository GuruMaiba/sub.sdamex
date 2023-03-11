<?php

namespace app\modules\personal\controllers;
use Yii;

use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use app\controllers\AppController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class NotificationController extends AppController
{
    public function actionIndex()
    {
        // $gmt = ($_COOKIE['GMT'])
        return $this->render('index');
    }
}
