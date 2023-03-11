<?php

namespace app\models\exam;

use Yii;
use app\models\{User, Teacher};
use app\models\exam\{Fullexam};

/**
 * This is the model class for table "examresult_full".
 *
 * @property int $id
 * @property int $fullexam_id
 * @property int $user_id
 * @property int $user_points
 * @property int $max_points
 * @property int $teacher_id
 * @property int $check
 * @property string $teacher_comment
 * @property string $answers
 *
 * @property User $learner
 * @property Teacher $teacher
 */
class Result extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'result_fullexam';
    }

    public function rules()
    {
        return [
            [['fullexam_id', 'user_id', 'answers'], 'required'],
            [['fullexam_id', 'user_id', 'teacher_id', 'check'], 'integer'],
            [['teacher_comment', 'answers'], 'string'],
            [['fullexam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fullexam::className(), 'targetAttribute' => ['fullexam_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_points' => 'Пользовательские баллы',
            'max_points' => 'Максимально возможное количество баллов',
            'check' => 'Проверено',
            'teacher_comment' => 'Комментарий учителя',
            'answers' => 'Ответы',
        ];
    }

    public function getFullexam()
    {
        return $this->hasOne(Fullexam::className(), ['id' => 'fullexam_id']);
    }

    public function getLearner()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['user_id' => 'teacher_id']);
    }
}
