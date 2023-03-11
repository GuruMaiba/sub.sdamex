<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\exam\Examresult;
use app\models\teacher\{Appointment, AppointmentArchive, Time, TimeEdit, Student};
use app\components\AppointmentStatus;

/**
 * This is the model class for table "teacher".
 *
 * @property int $user_id
 * @property string $video
 *
 * @property User $user
 * @property Examresult[] $examresults
 * @property TeacherAppointment[] $teacherAppointments
 * @property TeacherTime[] $teacherTimes
 * @property TeacherTimeEdit[] $teacherTimeEdits
 */
class Teacher extends \yii\db\ActiveRecord
{
    // default month
    public const DEF_STAT_MONTH = [
        'practicals' => [
            'count' => 0,
            'count_fullexam' => 0,
            'list' => [
                // 'ОГЭ - задание 1', 'ЕГЭ - сочинение', 
            ],
        ],
        'webinars' => [
            'count' => 0,
            'list' => [] // ids
        ],
        'questions' => [
            'count' => 0,
            'list' => [] // ids
        ]
    ];

    public static function tableName()
    {
        return 'teacher';
    }

    public function rules()
    {
        return [
            [['video', 'specialization', 'subjects'], 'string', 'max' => 255],
            [['about_me'], 'string'],
            [['rating'], 'number'],
            [['student_count'], 'integer'],
            [['time_lock'], 'integer', 'min' => 1, 'max' => 999],
            [['user_id'], 'exist', 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'video' => 'Видео-визитка',
            'about_me' => 'О себе',
            'rating' => 'Рейтинг',
            'time_lock' => 'Блокировка времени, за ... часов',
            'statistics' => 'Статистика',
            'subjects' => 'Предметы',
            'specialization' => 'Специализация',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getStudents()
    {
        return $this->hasMany(Student::className(), ['teacher_id' => 'user_id']);
    }

    public function getExamresults()
    {
        return $this->hasMany(Examresult::className(), ['teacher_id' => 'user_id']);
    }

    public function getActiveAppointments()
    {
        return $this->hasMany(Appointment::className(), ['teacher_id' => 'user_id'])
            ->where(['in', 'status', [
                AppointmentStatus::ACTIVE,
                AppointmentStatus::STUDENT_SUCCESS,
                AppointmentStatus::STUDENT_ERROR
            ]])->with('archive')->orderBy('begin_date');
    }

    public function getArchiveAppointments()
    {
        return $this->hasMany(AppointmentArchive::className(), ['teacher_id' => 'user_id']);
    }

    public function getTime()
    {
        return $this->hasMany(Time::className(), ['teacher_id' => 'user_id']);
    }

    public function getTimeEdits()
    {
        return $this->hasMany(TimeEdit::className(), ['teacher_id' => 'user_id']);
    }

    public function getSubjects()
    {
        return $this->hasMany(TeacherSubject::className(), ['teacher_id' => 'user_id']);
    }

    // SPECIALIZATION
    public static function getSpec($inx)
    {
        $spec = ['ОГЭ', 'ЕГЭ'];
        // $inx = Yii::$app->params['subInx'];
        // switch ($inx) {
        //     case 2:
        //         $spec = [ 'ОГЭ', 'ЕГЭ', 'Tofil', 'Tofu' ];
        //         break;
        // }
        return $spec;
    }
}
