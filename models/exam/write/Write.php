<?php

namespace app\models\exam\write;

use Yii;
use app\models\exam\Exercise;
use app\models\webinar\Webinar;
use app\models\course\Lesson;

/**
 * This is the model class for table "examwrite".
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $webinar_id
 * @property int $publish
 * @property int $count_points
 * @property string $title
 * @property string $desc
 * @property string $task
 *
 * @property Exercise $lesson
 * @property Lesson $lesson
 * @property Webinar $webinar
 * @property Reply[] $answers
 * @property Examprogress[] $examprogresses
 */
class Write extends \app\models\AppActiveRecord
{
    // public $savePath = 'web/audio/';
    public $track;

    public static function tableName()
    {
        return 'examwrite';
    }

    public function rules()
    {
        return [
            [['exercise_id', 'lesson_id', 'webinar_id', 'exp'], 'integer'],
            [['text', 'task'], 'string'],
            [['audio_name', 'themes'], 'string', 'max' => 255],
            [['track'], 'file', 'extensions' => 'mp3, ogg, wav, webm'],
            [['publish'], 'boolean'],
            [['exercise_id'], 'exist', 'targetClass' => Exercise::className(), 'targetAttribute' => ['exercise_id' => 'id']],
            [['lesson_id'], 'exist', 'targetClass' => Lesson::className(), 'targetAttribute' => ['lesson_id' => 'id']],
            [['webinar_id'], 'exist', 'targetClass' => Webinar::className(), 'targetAttribute' => ['webinar_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст',
            'task' => 'Задание',
            'track' => 'Аудио',
            'exp' => 'Опыт',
            'publish' => 'Опубликовать',
            'themes' => 'Темы',
        ];
    }

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

    public function getAnswers($type = 'mod')
    {
        $class = Reply::className();
        $attr = ['examwrite_id' => 'id'];
        // return $this->hasMany(Reply::className(), ['examwrite_id' => 'id']);

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->asArray();
    }
}
