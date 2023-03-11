<?php

namespace app\controllers;

use Yii;
use app\models\{User, UserLevel, Level};
use app\models\course\{Student};
use app\models\webinar\{Webinar, Member, Comment, Chat, ChatBan};
use yii\web\HttpException;

class WebinarsController extends AppController
{
    public function rules()
    {
        return [
            [
                ['attribute1', 'attribute2', 'attribute3'],
                'filter',
                'filter' => function ($value) {
                    return strip_tags($value);
                }
            ],
        ];
    }

    public function actionIndex()
    {
        $short = Yii::$app->params['shortName'];
        $lable = Yii::$app->params['listSubs'][Yii::$app->params['subInx']]['lable'];
        $this->view->title = "Онлайн-вебинары по подготовке к экзаменам ОГЭ и ЕГЭ ".date('Y')." | ".strtoupper($lable)." / $short";
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => "Онлайн-вебинары с разборами и решением экзаменационных заданий по предмету - $lable, так же доступных в записи, в том числе бесплатно бесплатно."
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'бесплатные, вебинары, онлайн, подготовка, экзамены, ОГЭ, ЕГЭ, '.$lable.', '.Yii::$app->params['commonKeyWords'],
        ]);

        return $this->render('index', [
            'model' => Webinar::find()->alias('w')
                ->select('w.*')
                ->addSelect("(SELECT COUNT(*) FROM webinar_member AS m WHERE w.id = m.webinar_id) as countMembers")
                ->addSelect("(SELECT COUNT(*) FROM webinar_comment AS c WHERE w.id = c.webinar_id) as countComments")
                ->joinWith(['members m', 'comments c'], false)
                ->where(['and',
                    ['publish' => 1],
                    ['in','subject_id', [ 1, Yii::$app->params['subInx'] ]]
                ])
                ->groupBy('w.id')->orderBy(['start_at' => SORT_DESC])->asArray()->all(),
        ]);
    }

    public function actionDetails($id)
    {
        $myId = Yii::$app->user->identity->id;
        $model = Webinar::find()->where(['id'=>$id])->with(['author', 'commentslim', 'commentslim.user', 'courses'])->asArray()->limit(1)->one();
        if (!$model || (!$model['publish'] && $model['author_id'] != $myId))
            throw new HttpException(404 ,'Страница не найдена!');

        // $model->updateOffsetChat($model->chatMessCount);
        $is = [
            'prem' => false,
            'guest' => Yii::$app->user->isGuest,
            'author' => $myId == $model['author_id'],
            'member' => ((int)$model['cost'] < 1) ? true : Member::find()->having(['webinar_id'=>$id, 'user_id'=>$myId])->limit(1)->exists(),
        ];
        $gmt = $_COOKIE['GMT'];
        $model['strDate'] = ($gmt) ? strtotime($gmt.' hours', $model['start_at']) : $model['start_at'];
        $model['strDate'] = $this->ruMonth(date('d F Y H:i', $model['strDate']));

        if (!$is['guest']) {
            foreach ($model['courses'] as $course) {
                $student = Student::find()->where(['learner_id'=>$myId, 'course_id'=>$course['id']])->asArray()->limit(1)->one();
                if ( $student && $student['end_at'] > (time()+($gmt*3600)) ) {
                    $is['prem'] = true;
                    $is['member'] = true;
                    break;
                }
            }
        }

        $short = Yii::$app->params['shortName'];
        $lable = Yii::$app->params['listSubs'][Yii::$app->params['subInx']]['lable'];
        $this->view->title = "$model[title] | ".strtoupper($lable)." / $short";
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => substr(strip_tags($model['desc']), 0, 300),
        ]);
        $keys = str_replace($model['title'],'',[',','.','!','?','$','"','(',')','-','+','_','=','/','|']);
        $keys = str_replace($keys,', ',' ');
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => "вебинар, онлайн, $keys, ".$lable.", ".Yii::$app->params['commonKeyWords'],
        ]);

        return $this->render('details', [
            'model' => $model,
            'countMembers' => Member::find()->having(['webinar_id'=>$id])->count(),
            'countComments' => Comment::find()->having(['webinar_id'=>$id])->count(),
            'is' => $is,
        ]);
    }

    public function actionStartEnd() {
        $webId = $_POST['id'];
        $type = $_POST['type'];
        $myId = Yii::$app->user->identity->id;
        
        if (empty($webId) || empty($type))
            return 0;

        $web = Webinar::find()->where(['id'=>$webId])->limit(1)->one();
        if (!$web || $web->author_id != $myId)
            return 0;

        if ($type == 1)
            $web->start = 1;
        else
            $web->end = 1;

        $web->update();
        return 1;
    }

    public function actionCheckStatus() {
        $webId = $_POST['id'];
        
        if (empty($webId))
            return 0;

        $web = Webinar::find()->select(['id', 'start', 'end'])->where(['id'=>$webId])->asArray()->limit(1)->one();
        if (!$web)
            return 0;

        if ($web['start'])
            return (!$web['end']) ? 1 : 2;

        return -1;
    }

    public function actionChat($id = 0)
    {
        $this->layout = false;
        if ($id <= 0) // DEBUG Yii::$app->user->isGuest
            throw new HttpException(404 ,'Страница не найдена!');

        $web = Webinar::find()
            ->select(['id','author_id','ava','publish','start','end','start_at', 'cost'])
            ->where(['id'=>$id])->with([
                'courses' => function ($query) {
                    $query->select(['id', 'subject_id']);
                }
            ])->asArray()->limit(1)->one();

        $is = [
            'prem' => false,
            'frame' => $_GET['frame'],
            'author' => false,
            'moder' => false,
            'member' => false,
            'ban' => false,
        ];
        if (!Yii::$app->user->isGuest) {
            $myId = Yii::$app->user->identity->id;
            $is['author'] = ($web['author_id'] == $myId);
            $is['moder'] = Yii::$app->user->can('moderator');
            $is['member'] = ((int)$web['cost'] < 1) ? true : Member::find()->where(['webinar_id'=>$id, 'user_id'=>$myId])->limit(1)->exists();
            $is['ban'] = ChatBan::find()->where(['webinar_id' => $id, 'user_id' => $myId])->limit(1)->exists();

            foreach ($web['courses'] as $course) {
                $student = Student::find()->where(['learner_id'=>$myId, 'course_id'=>$course['id']])->asArray()->limit(1)->one();
                if ( $student && $student['end_at'] > (time()+($gmt*3600)) ) {
                    $is['prem'] = true;
                    $is['member'] = true;
                    break;
                }
            }
        } else
            $is['member'] = ((int)$web['cost'] < 1) ? true : false;

        $admin = $is['author'] || $is['moder'];
        if (!$web || (!$web['publish'] && !$admin) || (!$web['start'] && !$admin) || $web['end']
            || !$is['member'])
            throw new HttpException(404 ,'Страница не найдена!');

        $model = Chat::find()->where(['webinar_id'=>$id])
            ->with([
                'user' => function ($query) {
                    $query->select(['id', 'ava', 'username']);
                }
            ])
            ->orderBy(['id' => SORT_DESC])
            ->limit(Webinar::COUNT_VIEW_CHAT)
            ->asArray()->all();

        return $this->render('chat', [
            'model' => $model,
            'webinar' => $web,
            'is' => $is,
        ]);
    }

    public function actionAddLike()
    {
        $webId = $_POST['id'];
        $userId = $_POST['user_id'];
        $myId = Yii::$app->user->identity->id;
        $sub = Yii::$app->params['subInx'];
        
        if (empty($webId) || empty($userId))
            return 0;

        $web = Webinar::find()->select(['id', 'author_id'])->where(['id'=>$webId, 'author_id'=>$myId])->asArray()->limit(1)->one();
        $user = User::find()->where(['id'=>$userId])->limit(1)->one();
        if ($web != [] && $user) {
            $stats = json_decode($user->statistics, true);
            $teacherStat = (array)$stats[$sub]['teachers'][$myId];
            
            $teacherStat['count_likes'] = ($teacherStat['count_likes'] > 0) ? $teacherStat['count_likes']+1 : 1;

            $stats[$sub]['teachers'][$myId] = $teacherStat;
            $user->statistics = json_encode($stats);
            $user->update();

            return 1;
        } else
            return 0;
    }

    public function actionAddExp()
    {
        $webId = $_POST['id'];
        $userId = $_POST['user_id'];
        $type = $_POST['exp']; // 1,2,3
        $exp = [
            1 => 6,
            2 => 18,
            3 => 36
        ];

        if (empty($webId) || empty($userId) || empty($type) || $type < 1 || $type > 3)
            return 0;

        $myId = Yii::$app->user->identity->id;
        $sub = Yii::$app->params['subInx'];
        $web = Webinar::find()->select(['id', 'author_id'])
            ->where(['id'=>$webId, 'author_id'=>Yii::$app->user->identity->id])
            ->asArray()->limit(1)->one();
        $user = User::find()->where(['id'=>$userId])->limit(1)->one();
        if ($web != [] && $user) {
            $user->addExp($exp[$type]);
            $stats = json_decode($user->statistics, true);
            $teacherStat = (array)$stats[$sub]['teachers'][$myId];
            
            $teacherStat['count_exp'] = ($teacherStat['count_exp'] > 0) ? $teacherStat['count_exp']+$exp[$type] : $exp[$type];

            $stats[$sub]['teachers'][$myId] = $teacherStat;
            $user->statistics = json_encode($stats);
            $user->update();
            return 1;
        } else
            return 0;
    }

    public function actionAddMessage()
    {
        if (Yii::$app->user->isGuest) // DEBUG: перенести в behaviors
            return 0;

        $id = $_POST['id'];
        $message = $_POST['message'];
        $myId = Yii::$app->user->identity->id;

        $web = Webinar::find()->where(['id'=>$id,'publish'=>1])->with(['courses'])->asArray()->limit(1)->one();

        // $isPrem = false;
        $isMember = ((int)$web['cost'] < 1) ? true : false;
        if ((int)$web['cost'] > 0) {
            foreach ($web['courses'] as $course) {
                $student = Student::find()->where(['learner_id'=>$myId, 'course_id'=>$course['id']])->asArray()->limit(1)->one();
                if ( $student && $student['end_at'] > (time()+($gmt*3600)) ) {
                    // $isPrem = true;
                    $isMember = true;
                    break;
                }
            }
        }

        if (!$isMember)
            $isMember = Member::find()->where(['webinar_id' => $id, 'user_id' => $myId])->limit(1)->exists();

        if (empty($id) || $id < 1 || empty($message) || !$web || !$isMember)
            return 0;

        if (ChatBan::find()->where(['webinar_id' => $id, 'user_id' => $myId])->limit(1)->exists())
            return -1;

        $model = new Chat;
        $model->webinar_id = $id;
        $model->user_id = $myId;
        $model->message = $message;
        $model->save();
        
        return $this->renderPartial('_messages', [
            'model' => [$model],
            'isAdd' => true
        ]);
    }

    public function actionCheckMessage()
    {
        $id = $_POST['id'];
        $last = $_POST['lastId'];

        if (empty($id) || $id < 1)
            return 0;

        $last = (empty($last)) ? 0 : $last;
        $myId = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->identity->id;
        $list = Chat::find()->where(['webinar_id' => $id])
        // ->where(['and',
        //     ['!=', 'user_id', $myId],
        //     ['>', 'id', $last]
        // ])
        ->andWhere(['!=', 'user_id', $myId])
        ->andWhere(['>', 'id', $last])
        ->with([
            'user' => function ($query) {
                $query->select(['id', 'ava', 'username']);
            }
        ])->asArray()->all();

        if ($list == [])
            return -1;
        
        $web = Webinar::find()->select(['id','author_id'])->where(['id'=>$id])->asArray()->limit(1)->one();
        return $this->renderPartial('_messages', [
            'model' => $list,
            'isAuthor' => $web['author_id'] == $myId,
            'isModer' => Yii::$app->user->can('moderator'),
        ]);
    }

    public function actionAddBan()
    {
        $id = $_POST['id'];
        $userId = $_POST['user_id'];
        $myId = Yii::$app->user->identity->id;
        if (empty($id) || $id < 1 || empty($userId) || $userId < 1)
            return 0;

        $web = Webinar::find()->where(['id'=>$id, 'publish'=>1])->asArray()->limit(1)->one();
        if (!$web || $web['author_id'] != $myId || $web['end'])
            return 0;

        $ban = ChatBan::find()->where(['webinar_id' => $id, 'user_id' => $userId])->limit(1)->one();
        if (!$ban) {
            $ban = new ChatBan;
            $ban->webinar_id = $id;
            $ban->user_id = $userId;
            $ban->save();
        }
        return 1;
    }

    public function actionMember()
    {
        $id = $_GET['id'];
        $id = $_POST['id'];
        if (empty($id) || $id < 1)
            return 0;

        $webinar = Webinar::find()->where(['id'=>$id])->asArray()->limit(1)->one();
        if (!$webinar || $webinar['cost'] > 0)
            return 0;

        $member = Member::find()->where(['webinar_id' => $id, 'user_id' => Yii::$app->user->identity->id])->limit(1)->one();
        if ($_POST['type'] == 'add') {
            if (!$member) {
                $member = new Member;
                $member->webinar_id = $id;
                $member->user_id = Yii::$app->user->identity->id;
                $member->save();
            }
        } else {
            if ($member)
                $member->delete();
        }
        return 1;
    }

    public function actionGetComments()
    {
        $id = $_POST['id'];
        $count = $_POST['count'];
        if (empty($id) || $id < 1 || empty($count))
            return 0;

        $web = Webinar::find()->where(['id'=>$id])->asArray()->limit(1)->one();
        if (!$web);
            return 0;

        $model = Comment::find()
            ->where(['webinar_id' => $id])
            ->with(['user'])
            ->orderBy(['create_at' => SORT_DESC])
            ->limit(Webinar::COUNT_VIEW_COMM)
            ->offset($count)
            ->asArray()->all();
        
        if ($model != [])
            return $this->renderPartial('_comments', [
                'model' => $model,
                // 'isAuthor' => $web['author_id'] == Yii::$app->user->identity->id,
                // 'isModer' => Yii::$app->user->can('moderator'),
            ]);
        else
            return 1;
    }

    public function actionAddComment()
    {
        $id = $_POST['id'];
        $comment = $_POST['comment'];

        if (empty($id) || $id < 1 || empty($comment) || Yii::$app->user->isGuest) // DEBUG
            return 0;

        $myId = Yii::$app->user->identity->id;
        $web = Webinar::find()->where(['id'=>$id])->with(['courses'])->asArray()->limit(1)->one();
        $isMember = ((int)$web['cost'] < 1) ? true : false;

        if ((int)$web['cost'] > 0) {
            foreach ($web['courses'] as $course) {
                $student = Student::find()->where(['learner_id'=>$myId, 'course_id'=>$course['id']])->asArray()->limit(1)->one();
                if ( $student && $student['end_at'] > (time()+($gmt*3600)) ) {
                    // $isPrem = true;
                    $isMember = true;
                    break;
                }
            }
        }

        if (!$isMember)
            $isMember = Member::find()->where(['webinar_id' => $id, 'user_id' => Yii::$app->user->identity->id])->limit(1)->exists();

        if (empty($id) || $id < 1 || empty($comment) || !$web || !$isMember)
            return 0;

        $model = new Comment;
        $model->webinar_id = $id;
        $model->user_id = Yii::$app->user->identity->id;
        $model->message = $comment;
        $model->create_at = time();
        $model->save();

        return $this->renderPartial('_comments', [
            'model' => [$model],
            'isAdd' => true
        ]);
    }

    public function actionEditComment()
    {
        $id = $_POST['id'];
        $comment = $_POST['comment'];
        if (empty($id) || $id < 1 || empty($comment))
            return 0;

        $model = Comment::find()->where(['id'=>$id])->limit(1)->one();
        if ($model && ($model->user_id == Yii::$app->user->identity->id || Yii::$app->user->can('moderator'))) {
            $model->message = $comment;
            $model->update();
        }
        
        return 1;
    }

    public function actionDelComment()
    {
        $id = $_POST['id'];
        if (empty($id) || $id < 1)
            return 0;

        $model = Comment::find()->where(['id'=>$id])->limit(1)->one();
        if ($model && ($model->user_id == Yii::$app->user->identity->id || Yii::$app->user->can('moderator')))
            $model->delete();
        
        return 1;
    }
}
