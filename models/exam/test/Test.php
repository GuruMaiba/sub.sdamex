<?php

namespace app\models\exam\test;

use Yii;
use app\models\exam\Exercise;
use app\models\webinar\Webinar;
use app\models\course\Lesson;

/**
 * This is the model class for table "examtest".
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $webinar_id
 * @property int $publish
 * @property int $count_points
 * @property string $title
 * @property string $desc
 * @property string $task
 * @property string $correct_answers
 *
 * @property Lesson $lesson
 * @property Webinar $webinar
 * @property Question[] $examtestHisQuestions
 */
class Test extends \app\models\AppActiveRecord
{
    public $track;

    public static function tableName()
    {
        return 'examtest';
    }

    public function rules()
    {
        return [
            [['exercise_id', 'lesson_id', 'webinar_id', 'qst_exp'], 'integer'],
            [['text', 'task', 'correct_answers'], 'string'],
            [['audio_name'], 'string', 'max' => 255],
            [['track'], 'file', 'extensions' => 'mp3, ogg, wav'], // audio/mpeg, audio/ogg, audio/wav
            [['publish', 'oneshot'], 'boolean'],
            [['exercise_id'], 'exist', 'targetClass' => Exercise::className(), 'targetAttribute' => ['exercise_id' => 'id']],
            [['lesson_id'], 'exist', 'targetClass' => Lesson::className(), 'targetAttribute' => ['lesson_id' => 'id']],
            [['webinar_id'], 'exist', 'targetClass' => Webinar::className(), 'targetAttribute' => ['webinar_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task' => 'Задание',
            'text' => 'Описание',
            'track' => 'Аудио',
            'qst_exp' => 'Опыт за 1 вопрос',
            'publish' => 'Опубликовать',
            'oneshot' => 'Можно пройти только 1 раз',
        ];
    }

    // public function saveModel($lesson_id=0, $webinar_id=0, $exercise_id=0) {
    //     $lesson = new Lesson();
    //     $webinar = new Webinar();
    //     $exercise = new Exercise();

    //     if ($lesson_id > 0) {
    //         if ($this->chechModelLink( Lesson::findOne($lesson_id) ))
    //             $this->lesson_id = $lesson_id;
    //         else
    //             return 0;
    //     } else if ($webinar_id > 0) {
    //         if ($this->chechModelLink( Webinar::findOne($webinar_id) ))
    //             $this->webinar_id = $webinar_id;
    //         else
    //             return 0;
    //     } else if ($exercise_id > 0) {
    //         if (Exercise::findOne($exercise_id) != null)
    //             $this->exercise_id = $exercise_id;
    //         else
    //             return 0;
    //     }
    //     $this->save();

    //     $lesson->examtest_id = $this->id;
    //     $lesson->update();

    //     $webinar->examtest_id = $this->id;
    //     $webinar->update();
    // }

    // public function chechModelLink($model=null) {
    //     if ($model != null && $model->examwrite < 1)
    //         return true;
    //     else
    //         return false;
    // }

    public function getExercise()
    {
        return $this->hasOne(Exercise::className(), ['id' => 'exercise_id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lesson_id']);
    }

    public function getWebinar()
    {
        return $this->hasOne(Webinar::className(), ['id' => 'webinar_id']);
    }

    public function getQuestions($type = 'mod')
    {
        $class = Question::className();
        $attr = ['examtest_id' => 'id'];

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->asArray();
    }
}
