<?php

namespace app\models\exam\correlate;

use Yii;
use app\models\exam\Exercise;

/**
 * This is the model class for table "examcorrelate".
 *
 * @property int $id
 * @property int $exercise_id
 * @property string $task
 * @property string $title
 * @property string $text
 * @property string $qst_name
 * @property string $ans_name
 * @property string $output
 * @property string $audio_name
 * @property boolean $publish
 *
 * @property Exercise $exercise
 * @property Pairs[] $examcorrelatePairs
 */
class Correlate extends \app\models\AppActiveRecord
{
    // public $savePath = 'web/audio/';
    public $track;

    public static function tableName()
    {
        return 'examcorrelate';
    }

    public function attributeLabels()
    {
        return [
            'task' => 'Задание',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'qst_name' => 'Имя задающего вопросы',
            'ans_name' => 'Имя отвечающего на вопросы',
            'track' => 'Аудио',
            'pair_exp' => 'Опыт за правильную пару',
            'publish' => 'Опубликовать',
            'qst_hidden' => 'Скрыть вопросы',
            'themes' => 'Темы',
        ];
    }

    public function rules()
    {
        return [
            [['exercise_id'], 'required'],
            [['exercise_id', 'publish', 'pair_exp'], 'integer'],
            [['task', 'text'], 'string'],
            [['qst_hidden', 'publish'], 'boolean'],
            [['track'], 'file', 'extensions' => 'mp3, ogg, wav, webm'],
            [['qst_name', 'ans_name', 'audio_name', 'themes'], 'string', 'max' => 255],
            [['exercise_id'], 'exist', 'targetClass' => Exercise::className(), 'targetAttribute' => ['exercise_id' => 'id']],
        ];
    }

    // public function validLink($attribute, $params) {
    //     if (!$this->hasErrors()) {
    //         if ($this->exercise_id < 1)
    //             $this->addError('exercise_id', 'Отсутствует привязка к заданию!');
    //     }
    // }

    public function getExercise()
    {
        return $this->hasOne(Exercise::className(), ['id' => 'exercise_id']);
    }

    public function getPairs($type = 'mod')
    {
        $class = Pair::className();
        $attr = ['examcorrelate_id' => 'id'];

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->asArray();
    }
}
