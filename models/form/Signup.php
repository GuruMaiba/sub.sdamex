<?php

namespace app\models\form;

use Yii;
use yii\base\Model;
use app\models\User;
use app\components\UserStatus;

class Signup extends Model
{
    public $username;
    public $email;
    public $password;
    public $retypePassword;

    public function rules()
    {
        return [
            [['email', 'password', 'retypePassword'], 'required'],
            [['email', 'username'], 'filter', 'filter' => 'trim'],

            ['username', 'validUsername'],

            ['email', 'email'],
            ['email', 'unique', 'targetClass' => 'app\models\User', 'message' => 'Этот E-mail адрес уже зарегистрирован!'],

            ['password', 'string', 'min' => 6],
            ['retypePassword', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function validUsername($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = new User();
            $flag = true;
            for ($i=1; $flag; $i++) {
                $user = $user->findByUsername($this->username);
                if (!$user) {
                    $flag = false;
                } else {
                    $flag = true;
                    $this->username .= $i;
                }
            }
        }
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $sub = Yii::$app->params['subInx'];
            $stats = [ $sub => ($sub > 1) ? User::DEF_STAT : [] ];

            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = UserStatus::INACTIVE;
            $user->statistics = json_encode($stats);
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateToken();
            $user->save();

            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('user');
            $auth->assign($authorRole, $user->getId());

            return $user;
        }

        return null;
    }
}
