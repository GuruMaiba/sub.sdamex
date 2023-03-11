<?php

namespace app\models\webinar;

use Yii;
use app\models\User;

/**
 * This is the model class for table "webinar_comment".
 *
 * @property int $id
 * @property int $webinar_id
 * @property int $user_id
 * @property string $message
 * @property int $create_at
 * @property int $chat_or_comment
 *
 * @property User $user
 * @property Webinar $webinar
 */
class Comment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'webinar_comment';
    }

    public function rules()
    {
        return [
            [['webinar_id', 'user_id', 'message', 'create_at'], 'required'],
            [['webinar_id', 'user_id', 'reply_id', 'create_at'], 'integer'],
            [['message'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['webinar_id'], 'exist', 'skipOnError' => true, 'targetClass' => Webinar::className(), 'targetAttribute' => ['webinar_id' => 'id']],
            // [['reply_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::className(), 'targetAttribute' => ['reply_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'webinar_id' => 'Webinar ID',
            'user_id' => 'User ID',
            'reply_id' => 'Reply ID',
            'message' => 'Message',
            'create_at' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebinar()
    {
        return $this->hasOne(Webinar::className(), ['id' => 'webinar_id']);
    }

    // public function getReplies()
    // {
    //     return $this->hasMany(Comment::className(), ['reply_id' => 'id']);
    // }
}
