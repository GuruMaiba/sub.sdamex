<?php

namespace app\controllers;

use Yii;
// use app\models\course\{Course, Module, Lesson};
use app\controllers\AppController;

class DefaultController extends AppController
{
    public function actionIndex()
    {
        
        return $this->render('index', [ ]);
    }
}
