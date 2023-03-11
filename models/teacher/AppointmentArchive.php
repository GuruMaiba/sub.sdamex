<?php

namespace app\models\teacher;

use Yii;
use app\models\{User, Teacher};

/**
 * This is the model class for table "teacher_appointment_archive".
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $student_id
 * @property int $begin_date
 * @property int $end_date
 * @property int $status
 * @property string $teacher_message
 * @property string $student_message
 *
 * @property Appointment $appointment
 * @property User $student
 * @property Teacher $teacher
 */
class AppointmentArchive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_appointment_archive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'student_id', 'subject_id', 'begin_date', 'end_date'], 'required'],
            [['teacher_id', 'student_id', 'subject_id', 'begin_date', 'end_date'], 'integer'],
            [['status'], 'integer', 'min' => -9, 'max' => 9], 
            [['teacher_message', 'student_message'], 'string'],
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
            'id' => 'ID',
            'teacher_id' => 'Teacher ID',
            'student_id' => 'Student ID',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'status' => 'Status',
            'teacher_message' => 'Teacher Message',
            'student_message' => 'Student Message',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointment()
    {
        return $this->hasOne(Appointment::className(), ['archive_id' => 'id']);
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
