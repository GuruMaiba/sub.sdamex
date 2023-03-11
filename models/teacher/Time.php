<?php

namespace app\models\teacher;

use Yii;
use app\models\Teacher;
use app\models\User;

/**
 * This is the model class for table "teacher_time".
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $day
 * @property int $hour
 * @property int $min
 *
 * @property Teacher $teacher
 */
class Time extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'teacher_time';
    }

    public function rules()
    {
        return [
            [['teacher_id', 'day', 'hour', 'min'], 'required'],
            [['teacher_id', 'student_id', 'day', 'hour', 'min'], 'integer'],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::className(), 'targetAttribute' => ['teacher_id' => 'user_id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher ID',
            'student_id' => 'Teacher ID',
            'day' => 'Day',
            'hour' => 'Hour',
            'min' => 'Min',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['user_id' => 'teacher_id']);
    }
}
