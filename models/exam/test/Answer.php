<?php

namespace app\models\exam\test;

use Yii;

/**
 * This is the model class for table "examtest_answer".
 *
 * @property int $id
 * @property int $question_id
 * @property string $text
 * @property boolean $correct
 *
 * @property Question $qestion
 */
class Answer extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'examtest_answer';
    }

    public function rules()
    {
        return [
            [['question_id', 'text'], 'required'],
            [['question_id'], 'integer'],
            [['text'], 'string'],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Вопрос',
            'text' => 'Текст',
        ];
    }

    public function getQestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }
}
