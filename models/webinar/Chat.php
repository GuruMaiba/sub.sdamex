<?php

namespace app\models\webinar;

use Yii;
use app\models\User;

/**
 * This is the model class for table "webinar_chat".
 *
 * @property int $id
 * @property int $webinar_id
 * @property int $user_id
 * @property string $message
 *
 * @property Webinar $webinar
 */
class Chat extends \app\models\AppActiveRecord
{
    public static function tableName()
    {
        return 'webinar_chat';
    }

    public function rules()
    {
        return [
            [['webinar_id', 'user_id', 'message'], 'required'],
            [['webinar_id', 'user_id'], 'integer'],
            [['message'], 'string'],
            [['webinar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Webinar::className(), 'targetAttribute' => ['webinar_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'webinar_id' => 'Webinar ID',
            'user_id' => 'User ID',
            'message' => 'Message',
        ];
    }

    public function getWebinar()
    {
        return $this->hasOne(Webinar::className(), ['id' => 'webinar_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
