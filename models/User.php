<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use app\components\{UserStatus, AppointmentStatus};
use app\models\form\Login;
use app\models\teacher\{Appointment, AppointmentArchive};

/**
 * User model
 *
 * @property integer $id
 * @property string $ava
 * @property string $username
 * @property string $email
 * @property string $new_email
 * @property string $phone
 * @property string $name
 * @property string $middlename
 * @property string $phrase (text)
 * @property string $password_hash
 * @property string $token
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $count_login_error
 * @property integer $teacher_class
 * @property decimal $cash (money 19,0)
 *
 *
 * @property UserProfile $profile
 */

class User extends ActiveRecord implements IdentityInterface
{
    // default ava
    public const DEF_AVA = 'no_img.jpg';
    // delete ava
    public const DEL_AVA = 'del_img.jpg';

    // default statistics
    public const DEF_STAT = [
        'courses' => [
            // 'course_id' => [
            //     'end' => 0,
            //     'modules' => [
            //         'module_id' => [
            //             'end' => 0,
            //             'lessons' => [
            //                 'lesson_id' => [
            //                     'end' => 0,
            //                     'test_id' => ['completed'=>0, 'exp'=>5, 'points' => 3],
            //                     'write_id' => ['completed'=>0, 'exp'=>5, 'right' => 0],
            //                 ],
            //             ],
            //         ],
            //     ],
            // ],
        ],
        'webinars' => [
            'count_viewed' => 0, // количсевто просмотренных вебинаров
        ],
        'exams' => [
            'count_corr' => 0,  // количество правильных заданий
            'count_err' => 0,  // количество неправильных заданий
            'full_last' => [    // результаты последнего полного экзамена
                'id' => 0,
                'date' => 0,
                'number_attempts' => 0,
                'points' => 0,
                'mark' => 0,
            ],
            'list' => [
                // 'full_id' => [
                //     'sections' => [
                //         'sect_id' => [
                //             'exercises' => [
                //                 'exe_id' => [
                //                     // DEF_STAT_EXE
                //                 ],
                //             ],
                //         ],
                //     ],
                //     'themes' => [
                //         'theme_id' => [
                //             // DEF_STAT_THEME
                //         ],
                //     ],
                // ],
            ],
        ],
        'teachers' => [
            // teacher_id => [
            //     'count_class' => 0, // кол. пройденных уроков
            //     'count_likes' => 0, // кол. полученных лайков всего
            //     'count_exp' => 0,   // кол. полученного опыта ???
            // ], // end teacher_id
        ], // teachers
    ]; // stats

    // default statistics
    public const DEF_STAT_EXE = [
        'count_corr' => 0, // кол. правильных заданий
        'count_err' => 0, // кол. неправильных заданий
        'completed_list' => [], // выполненные упражнения
        'last' => [], // последние задания
        'percent_last' => 0, // %
        'percent_all' => 0, // %
    ];

    // default statistics
    public const DEF_STAT_THEME = [
        'count_corr' => 0, // кол. правильных заданий
        'count_err' => 0, // кол. неправильных заданий
        'last' => [], // последние темы
        'percent_last' => 0, // %
        'percent_all' => 0, // %
    ];

    public $image;
    public $_ban = null;
    public $_role = null;
    public $_teacherOption = null;

    // public static function getDb()
    // {
    //     return \Yii::$app->commondb;  
    // }

    public static function tableName()
    {
        return 'user';
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Nickname',
            'email' => 'E-mail',
            'phone' => 'Телелефон',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'phrase' => 'Мои мысли',
            'status' => 'Статус',
            'teacher_class' => 'Количество доступных занятий',
            'created_at' => 'Дата Регистрации',
            'updated_at' => 'Дата Обновновления',
            'cash' => 'Баланс',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function login($rememberMe)
    {
        return Yii::$app->getUser()->login($this, $rememberMe ? 3600 * 24 * 30 : 0);
    }

    public function isBan()
    {
        $ban = $this->getBan();
        if ($ban && ( $this->status == UserStatus::PERMANENT_BAN || $ban->ban_end > time() ) )
            return true;
        else
            return false;
    }

    public function addExp($exp)
    {
        $sub = Yii::$app->params['subInx'];
        $model = UserLevel::find()->where(['user_id' => $this->id, 'subject_id' => $sub])->limit(1)->one();
        if (!$model) {
            $model = new UserLevel;
            $model->user_id = $this->id;
            $model->subject_id = $sub;
            $model->lvl = 1;
            $model->exp = 0;
        }
        
        $model->exp += $exp;
        $max = Level::find()->where(['isMax' => true])->asArray()->limit(1)->one();
        if ($max && $max['id'] > $model->lvl) {
            $numb = $model->lvl;
            do {
                $model->lvl = $numb;
                ++$numb;
                $nextLvl = Level::find()->where(['id' => $numb])->asArray()->limit(1)->one();
            } while ($model->exp > $nextLvl['exp']);
        }
        
        $model->save();
    }

    public function getLevel()
    {
        $range = ['MAX', 'MAX'];
        
        if (Yii::$app->user->can('teacher'))
            return [
                'lvl' => 888,
                'rangeExp' => $range,
            ];

        $sub = Yii::$app->params['subInx'];
        $model = UserLevel::find()->where(['user_id' => $this->id, 'subject_id' => $sub])->limit(1)->one();
        if (!$model) {
            $model = new UserLevel;
            $model->user_id = $this->id;
            $model->subject_id = $sub;
            $model->exp = 0;
            $model->lvl = 1;
            $model->save();
        }

        $max = Level::find()->where(['isMax' => true])->limit(1)->one();
        if ($max && $max->id > $model->lvl) {
            $rangeLevels = Level::find()->where(['in','id',[$model->lvl, ($model->lvl+1)]])->asArray()->limit(2)->all();
            if ($rangeLevels[0] && $rangeLevels[1])
                $range = [$rangeLevels[0]['exp'], $rangeLevels[1]['exp']];
        }

        return [
            'lvl' => $model->lvl,
            'exp' => $model->exp,
            'rangeExp' => $range,
        ];
    }

    public function checkStat()
    {
        $sub = Yii::$app->params['subInx'];
        $user = Yii::$app->user->identity;
        $stats = json_decode($user->statistics, true);
        if (empty($stats[$sub])) {
            $stats[$sub] = User::DEF_STAT;
            $user->statistics = json_encode($stats);
            $user->update();
        }
    }

/**
 * GET
 * -----------------------------------------------------
 */

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getBan()
    {
        if ($this->_ban === null) {
            $this->_ban = BanUser::findOne($this->id);
        }

        return $this->_ban;
    }

    public function getRole()
    {
        if ($this->_role === null) {
            $this->_role = current(Yii::$app->authManager->getRolesByUser($this->id));
            if (!$this->_role)
                return null;
        }

        return $this->_role;
    }

/**
 * ---------------------
 * END GET
 */



/**
 * SET
 * -----------------------------------------------------
 */
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        return $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function setRole($role_name = null)
    {
        if ($role_name != null && $role_name != '') {
            $oldRole = $this->role;
            if ($oldRole->name != 'MegaAdmin' && $oldRole->name != $role_name) {
                $auth = Yii::$app->authManager;
                $authRole = $auth->getRole($role_name);
                $auth->assign($authRole, $this->id);
                if ($oldRole != null)
                    $auth->revoke( $oldRole, $this->id );

                if ($role_name == 'teacher' && !Teacher::find()->where([ 'user_id' => $this->id ] )->exists() ) {
                    $option = new Teacher();
                    $option->user_id = $this->id;
                    // $stats = [];
                    // $stats[date('Y')][date('n')] = Teacher::DEF_STAT_MONTH;
                    // $option->statistics = json_encode($stats);
                    $option->save();
                }
            }
        }
    }
/**
 * ---------------------
 * END SET
 */



/**
 * FIND BY
 * -----------------------------------------------------
 */
    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id, 'status' => UserStatus::ACTIVE])->limit(1)->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->limit(1)->one();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find()->where(['email' => $email])->limit(1)->one();
    }

    /**
     * Finds user by id and token
     *
     * @param string $token for reset password and change email
     * @return static|null
     */
    public static function findByToken($id = 0, $token)
    {
        if ($id == 0 || !static::timeTokenValid($token)) {
            return null;
        }

        return static::find()->where([ 'id' => $id, 'token' => $token ])->limit(1)->one();
    }

/**
 * ---------------------
 * END FIND BY
 */



/**
 * GENERATE
 * -----------------------------------------------------
 */

    public function generateToken()
    {
        return $this->token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateAuthKey()
    {
        return $this->auth_key = Yii::$app->security->generateRandomString();
    }

/**
 * ---------------------
 * END GENERATE
 */



 /**
  * VALIDATE
  * -----------------------------------------------------
  */

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validate token
     *
     * @param string $token
     * @return true|false
     */
    public function validateToken($token)
    {
        return $this->token === $token;
    }

    /**
     * Finds out if token is valid
     *
     * @param string $token for reset password and change email
     * @return boolean
     */
    public static function timeTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + Yii::$app->params['user.timeTokenExpire'] >= time();
    }

/**
 * ---------------------
 * END VALIDATE
 */

 /**
  * GET
  * ---------------------
  */

    public function getTeachers()
    {
        return $this->hasMany(Teacher::className(), ['student_id' => 'id'])->viaTable('teacher_student', ['teacher_id' => 'user_id']);
    }

    public function getBanUser()
    {
        return $this->hasOne(BanUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamprogresses()
    {
        return $this->hasMany(Examprogress::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEWAnswers()
    {
        return $this->hasMany(EWAnswer::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLearner()
    {
        return $this->hasOne(Learner::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgressLearnerCourses()
    {
        return $this->hasMany(ProgressLearnerCourse::className(), ['learner_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Teacher::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveAppointments()
    {
        return $this->hasMany(Appointment::className(), ['student_id' => 'id'])
            ->where(['in', 'status', [
                AppointmentStatus::ACTIVE,
                AppointmentStatus::TEACHER_SUCCESS,
                AppointmentStatus::TEACHER_ERROR,
                AppointmentStatus::TEACHER_DIFFICULT
            ]])->with('archive')->orderBy('begin_date');
    }

    public function getArchiveAppointments()
    {
        return $this->hasMany(AppointmentArchive::className(), ['student_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebinars()
    {
        return $this->hasMany(Webinar::className(), ['user_id' => 'id']);
    }

    public function getPay()
    {
        return $this->hasMany(Payment::className(), ['user_id' => 'id']);
    }

/**
 * ---------------------
 * END GET
 */

} // end User
