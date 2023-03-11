<?php

namespace app\models\exam;

use Yii;
// use app\models\exam\{Course};

/**
 * This is the model class for table "Section".
 *
 * @property int $id
 * @property int $course_id
 * @property int $place
 * @property string $name
 * @property boolean $publish
 *
 * @property Fullexam $fullexam
 * @property Exercise[] $exercises
 */
class Section extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'examsection';
    }

    public function rules()
    {
        return [
            [['fullexam_id', 'name', 'place'], 'required'],
            [['fullexam_id', 'place', 'publish'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['fullexam_id'], 'exist', 'targetClass' => Fullexam::className(), 'targetAttribute' => ['fullexam_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fullexam_id' => 'Курс',
            'name' => 'Название',
            'place' => 'Место в выдаче',
            'publish' => 'Опубликованно',
        ];
    }

    public function getFullexam()
    {
        return $this->hasOne(Fullexam::className(), ['id' => 'fullexam_id']);
    }

    public function getExercises()
    {
        return $this->hasMany(Exercise::className(), ['section_id' => 'id'])
            ->orderBy('place');
    }
}
