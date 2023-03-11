<?php

namespace app\models\exam\addition;

use Yii;
use app\models\exam\Exercise;

/**
 * This is the model class for table "examaddition".
 *
 * @property int $id
 * @property int $exercise_id
 * @property int $word_points
 * @property string $task
 * @property string $title
 * @property string $text
 * @property boolean $publish
 *
 * @property Exercise $exercise
 */
class Addition extends \app\models\AppActiveRecord
{
    public static function tableName()
    {
        return 'examaddition';
    }

    public function rules()
    {
        return [
            [['exercise_id', 'word_exp'], 'integer'],
            [['exercise_id'], 'validLink'],
            [['task', 'text'], 'string'],
            [['publish'], 'boolean'],
            [['exercise_id'], 'exist', 'targetClass' => Exercise::className(), 'targetAttribute' => ['exercise_id' => 'id']],
        ];
    }

    public function validLink($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->exercise_id < 1)
                $this->addError('exercise_id', 'Отсутствует привязка к заданию!');
        }
    }

    public function attributeLabels()
    {
        return [
            'task' => 'Задание',
            'text' => 'Текст _(word/true/theme_id)',
            'word_exp' => 'Количество опыта за слово',
            'publish' => 'Опубликовать',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExercise()
    {
        return $this->hasOne(Exercise::className(), ['id' => 'exercise_id']);
    }
}
