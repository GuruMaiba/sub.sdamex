<?php

namespace app\modules\personal\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\Url;
use app\models\User;
use app\models\message\{Dialog, Message};
use app\components\UserStatus;
use app\controllers\AppController;

class MessagesController extends AppController
{
    public function actionIndex($id = 0)
    {
        $this->view->title = "Сообщения | ".Yii::$app->params['shortName'];

        $myId = Yii::$app->user->identity->id;

        $activeMessages = null;
        if ($id > 0 && $id != $myId) {
            $otherUser = User::find()->select(['id','ava','username'])->where(['id'=>$id])->asArray()->limit(1)->one();
            if ($otherUser) {
                $id = (int)$this->createDialog($id)['id'];
                $activeMessages = Message::find()->where(['dialog_id'=>$id])->orderBy('date')->asArray()->limit(Message::VIEW_MESS)->all();
            } else
                $id = 0;
        } else
            $id = 0;
        
        $dialogs = Dialog::find()->where([ 'or',
            ['and', ['first_user_id' => $myId], ['first_del' => false]],
            ['and', ['second_user_id' => $myId], ['second_del' => false]] ])
            ->with([
                'firstUser' => function ($query) {
                    $query->select(['id', 'ava', 'username']);
                },
                'secondUser' => function ($query) {
                    $query->select(['id', 'ava', 'username']);
                },
            ])
            ->orderBy(['last_message_date' => SORT_DESC])->asArray()->all();

        // return $this->debug($dialogs);
        return $this->render('index', [
            'activeId' => $id,
            'otherUser' => $otherUser,
            'messages' => $activeMessages,
            'dialogs' => $dialogs
        ]);
    }

    public function actionCreateDialog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $myId = Yii::$app->user->identity->id;
        $userId = (int)$_POST['user_id'];

        if (empty($userId) || $userId == $myId)
            return ['type' => 'error', 'message' => 'Проблема с id пользователя!'];

        $user = User::find()->select(['id', 'ava', 'username'])->where(['id'=>$userId])->asArray()->limit(1)->one();
        if (!$user)
            return ['type' => 'error', 'message' => 'Пользователь не найден!'];

        $reqCreate = $this->createDialog($userId);
        if ($reqCreate['type'] == 'isset')
            return $reqCreate;

        $model = [
            'id' => $reqCreate['id'],
            'firstUser' => $user,
        ];

        $reqPartial = $this->renderPartial('_dialog', [
            'model' => [$model],
        ]);
        return ['type' => 'success', 'cont' => $reqPartial, 'id' => $reqCreate['id']];
    }

    protected function createDialog($userId) {
        $myId = Yii::$app->user->identity->id;
        $dialog = Dialog::find()->where([ 'or',
            ['and', ['first_user_id' => $userId], ['second_user_id' => $myId]],
            ['and', ['first_user_id' => $myId], ['second_user_id' => $userId]] ])->limit(1)->one();

        if ($dialog) {
            if ($dialog->first_user_id == $myId)
                $dialog->first_del = false;
            if ($dialog->second_user_id == $myId)
                $dialog->second_del = false;

            $dialog->update();
            return ['type' => 'isset', 'id' => $dialog->id];
        }

        $dialog = new Dialog();
        $dialog->first_user_id = $myId;
        $dialog->second_user_id = $userId;
        $dialog->save();

        return ['type' => 'new', 'id' => $dialog->id];
    }

    public function actionDelDialog()
    {
        $myId = Yii::$app->user->identity->id;
        $id = (int)$_POST['id'];

        if (empty($id)) return 0;
        $dialog = Dialog::find()->where([ 'id' => $id ])->limit(1)->one();

        if (!$dialog) return 0;

        if ($dialog->first_user_id == $myId)
            $dialog->first_del = 1;
        else if ($dialog->second_user_id == $myId)
            $dialog->second_del = 1;

        if ($dialog->first_del && $dialog->second_del)
            $dialog->delete();
        else
            $dialog->update();
        
        return 1;
    }

    public function actionUserList()
    {
        $search = $_POST['search'];
        if (empty($search))
            return 0;

        $search = trim($search, "@");
        $users = User::find()
            ->select(['id', 'username'])
            ->where(['like', 'username', $search])
            ->andwhere(['!=', 'id', Yii::$app->user->identity->id])
            ->andwhere(['status' => UserStatus::ACTIVE])
            ->asArray()->limit(4)->all();
            
        return $this->renderPartial('_searchUser', [
            'model' => $users,
        ]);
    }

    public function actionAddMessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int)$_POST['id'];
        $text = $_POST['text'];

        if ($id < 1 || empty($text))
            return ['error' => 'Ошибка данных!'];

        $now = time();
        $myId = Yii::$app->user->identity->id;
        $dialog = Dialog::find()->where(['id'=>$id])->limit(1)->one();

        if ($dialog) {
            $dialog->last_message_date = $now;

            if ($dialog->first_user_id == $myId)
                $dialog->second_miss_message += 1;
            else if ($dialog->second_user_id == $myId)
                $dialog->first_miss_message += 1;
            else
                return ['error' => 'Вы не являетесь участником этого диалога!'];

            $dialog->update();
        } else
            return ['error' => 'Диалог не найден!'];

        $message = new Message();
        $message->dialog_id = $id;
        $message->user_id = $myId;
        $message->message = $text;
        $message->date = time();
        $message->save();

        return [
            'message' => $this->renderPartial('_messages', [
                'messages' => [$message],
                'lastUser' => (int)$_POST['lastUser'],
                'lastDate' => (int)$_POST['lastDate'],
                'type' => 'new',
            ])
        ];
    }

    public function actionViewMessages()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int)$_POST['id'];
        $viewedMess = (int)$_POST['viewedMess'];
        if ($id < 1)
            return ['error' => 'Диалог не найден!'];

        $myId = Yii::$app->user->identity->id;
        $dialog = Dialog::find()->where(['id'=>$id])->limit(1)->one();
        $otherUser = null;
        if ($dialog) {
            if ($viewedMess == 0) {
                if ($dialog->first_user_id == $myId) {
                    $dialog->second_miss_message = 0;
                    $otherUser = $dialog->second_user_id;
                } else if ($dialog->second_user_id == $myId) {
                    $dialog->first_miss_message = 0;
                    $otherUser = $dialog->first_user_id;
                } else
                    return ['error' => 'Вы не являетесь участником этого диалога!'];

                $dialog->update();
                $otherUser = User::find()->select(['id','ava','username'])->where(['id'=>$otherUser])->asArray()->limit(1)->one();
            }            
        } else
            return ['error' => 'Диалог не найден!'];

        $countMess = Message::find()->where(['dialog_id' => $id])->count('[[id]]');
        $offset = ($countMess > Message::VIEW_MESS) ? $countMess - (Message::VIEW_MESS + $viewedMess) : 0;
        $messages = Message::find()->where(['dialog_id'=>$id])->orderBy('date')->asArray()->limit(Message::VIEW_MESS)->offset($offset)->all();

        return [
            'messages' => $this->renderPartial('_messages', [
                'otherUser' => $otherUser,
                'messages' => $messages,
                'type' => 'old',
            ]),
        ];
    }

    public function actionUpdateView() {
        $id = (int)$_POST['id'];
        if ($id < 1)
            return 0;

        Message::updateAll(['view' => 1], ['and', ['dialog_id' => $id], ['!=', 'user_id', Yii::$app->user->identity->id], ['view'=>0]]);
        return 1;
    }

    public function actionCheckMessage()
    {
        $id = $_POST['id'];
        $myId = Yii::$app->user->identity->id;
        $lastId = $_POST['lastId'];
        $lastUser = $_POST['lastUser'];
        $lastDate = $_POST['lastDate'];
        if ($id < 1 || $lastId < 1 || $lastUser < 1 || $lastDate < 1)
            return 0;

        $messages = Message::find()->where(['and',
            ['dialog_id'=>$id],
            ['>', 'id', $lastId],
            ['!=', 'user_id', $myId]
        ])->asArray()->all();

        if (count($messages) > 0) {
            return $this->renderPartial('_messages', [
                'otherUser' => User::find()
                    ->select(['id', 'ava', 'username'])
                    ->where(['id' => $messages[0]['user_id']])
                    ->asArray()->limit(1)->one(),
                'messages' => $messages,
                'lastUser' => $lastUser,
                'lastDate' => $lastDate,
                'type' => 'new',
            ]);
        }
        else 
            return -1;
        
    }
}
