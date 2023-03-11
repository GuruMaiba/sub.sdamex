<?php

namespace app\models\teacher;

use Yii;
use app\models\{User, Teacher};

/**
 * This is the model class for table "teacher_student".
 *
 * @property int $teacher_id
 * @property int $student_id
 * @property int $is_review
 * @property string $review_text
 * @property int $review_rating
 * @property int $review_date
 *
 * @property User $student
 * @property Teacher $teacher
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'student_id'], 'required'],
            [['teacher_id', 'student_id', 'review_rating', 'review_date', 'review_anonymously'], 'integer'],
            [['is_review'], 'boolean'],
            [['review_text'], 'string'],
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
            'teacher_id' => 'Учитель',
            'student_id' => 'Студент',
            'is_review' => 'Оставил ли отзыв',
            'review_text' => 'Текст',
            'review_rating' => 'Рейтинг',
            'review_date' => 'Дата',
            'review_anonymously' => 'Анонимный отзыв'
        ];
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
