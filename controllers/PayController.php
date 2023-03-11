<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Url;
use app\models\form\{Signin, Signup, NewPass};
use app\models\{User, BanUser};
use app\components\{UserStatus};

class PayController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                ],
            ],
            // 'access' => [
            //     'class' => \yii\filters\AccessControl::className(),
            //     'except' => ['appointment', 'change-skype', 'review', 'review-delete'], // del index
            //     'rules' => [
            //         [
            //             'allow' => true,
            //             'actions' => ['index'],
            //             'matchCallback' => function ($rule, $action) {
            //                 return (Yii::$app->request->get('id') > 0 || !Yii::$app->user->isGuest);
            //             },
            //         ],
            //     ],
            // ],
        ];
    }

    public function actionIndex()
    {
        // $this->layout = false;

        return $this->render('index', [
            // 'page'=> ($_GET['p'] != 'signin' && $_GET['p'] != 'signup') ? 'signin' : $_GET['p']
        ]);
    }
}

 ?>
