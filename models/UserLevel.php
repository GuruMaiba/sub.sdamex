<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_lvl".
 *
 * @property int $user_id
 * @property int $exp
 * @property int $lvl
 * @property string $range
 */
class UserLevel extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_lvl';
    }

    public function rules()
    {
        return [
            [['exp', 'lvl'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'exp' => 'Опыт',
            'lvl' => 'Уровень',
        ];
    }
}
