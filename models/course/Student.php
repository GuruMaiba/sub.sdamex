<?php

namespace app\models\course;

use Yii;
use app\models\User;

/**
 * This is the model class for table "learner_course".
 *
 * @property int $learner_id
 * @property int $course_id
 * @property int $start_at
 * @property int $end_at
 *
 * @property Course $course
 * @property User $learner
 */
class Student extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'learner_course';
    }

    public function rules()
    {
        return [
            [['learner_id', 'course_id', 'start_at', 'end_at'], 'required'],
            [['learner_id', 'course_id', 'start_at', 'end_at'], 'integer'],
            [['learner_id', 'course_id'], 'unique', 'targetAttribute' => ['learner_id', 'course_id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['learner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['learner_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'learner_id' => 'Learner ID',
            'course_id' => 'Course ID',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getLearner()
    {
        return $this->hasOne(User::className(), ['id' => 'learner_id']);
    }
}
