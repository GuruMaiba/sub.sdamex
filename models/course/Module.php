<?php

namespace app\models\course;

use Yii;

/**
 * This is the model class for table "module".
 *
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property string $desc
 * @property int $place
 * @property int $free
 * @property int $publish
 *
 * @property Examprogress[] $examprogresses
 * @property Lesson[] $lessons
 * @property Course $course
 * @property Progress[] $progresses
 */
class Module extends \app\models\AppActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'module';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['course_id', 'title'], 'required'],
            [['course_id', 'place', 'free', 'publish'], 'integer'],
            [['desc', 'ava'], 'string'],
            [['image'], 'file', 'extensions' => 'png, jpg'],
            [['title'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'image' => 'Обложка',
            'ava' => 'Путь к картинке',
            'title' => 'Название',
            'desc' => 'Описание',
            'place' => 'Место',
            'free' => 'Бесплатный',
            'publish' => 'Опубликовать',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamprogresses()
    {
        return $this->hasMany(Examprogress::className(), ['module_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['module_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgresses()
    {
        return $this->hasMany(Progress::className(), ['module_id' => 'id']);
    }
}
