<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lvl".
 *
 * @property int $id
 * @property int $lvl
 * @property int $exp
 */
class Level extends \yii\db\ActiveRecord
{
    // public static function getDb()
    // {
    //     return \Yii::$app->commondb;  
    // }
    
    public static function tableName()
    {
        return 'lvl';
    }

    public function rules()
    {
        return [
            ['exp', 'integer'],
            ['isMax', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'LVL',
            'exp' => 'Количество опыта',
            'isMax' => 'MAX',
        ];
    }
}
