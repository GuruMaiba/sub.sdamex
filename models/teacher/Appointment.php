<?php

namespace app\models\teacher;

use Yii;
use app\models\{User, Teacher};

/**
 * This is the model class for table "teacher_appointment".
 *
 * @property int $archive_id
 * @property int $teacher_id
 * @property int $student_id
 * @property int $begin_date
 * @property int $end_date
 * @property int $status
 *
 * @property AppointmentArchive $archive
 * @property User $student
 * @property Teacher $teacher
 */
class Appointment extends \yii\db\ActiveRecord
{
    public const DEF_HOUR = 1;
    public const DEF_MIN = 0;

    public static function getTimeStep() {
        return ((self::DEF_HOUR*60*60) + (self::DEF_MIN*60));
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_appointment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['archive_id', 'teacher_id', 'student_id', 'subject_id', 'begin_date', 'end_date'], 'required'],
            [['archive_id', 'teacher_id', 'student_id', 'subject_id', 'begin_date', 'end_date'], 'integer'],
            [['status'], 'integer', 'min' => -9, 'max' => 9], 
            [['archive_id'], 'exist', 'skipOnError' => true, 'targetClass' => AppointmentArchive::className(), 'targetAttribute' => ['archive_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teacher::className(), 'targetAttribute' => ['teacher_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'archive_id' => 'Archive ID',
            'teacher_id' => 'Teacher ID',
            'student_id' => 'Student ID',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchive()
    {
        return $this->hasOne(AppointmentArchive::className(), ['id' => 'archive_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(User::className(), ['id' => 'student_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['user_id' => 'teacher_id']);
    }
}
