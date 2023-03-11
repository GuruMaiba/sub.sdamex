<?php

namespace app\models\exam\test;

use Yii;

/**
 * This is the model class for table "examtest_his_question".
 *
 * @property int $id
 * @property int $examtest_id
 * @property string $text
 * @property int $multiple_answer
 * @property int $points
 * @property int $place
 *
 * @property Test $examtest
 * @property Answer[] $examtestHisAnswers
 */
class Question extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examtest_question';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['examtest_id', 'text', 'place'], 'required'],
            [['examtest_id', 'place'], 'integer'],
            [['multiple_answer', 'hard'], 'boolean'],
            [['text'], 'string'],
            [['examtest_id'], 'exist', 'targetClass' => Test::className(), 'targetAttribute' => ['examtest_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'examtest_id' => 'Тест',
            'text' => 'Текст',
            'multiple_answer' => 'Множественный ответ',
            'hard' => 'Сложный',
            'place' => 'Положение в списке',
        ];
    }

    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'examtest_id']);
    }

    public function getAnswers($type = 'mod')
    {
        $class = Answer::className();
        $attr = ['question_id' => 'id'];

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->asArray();
    }
}
