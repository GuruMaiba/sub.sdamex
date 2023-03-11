<?php

namespace app\models\exam;

use Yii;
use app\models\{Learner, Teacher};

/**
 * This is the model class for table "examresult".
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $learner_id
 * @property int $exam_id
 * @property int $type
 * @property int $user_points
 * @property int $max_points
 * @property string $user_answers_string
 * @property int $check
 * @property string $comment_teacher
 *
 * @property Learner $learner
 * @property Teacher $teacher
 * @property Course[] $examresultCourses
 * @property Answer[] $examwriteUserAnswers
 */
class Result extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'examresult';
    }

    public function rules()
    {
        return [
            [['teacher_id', 'learner_id', 'exam_id', 'type', 'user_points', 'max_points', 'check'], 'integer'],
            [['learner_id', 'exam_id', 'type', 'user_points', 'max_points'], 'required'],
            [['user_answers_string', 'comment_teacher'], 'string'],
            [['learner_id'], 'exist', 'targetClass' => Learner::className(), 'targetAttribute' => ['learner_id' => 'user_id']],
            [['teacher_id'], 'exist', 'targetClass' => Teacher::className(), 'targetAttribute' => ['teacher_id' => 'user_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'teacher_id' => 'Учитель',
            'learner_id' => 'Ученик',
            'exam_id' => 'Экзамен',
            'type' => 'Тип экзамена',
            'user_points' => 'Очки набранные пользователем',
            'max_points' => 'Максимальное количество очков',
            'user_answers_string' => 'Ответы пользователя',
            'check' => 'Проверка',
            'comment_teacher' => 'Комментарий учителя',
        ];
    }

    public function getLearner()
    {
        return $this->hasOne(Learner::className(), ['user_id' => 'learner_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['user_id' => 'teacher_id']);
    }

    public function getERCourses()
    {
        return $this->hasMany(Course::className(), ['result_id' => 'id']);
    }

    public function getEWAnswers()
    {
        return $this->hasMany(Answer::className(), ['examresult_id' => 'id']);
    }
}
