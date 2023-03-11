<?php

namespace app\models\form;

use Yii;
use app\components\UserStatus;
use app\models\{User, BanUser};
use yii\base\Model;

class Signin extends Model
{
    public $email;
    public $password;
    public $rememberMe      = false;
    public $GMT             = 0;

    private $_user          = null;
    private $_ban           = null;
    const ALLOW_COUNT_ERROR = 6;

    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            // email must be a email value
            ['email', 'email'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'string', 'min' => '6'],
        ];
    }

    public function attributeLabels() {
        return [
            'email' => 'E-mail',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

     /**
      * Validates the password.
      * This method serves as the inline validation for password.
      *
      * @param string $attribute the attribute currently being validated
      * @param array $params the additional name-value pairs given in the rule
      */
    public function validateUser()
    {
        // Получаем куку с ошибками
        $error = $_COOKIE['signin_error'];

        // Если куки пусты
        if (!$error) {
            $user = $this->getUser(); // Получаем пользователя
            $error = 'Неверно введён email или пароль!'; // default error

            if ($user) {
                // Если пользователь не активировал аккаунт
                if ($user->status == UserStatus::INACTIVE) {
                    // DEBUG: добавить ссылку
                    $error = 'Ваша учётная запись не активирована! Для повторной отправки письма ...';
                // Если пользователь имеет бан
                } else if ($user->isBan()) {
                    $ban = $user->getBan();
                    $error = $ban->banCheck($user->status, $GMT);

                    // value = Email + &cstsplit; + ErrorMessage
                    setcookie('login_error', $user->email . '&cstsplit;' . $error, $ban->ban_end);
                } else {
                    // Если набран не верный пароль
                    if (!$user->validatePassword($this->password)) {
                        $user->count_login_error += 1; // Счётчик количества ошибок

                        $allow = self::ALLOW_COUNT_ERROR; // кол. разрешённых ошибок
                        $count = $user->count_login_error; // кол. набранных ошибок

                        // Если количество ошибок превысило дозволенное, тогда
                        if ($count >= $allow) {
                            $ban = $user->getBan(); // даём бан
                            $ban->addTimeBanLogin($user->status); // высчитываем время бана

                            // value = Email + &cstsplit; + ErrorMessage
                            setcookie('login_error', $user->email. '&cstsplit;' .$error, $ban->ban_end); // ставим куку

                            $user->status = UserStatus::LOGIN_ERROR; // меняем статус
                            $user->update();
                        // if count error is NOT more than the allowed value
                        } else {
                            $try = $allow - $count; // оставшиеся попытки
                            // if try - 0 < value <= 3
                            if ( $try <= 5 && $try > 0) {
                                $error .= ' У вас осталось ' . $try . ' попытки!';
                            }
                        }
                    // УСПЕХ
                    } else
                        $error = null;
                } // end if user status ban
            } // end if user
        } // end if error

        // если есть ошбики
        if ($error) {
            $this->addError('allErrors', $error); // передаём в модель
            return false; // не прошёл валидацию
        }

        return true; // прошёл валидацию
    }

     /**
      * Logs in a user using the provided username and password.
      *
      * @return boolean whether the user is logged in successfully
      */
    public function login()
    {
        $user = $this->getUser();
        $user->updated_at = time();     // time login
        $user->count_login_error = 0;   // reset the counter
        $user->status = UserStatus::ACTIVE;

        // check stats, if empty - add
        $sub = Yii::$app->params['subInx'];
        $fullStats = json_decode($user->statistics, true);
        if ( empty($fullStats[$sub]) ) {
            $fullStats[$sub] = ($sub != Yii::$app->params['listSubs']['MAIN']) ? User::DEF_STAT : [];
            $user->statistics = json_encode($fullStats);
        }

        $user->update();

        return $user->login($this->rememberMe);
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    /**
     * Finds ban by user [[id]]
     *
     * @return BanUser|null
     */
    public function getBan()
    {
        $user = $this->getUser();
        if ($this->_ban === null) {
            $this->_ban = BanUser::findOne($user->id);
        }

        return $this->_ban;
    }
}
