<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Url;
use app\models\form\{Signin, Signup, NewPass};
use app\models\{User, BanUser};
use app\components\{UserStatus};

class AccountController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'login'             => ['GET'],
                    'logout'     => ['POST'],
                    'signin'       => ['POST'],
                    'signup'      => ['POST'],
                    'resetpass'       => ['POST'],
                    'newpass'         => ['POST'],
                    'set-newpass'   => ['POST'],
                    'change-aboutme'    => ['POST'],
                    'review'            => ['POST'],
                    'review-delete'     => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'except' => ['appointment', 'change-skype', 'review', 'review-delete'], // del index
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->request->get('id') > 0 || !Yii::$app->user->isGuest);
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['set-videolink', 'change-time', 'edit-time', 'change-timelock', 'change-aboutme'],
                        'roles' => ['updateProfile'],
                        'roleParams' => ['id' => Yii::$app->request->post('id')],
                    ],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        $this->layout = false;
        if (!Yii::$app->user->isGuest)
            return $this->redirect(['/personal/profile']);

        return $this->render('login', [
            'page'=> ($_GET['p'] != 'signin' && $_GET['p'] != 'signup') ? 'signin' : $_GET['p']
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['/account/login']);
    }

    public function actionSignin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl(Yii::$app->params['listSubs']['MAIN']['link'].'/account/sub-signin')
            ->setData([
                'sub' => Yii::$app->params['subInx'],
                'appKey' => Yii::$app->params['secretKey'],
                'Signin' => $_POST['Signin'],
            ])->send();
            
        if (!$response->isOk)
            return [ 'type' => 'error', 'messages' => ['Что-то пошло не так!'] ];

        if ($response->data['type'] == 'success') {
            $user = User::findByEmail($_POST['Signin']['email']);
            $user->login( ($_POST['Signin']['rememberMe'] == 'on') ? true : false );
        }

        return $response->data;
    }

    public function actionSignup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl(Yii::$app->params['listSubs']['MAIN']['link'].'/account/sub-signup')
            ->setData([
                'sub' => Yii::$app->params['subInx'],
                'appKey' => Yii::$app->params['secretKey'],
                'Signup' => $_POST['Signup'],
            ])->send();
            
        if (!$response->isOk)
            return [ 'type' => 'error', 'messages' => ['Что-то пошло не так!'] ];

        return $response->data;
    }

    public function actionResetpass()
    {
        $request = [ 'type' => 'error', 'messages' => ['Что-то пошло не так!'] ];
        if (isset($_POST['email'])) {
            $request['messages'] = [];
            $user = User::findByEmail($_POST['email']);
            if ($user) {
                $user->generateToken();

                // Settings back url http:/website/account/confirm?id=..&token=..
                $url = Url::toRoute([ 'account/newpass' ], true);
                $url .= '?id=' . $user->id . '&token=' . $user->token;

                // send mail
                $mail = Yii::$app->mailer->compose('newPass', ['url'=>$url]) // result rendering view
                    ->setFrom([ Yii::$app->params['mailingEmail'] => Yii::$app->params['shortName'] ])
                    ->setTo($user->email)
                    ->setSubject('Подтверждение почты')
                    ->send();

                $user->update();
                $request['type'] = 'success';
            } else {
                $request['messages'][] = 'Пользователь с такой почтой не найден.';
            }
        }
        return json_encode($request);
    }

    public function actionNewpass($id, $token)
    {
        $this->layout = false;

        $vars = [
            'page' => 'signin',
            'id' => $id,
            'token' => $token,
            'error' => true,
            'errMessages' => ['Токен не действителен!']
        ];

        $user = User::findByToken($id, $token);
        if (!$user)
            return $this->render('login', $vars);

        $vars['page'] = 'newPass';
        $vars['error'] = false;

        return $this->render('login', $vars);
        // return $this->render('newpass', ['model' => $model]);
    }

    public function actionSetNewpass()
    {
        $request = [ 'type' => 'error', 'messages' => ['Токен не действителен!'] ];
        $user = User::findByToken($_POST['id'], $_POST['token']);
        if ($user) {
            $model = new NewPass();
            // login(rememberMe = true)
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $user->setPassword($model->newPass);
                $user->token = null;
                if ($user->update() && $user->login(true)) {
                    $request['type'] = 'success';
                }
            } else {
                $request['messages'] = [];
                $request['messages'][] = $model->getErrors('newPass')[0];
                $request['messages'][] = $model->getErrors('retypePass')[0];
            }
        }
        return json_encode($request);
    }

    protected function errorList($rq, $err) {
        if (isset($err[0])) {
            $rq['type'] = 'error';
            $rq['messages'][] = '<li>'.$err[0].'</li>';
        }
        return $rq;
    }
}

 ?>
