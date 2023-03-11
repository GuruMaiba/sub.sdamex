<?php

namespace app\models\teacher;

use Yii;
use app\models\Teacher;

/**
 * This is the model class for table "teacher_time_edit".
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $date
 * @property int $add_or_del
 *
 * @property Teacher $teacher
 */
class TimeEdit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_time_edit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'date', 'add_or_del'], 'required'],
            [['teacher_id', 'date'], 'integer'],
            ['add_or_del', 'boolean'],
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
            'date' => 'Date',
            'add_or_del' => 'Add Or Del',
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
