<?php

namespace app\modules\personal\controllers;

use Yii;
// use yii\base\InvalidParamException;
// use yii\web\BadRequestHttpException;
use yii\httpclient\Client;
use yii\web\{Response, UploadedFile};
use app\controllers\AppController;
use app\models\User;
use app\models\promoter\Code;
use app\models\form\UserSettings;
// use vintage\tinify\UploadedFile;

class SettingsController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'change' => ['POST'],
                    'change-ava' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = Yii::$app->user->identity;
        $promoter = [];
        if (Yii::$app->user->can('promoter')) {
            $codes = Code::find()->select(['code'])->where(['and', ['promoter_id'=>$model->id], ['>', 'end_at', time()]])->asArray()->all();
            if (!empty($codes)) {
                foreach ($codes as $code)
                    $promoter['codes'][] = $code['code'];
                
                $codes = $promoter['codes'];
                $promoter['invited'] = User::find()->select(['id','username','invite_code','created_at'])
                    ->where(['in','invite_code',$codes])
                    ->with([
                        'pay' => function ($query) use ($codes) {
                            $query->select(['id','user_id','code','success'])->andWhere(['in','code',$codes])->andWhere(['success'=>1]);
                        }
                    ])->asArray()->all();
            }
        }

        $this->view->title = "Настройки профиля | ".Yii::$app->params['shortName'];

        return $this->render('index', [
            'model' => $model,
            'invite' => (!empty($model->invite_code)) ? Code::find()->where(['code'=>$model->invite_code])->asArray()->limit(1)->one() : null,
            'promoter' => $promoter,
        ]);
    }

    public function actionChange()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl(Yii::$app->params['listSubs'][1]['link'].'account/change-option')
            ->setData([
                'secretKey' => Yii::$app->params['secretKey'],
                'user_id' => Yii::$app->user->identity->id,
                'UserSettings' => $_POST['UserSettings'],
                ])
            ->send();

        if (!$response->isOk)
            return ['error' => [($response->data['error']) ? $response->data['error'] : 'Что-то пошло не так!']];

        return $response->data;
    }

    public function actionChangeAva()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('ava');

        if (!strripos($file->type, 'jpg') && !strripos($file->type, 'jpeg') && !strripos($file->type, 'png'))
            return ['error' => 'Необходим другой формат файла: jpg, jpeg, png!'];
        
        if ($file->size > 10000000)
            return ['error' => 'Изображение слишком большого размера, максимум 10МБ!'];
        
        $name = $file->name;
        $path = Yii::getAlias("@imgTeamp");

        while (file_exists("$path/$name"))
            $name = 's'.$name;

        $path = "$path/$name";
        $file->saveAs($path);

        // Получаем атрибуты изображения
        list($avaWidth, $avaHeight) = getimagesize($path);
        $size = 250;

        if ($avaWidth < $size || $avaHeight < $size) {
            unlink($path);
            return ['error' => "Слишком маленькое изображение, минимальный размер $size х $size px!"];
        }
        
        if ($file) {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl(Yii::$app->params['listSubs'][1]['link'].'account/sub-change-ava')
                ->setData([
                    'secretKey' => Yii::$app->params['secretKey'],
                    'user_id' => Yii::$app->user->identity->id,
                    'sub' => Yii::$app->params['subInx'],
                    'Coords' => $_POST['Coords'],
                    'notCrop' => ($avaWidth == $avaHeight) ? true : false, 
                    ])
                ->addFile('ava', $path)
                ->send();

            unlink($path);

            // return $response->data;

            if (!$response->isOk)
                return ['error' => ($response->data['error']) ? $response->data['error'] : 'Что-то пошло не так!'];

            return $response->data;
        }

        return ['error' => 'Изображение не загрузилось!'];
    }
}
