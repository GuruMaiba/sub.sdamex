<?php

namespace app\modules\personal\controllers;
use Yii;

// use yii\base\InvalidParamException;
// use yii\web\BadRequestHttpException;
use app\models\exam\Result;
use app\models\exam\write\Reply;
use app\controllers\AppController;
// use yii\filters\VerbFilter;
// use yii\filters\AccessControl;

class CheckController extends AppController
{
    public function actionIndex()
    {
        $this->view->title = "Практические работы | ".Yii::$app->params['shortName'];
        return $this->render('index', [
            'examResults' => Result::find()->where(['user_id'=>Yii::$app->user->identity->id])->with([
                    'fullexam' => function ($query) { $query->select(['id', 'name', 'max_points']); }
                ])->orderBy(['id'=>SORT_DESC])->asArray()->limit(3)->all(),
            'practicalReplies' => Reply::find()->where(['user_id'=>Yii::$app->user->identity->id])->with([
                    'write' => function ($query) { $query->select(['id', 'exercise_id', 'lesson_id', 'webinar_id', 'exp', 'text', 'task']); },
                    'write.exercise' => function ($query) { $query->select(['id', 'section_id', 'name', 'fullexam_points']); },
                    'write.exercise.section' => function ($query) { $query->select(['id', 'fullexam_id']); },
                    'write.exercise.section.fullexam' => function ($query) { $query->select(['id', 'subject_id', 'name']); },
                    'write.lesson' => function ($query) { $query->select(['id', 'module_id', 'title']); },
                    'write.lesson.module' => function ($query) { $query->select(['id', 'course_id']); },
                    'write.lesson.module.course' => function ($query) { $query->select(['id', 'subject_id', 'title']); },
                ])->orderBy(['id'=>SORT_DESC])->asArray()->limit(10)->all(),
        ]);
    }
}
