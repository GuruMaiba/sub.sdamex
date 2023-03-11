<?php

namespace app\models\webinar;

use Yii;
use app\models\User;

/**
 * This is the model class for table "webinar_member".
 *
 * @property int $id
 * @property int $webinar_id
 * @property int $user_id
 * @property int $create_at
 *
 * @property User $user
 * @property Webinar $webinar
 */
class ChatBan extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'webinar_chat_ban';
    }

    public function rules()
    {
        return [
            [['webinar_id', 'user_id'], 'required'],
            [['webinar_id', 'user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['webinar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Webinar::className(), 'targetAttribute' => ['webinar_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'webinar_id' => 'Webinar ID',
            'user_id' => 'User ID',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getWebinar()
    {
        return $this->hasOne(Webinar::className(), ['id' => 'webinar_id']);
    }
}
