<?php

namespace app\models\exam\correlate;

use Yii;

/**
 * This is the model class for table "examcorrelate_pair".
 *
 * @property int $id
 * @property int $examcorrelate_id
 * @property string $qst_text
 * @property string $ans_text
 * @property int $points
 *
 * @property Correlate $examcorrelate
 */
class Pair extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'examcorrelate_pair';
    }

    public function rules()
    {
        return [
            [['examcorrelate_id'], 'required'],
            [['examcorrelate_id'], 'integer'],
            [['qst_text', 'ans_text'], 'string'],
            [['examcorrelate_id'], 'exist', 'targetClass' => Correlate::className(), 'targetAttribute' => ['examcorrelate_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'qst_text' => 'Текст вопроса',
            'ans_text' => 'Текст ответа',
        ];
    }

    public function getCorrelate()
    {
        return $this->hasOne(Correlate::className(), ['id' => 'examcorrelate_id']);
    }
}
