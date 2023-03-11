<?php

namespace app\models\form;

use Yii;
use yii\rbac\Role;
use yii\base\Model;
// use yii\behaviors\TimestampBehavior;
use app\models\User;
use app\components\UserStatus;
use yii\imagine\Image;

class UserSettings extends Model
{
    public $id;
    public $ava             = User::DEF_AVA;
    public $username;
    public $email;
    public $phone;
    public $first_name;
    public $last_name;
    public $oldPass;
    public $newPass;
    public $retypeNewPass;

    private $savePath       = 'web/css/images/users/';
    public $image           = null;
    public $_user           = null;
    // public $teacher_option;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['username', 'email'], 'required'],
            [['username', 'email'], 'trim'],
            ['email', 'email'],
            ['email', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот E-mail адрес уже зарегистрирован!'],
            [['username'], 'string', 'max' => 20],
            ['username', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот Nickname занят!'],

            // [['image'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxSize' => 20*1024*1024], // 20MB
            [['phone'], 'string', 'max' => 18],
            [['first_name', 'last_name', 'oldPass', 'newPass', 'retypeNewPass'], 'string', 'max' => 255],
            // ['ava', 'default', 'value' => User::DEF_AVA],

            [['newPass', 'retypePass'], 'string', 'min' => 6],
            ['retypePass', 'compare', 'compareAttribute' => 'newPass'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image' => 'Аватарка',
            'username' => 'Nickname',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
        ];
    }

    public function changeUserOption() {
        if ($this->_user != null) {
            $user = $this->_user;
        } else if ($this->id > 0) {
            $user = $this->findUser($this->id);
        }

        if ($user == null)
            return false;

        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->name = $this->first_name;
        $user->surname = $this->last_name;
        // DEBUG: Добавить изменение пароля
        // $user->setPassword($this->$newPass);

        $user->save();

        return true;
    }

    public function findUser($id) {
        if (($this->_user = User::findOne($id)) != null) {
            return $this->_user;
        }
        return null;
    }
} // end Settings
