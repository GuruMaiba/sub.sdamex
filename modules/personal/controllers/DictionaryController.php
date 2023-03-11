<?php

namespace app\modules\personal\controllers;
use Yii;

// use yii\base\InvalidParamException;
// use yii\web\BadRequestHttpException;
use app\controllers\AppController;
// use yii\filters\VerbFilter;
// use yii\filters\AccessControl;

class DictionaryController extends AppController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
