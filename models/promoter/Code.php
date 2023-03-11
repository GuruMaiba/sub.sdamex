<?php

namespace app\models\promoter;

use Yii;
use app\models\User;

/**
 * This is the model class for table "promoter_code".
 *
 * @property string $code
 * @property int $promoter_id
 * @property int $type
 * @property int $end_at
 *
 * @property Promoter $promoter
 */
class Code extends \app\models\AppActiveRecord
{
    public $old_code;
    public $str_date;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promoter_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'promoter_id'], 'required'],
            [['promoter_id', 'type', 'end_at'], 'integer'],
            [['code'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['promoter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['promoter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Промокод',
            'promoter_id' => 'Промоутер',
            'type' => 'Плюшки, которые даёт код',
            'str_date' => 'Дата окончания работы промокода (оставить пустым, если бессрочно)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromoter()
    {
        return $this->hasOne(User::className(), ['id' => 'promoter_id']);
    }
}
