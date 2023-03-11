<?php

namespace app\models\exam;

use Yii;
use app\models\exam\addition\{Addition};
use app\models\exam\correlate\{Correlate};
use app\models\exam\test\{Test};
use app\models\exam\write\{Write};

/**
 * This is the model class for table "examsection_exercise".
 *
 * @property int $id
 * @property int $section_id
 * @property int $place
 * @property int $type
 * @property int $fullexam_points
 * @property string $name
 * @property string $output
 * @property string $hint
 * @property boolean $publish
 *
 * @property Section $section
 * @property Addition[] $additions
 * @property Correlate[] $correlates
 * @property Test[] $tests
 * @property Write[] $writes
 */
class Exercise extends \yii\db\ActiveRecord
{
    public const STATS_EXAM_ARR = [
        'id' => 0,
        'points' => 0,
        'exp' => 0,
        'max_exp' => 0,
        'themes' => [
            'corr' => [],
            'err' => [],
        ]
    ]; 
    public const STAT_EXAM_TASK = [
        'task' => 0,
        'corr' => 0,
        'skip' => 0,
    ];
    // max number of exercise in statistics
    public const STAT_MAX_EXE = 15;

    public static function tableName()
    {
        return 'examsection_exercise';
    }

    // DEBUG: ДОБАВИТЬ ТИП
    public function rules()
    {
        return [
            [['section_id', 'name', 'place'], 'required'],
            [['section_id', 'place', 'publish', 'type', 'fullexam', 'fullexam_points', 'task_count'], 'integer'],
            [['hint'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['section_id'], 'exist', 'targetClass' => Section::className(), 'targetAttribute' => ['section_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Название',
            'hint' => 'Подсказка',
            'place' => 'Порядковый номер',
            'publish' => 'Опубликованно',
            'type' => 'Тип упражнений',
            'fullexam' => 'Включить в полный экзамен',
            'fullexam_points' => 'Баллы за задание в полном экзамене',
        ];
    }

    public function getSection()
    {
        return $this->hasOne(Section::className(), ['id' => 'section_id']);
    }

    public function getAdditions($type = 'mod')
    {
        $class = Addition::className();
        $attr = ['exercise_id' => 'id'];

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->orderBy(['publish'=>SORT_DESC])->asArray();
    }

    public function getCorrelates($type = 'mod')
    {
        $class = Correlate::className();
        $attr = ['exercise_id' => 'id'];

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->orderBy(['publish'=>SORT_DESC])->asArray();
    }

    public function getTests()
    {
        return $this->hasMany(Test::className(), ['exercise_id' => 'id']);
        $class = Test::className();
        $attr = ['exercise_id' => 'id'];

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->orderBy(['publish'=>SORT_ASC])->asArray();
    }

    public function getWrites($type = 'mod')
    {
        $class = Write::className();
        $attr = ['exercise_id' => 'id'];

        if ($type == 'mod')
            return $this->hasMany($class, $attr);
        else
            return $this->hasMany($class, $attr)->orderBy(['publish'=>SORT_ASC])->asArray();
    }
}
