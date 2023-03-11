<?php

namespace app\models\message;

use Yii;

use app\models\User;

/**
 * This is the model class for table "dialog_message".
 *
 * @property int $id
 * @property int $dialog_id
 * @property string $message
 * @property int $date
 * @property int $view
 *
 * @property Dialog $dialog
 */
class Message extends \yii\db\ActiveRecord
{
    public const VIEW_MESS = 50;
    
    // public static function getDb()
    // {
    //     return \Yii::$app->commondb;  
    // }

    public static function tableName()
    {
        return 'dialog_message';
    }

    public function rules()
    {
        return [
            [['dialog_id', 'message', 'date'], 'required'],
            [['dialog_id', 'user_id', 'date', 'view'], 'integer'],
            [['message'], 'string'],
            [['dialog_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dialog::className(), 'targetAttribute' => ['dialog_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dialog_id' => 'Dialog ID',
            'user_id' => 'User ID',
            'message' => 'Message',
            'date' => 'Date',
            'view' => 'View',
        ];
    }

    public function getDialog()
    {
        return $this->hasOne(Dialog::className(), ['id' => 'dialog_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
