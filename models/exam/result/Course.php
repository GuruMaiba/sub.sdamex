<?php

namespace app\models\exam\result;

use Yii;
use app\models\course\{Course, Module, Lesson, ProgressCourse};

/**
 * This is the model class for table "examresult_course".
 *
 * @property int $id
 * @property int $progress_id
 * @property int $course_id
 * @property int $module_id
 * @property int $lesson_id
 * @property int $result_id
 *
 * @property Course $course
 * @property Module $module
 * @property Lesson $lesson
 * @property ProgressCourse $progress
 * @property Examresult $result
 */
class ERCourse extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'examresult_course';
    }

    public function rules()
    {
        return [
            [['progress_id', 'course_id', 'module_id', 'lesson_id', 'result_id'], 'required'],
            [['progress_id', 'course_id', 'module_id', 'lesson_id', 'result_id'], 'integer'],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lesson::className(), 'targetAttribute' => ['lesson_id' => 'id']],
            [['module_id'], 'exist', 'skipOnError' => true, 'targetClass' => Module::className(), 'targetAttribute' => ['module_id' => 'id']],
            [['progress_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgressCourse::className(), 'targetAttribute' => ['progress_id' => 'id']],
            [['result_id'], 'exist', 'skipOnError' => true, 'targetClass' => Result::className(), 'targetAttribute' => ['result_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'progress_id' => 'Прогресс пользователя',
            'course_id' => 'Курс',
            'module_id' => 'Модуль',
            'lesson_id' => 'Урок',
            'result_id' => 'Результат',
        ];
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getModule()
    {
        return $this->hasOne(Module::className(), ['id' => 'module_id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lesson_id']);
    }

    public function getProgress()
    {
        return $this->hasOne(ProgressCourse::className(), ['id' => 'progress_id']);
    }

    public function getResult()
    {
        return $this->hasOne(Result::className(), ['id' => 'result_id']);
    }
}
