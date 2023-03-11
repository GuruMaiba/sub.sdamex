<?php

namespace app\controllers;

use Yii;
use app\models\Teacher;

class TeachersController extends AppController
{
    public function actionIndex()
    {
        $short = Yii::$app->params['shortName'];
        $lable = Yii::$app->params['listSubs'][Yii::$app->params['subInx']]['lable'];
        $this->view->title = "Онлайн-репетиторы по подготовке к экзаменам ОГЭ и ЕГЭ ".date('Y')." | ".strtoupper($lable)." / $short";
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => "Специально отобранные репетиторы, учителя, преподаватели, мастера своего дела, которые подробнейшим образом разъяснят задание по предмету - $lable"
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'онлайн, репетитор, учитель, преподаватель, подготовка, экзамены, ОГЭ, ЕГЭ, '.$lable.', '.Yii::$app->params['commonKeyWords'],
        ]);

        return $this->render('index', [
            'model' => Teacher::find()->andFilterWhere([ 'like', 'subjects', '"'.Yii::$app->params['subInx'].'"' ])->with(['user'])->asArray()->all(),
        ]);
    }
}
