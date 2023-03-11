<?php

namespace app\models\message;

use Yii;

use app\models\User;

/**
 * This is the model class for table "dialog".
 *
 * @property int $id
 * @property int $first_user_id
 * @property int $second_user_id
 * @property int $last_message_date
 * @property int $numb_miss_message
 *
 * @property User $firstUser
 * @property User $secondUser
 * @property Message[] $dialogMessages
 */
class Dialog extends \yii\db\ActiveRecord
{
    // public static function getDb()
    // {
    //     return \Yii::$app->commondb;  
    // }
    
    public static function tableName()
    {
        return 'dialog';
    }

    public function rules()
    {
        return [
            [['first_user_id', 'second_user_id'], 'required'],
            [['first_user_id', 'second_user_id', 'last_message_date', 'first_miss_message', 'second_miss_message'], 'integer'],
            [['first_del', 'second_del'], 'boolean'],
            [['first_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['first_user_id' => 'id']],
            [['second_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['second_user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_user_id' => 'First User ID',
            'second_user_id' => 'Second User ID',
            'last_message_date' => 'Last Message Date',
            'first_miss_message' => 'First Miss Message',
            'second_miss_message' => 'Second Miss Message',
            'first_del' => 'First del',
            'second_del' => 'Second del',
        ];
    }

    public function getFirstUser()
    {
        return $this->hasOne(User::className(), ['id' => 'first_user_id']);
    }

    public function getSecondUser()
    {
        return $this->hasOne(User::className(), ['id' => 'second_user_id']);
    }

    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['dialog_id' => 'id']);
    }
}
