<?php

namespace app\controllers;

use Yii;
// use app\models\course\{Course, Module, Lesson};
use yii\web\NotFoundHttpException;

class FunController extends AppController
{
    public function actionIndex()
    {
        return $this->render('index');
        // return $this->render('index', [
        //     'model' => Course::find()->asArray()->all(),
        // ]);
    }
}
