<?php

namespace app\models\form;

use Yii;
use yii\rbac\Role;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use app\models\{User, Teacher};
use app\components\UserStatus;
use yii\imagine\Image;

class AdminUser extends Model
{
    public $id;
    public $ava                 = User::DEF_AVA;
    public $username;
    public $email;
    public $phone;
    public $name;
    public $surname;
    public $phrase;
    public $skype;
    public $teacher_class       = 0;
    public $cash                = 0;

    private $savePath           = 'css/images/users/';
    public $image               = null;
    public $_user               = null;
    public $roles               = [];
    public $role;
    public $teacher_option;
    public $specialization      = '';
    public $specializationArr;

    public function rules()
    {
        return [
            [['id', 'teacher_class'], 'integer'],
            [['username', 'email'], 'required'],
            [['username', 'email'], 'trim'],
            ['email', 'email'],
            ['email', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот E-mail адрес уже зарегистрирован!'],
            ['username', 'unique',
                'filter' => ['!=', 'id', $this->id],
                'targetClass' => 'app\models\User',
                'message' => 'Этот Nickname занят!'],

            [['cash'], 'number'],
            [['image'], 'file', 'extensions' => 'png, jpg', 'maxSize' => 20*1024*1024], // 20MB
            [['phrase'], 'string'],
            [['phone'], 'string', 'max' => 18],
            [['ava', 'name', 'surname', 'skype', 'role'], 'string', 'max' => 255],

            ['ava', 'default', 'value' => User::DEF_AVA],
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
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'phrase' => 'То, чем пользователь хочет поделиться',
            'skype' => 'Логин в skype',
            'cash' => 'Баланс',
            'teacher_class' => 'Количество занятий с учителем',
            'role' => 'Роль пользователя',
        ];
    }

    // Находим пользователя и передаём его параметры в текущую модель
    public function getValue($id) {
        if ($this->_user == null && $id > 0)
            $this->_user = User::findOne($id);

        $user = $this->_user;
        if ($user == null)
            return false;

        $attr = [
            'id' => $user->id,
            'ava' => $user->ava,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'name' => $user->name,
            'surname' => $user->surname,
            'phrase' => $user->phrase,
            'skype' => $user->skype,
            'cash' => $user->cash,
            'teacher_class' => $user->teacher_class,
            'notifications' => json_encode(User::DEF_NOTIF),
            'statistics' => json_encode(User::DEF_STAT),
        ];
        $this->attributes = $attr;
        $role = current(Yii::$app->authManager->getRolesByUser($user->id));
        $this->role = $role->name;
        $this->teacher_option = $user->teacher;
        $this->specialization = [];
        $this->specializationArr = Teacher::getSpec();
        foreach ($this->specializationArr as $key => $val) {
            $this->specialization[$val] = (strpos($user->teacher->specialization, $val) === false) ? '' : 'active';
        }
        return true;
    }

    public function changeUserOption() {
        if ($this->_user != null) {
            $user = $this->_user;
        } else if ($this->id > 0) {
            $user = $this->findUser($this->id);
        }

        if ($user == null)
            $user = new User;

        $user->ava = $this->ava;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->phrase = $this->phrase;
        $user->skype = $this->skype;
        $user->cash = $this->cash;
        $user->teacher_class = $this->teacher_class;
        // DEBUG: Добавить изменение пароля

        if ($user->id == 0)
            $user->status = UserStatus::ACTIVE;
        $user->save();

        if ($user->role->name != $this->role && $user->role->name == 'teacher') {
            // Находим учителя
            $teacher = Teacher::find()->where(['user_id' => $user->id])->limit(1)->one();
            // удаляем строку
            if ($teacher)
                $teacher->delete();
        }

        $user->setRole($this->role);
        if ($this->role == 'teacher') {
            $this->teacher_option = Teacher::find()->where(['user_id' => $user->id])->limit(1)->one();
        }

        return true;
    }

    // Загрузить картинку
    public function upload($coords) {
        $name = $this->image->baseName . '.' . $this->image->extension;
        $path = Yii::$app->params['teampsPath'].$name;

        $this->image->saveAs($path);

        do {
            $name = substr(str_shuffle($this->permitted_chars), 0, 16).'.'.$this->image->extension;
        } while (file_exists($this->savePath.$name));

        Image::crop($path, $coords['W'], $coords['H'], [$coords['X'], $coords['Y']])
            ->save($this->savePath.$name, ['quality' => 70]);
        unlink($path);

        $this->deleteAva();

        $this->image = null;
        $this->ava = $name;
    }

    public function deleteAva() {
        if ($this->ava != User::DEF_AVA && file_exists($this->savePath.$this->ava)) {
            unlink($this->savePath.$this->ava);
            return true;
        } else { return false; }
    }

    public function findUser($id) {
        if (($this->_user = User::findOne($id)) != null) {
            return $this->_user;
        }
        return null;
    }
} // end User
