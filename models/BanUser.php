<?php

namespace app\models;

use Yii;
use app\components\UserStatus;

/**
 * This is the model class for table "ban_user".
 *
 * @property int $user_id
 * @property int $ban_begin
 * @property int $ban_end
 * @property string $cause
 *
 * @property User $user
 */
class BanUser extends \yii\db\ActiveRecord
{
    public $status;
    public $timeBan;

    // public static function getDb()
    // {
    //     return \Yii::$app->commondb;  
    // }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ban';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'timeBan'], 'safe'],
            [['ban_begin', 'ban_end'], 'integer'],
            [['cause'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'ban_begin' => 'Время блокировки',
            'ban_end' => 'Конец блокировки',
            'cause' => 'Описание блокировки',
        ];
    }

    /**
     * ban check
     * if user has ban then
     * create error message
     * @param integer $status - now status user
     * @param UNIXtime(integer) $GMT - user time zone
     * @return string|bool
     */
    public function banCheck($status, $GMT) {
        $error = false;

        // check user status
        switch ($status) {
            // if status == PERMANENT_BAN
            case UserStatus::PERMANENT_BAN:
                // edit time variable, set value one hundred days
                $time = time() + 100 * 24 * 3600;
                $error = 'Ваш аккаунт заблокирован!';
                $error .= ($this->cause) ? ' По причине: "' . $this->cause . '"' : '';
                break;

            // if status == TEMPORARY_BAN
            case UserStatus::TEMPORARY_BAN:
                $error = 'Ваш аккаунт заблокирован до ' . date('d.m.Y H:i', $this->ban_end + $GMT) . '!';
                $error .= ($this->cause) ? ' По причине: "' . $this->cause . '"' : '';
                break;

            // if status == LOGIN_ERROR
            case UserStatus::LOGIN_ERROR:
                $error = $this->cause;
                break;
        } // end switch

        return $error;
    }

    /**
     * add ban login
     * if user has ban then
     * time ban * 3
     * @param integer $status - now status user
     * @return string
     */
    public function addTimeBanLogin($status) {
        $timeBan = 5;

        // if status == LOGIN_ERROR
        if ($status == UserStatus::LOGIN_ERROR) {
            $timeBan = ($this->ban_end - $this->ban_begin) / 60;
            $timeBan *= 3;
        }

        // update or set new value
        $request = 'Превышение лимита попыток авторизации! Ваш аккаунт заблокирован на ' . $timeBan . ' минут!';
        $this->ban_begin = time();
        $this->ban_end = $this->ban_begin + ($timeBan * 60);
        $this->cause = $request;

        $this->save();

        return $request;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
