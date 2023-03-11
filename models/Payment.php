<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_pay".
 *
 * @property int $id
 * @property int $user_id
 * @property int $model_id
 * @property int $payment_id
 * @property int $type
 * @property int $amount
 * @property string $desc
 * @property int $success
 */
class Payment extends \app\models\AppActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_pay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'amount'], 'required'],
            [['user_id', 'model_id', 'payment_id', 'type', 'amount', 'success', 'updated_at'], 'integer'],
            [['desc', 'extra_options'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'model_id' => 'Крс/веб',
            'payment_id' => 'ID от банка',
            'type' => 'ТИП',
            'amount' => 'Оплачено',
            'desc' => 'Описание',
            'code' => 'Инвайт-код',
            'success' => 'Успешная оплата',
            'updated_at' => 'Дата'
        ];
    }
}
