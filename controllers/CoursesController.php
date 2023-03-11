<?php

namespace app\controllers;

use Yii;
use app\models\exam\test\Test;
use app\models\exam\write\{Write, Reply};
use app\models\course\{Course, Module, Lesson, Student};
use app\models\promoter\Code;
use app\components\CodeType;
use yii\web\Response;
use yii\web\HttpException;

class CoursesController extends AppController
{
    public $endCourseSale = 10;

    public function actionIndex()
    {
        $crs = Course::find()->where(['and',
                ['publish' => 1],
                ['in','subject_id', [ 1, Yii::$app->params['subInx'] ]]
            ])
            ->with([
                'modules' => function ($query) {
                    $query->select(['id', 'course_id'])->where(['publish'=>1]);
                },
                'modules.lessons' => function ($query) {
                    $query->select(['id', 'module_id', 'examtest_id', 'examwrite_id'])->where(['publish'=>1]);
                },
                'modules.lessons.test' => function ($query) {
                    $query->select(['id', 'lesson_id'])->where(['publish'=>1]);
                },
                'modules.lessons.write' => function ($query) {
                    $query->select(['id', 'lesson_id'])->where(['publish'=>1]);
                },
                'webinars' => function ($query) {
                    $query->select(['id'])->where(['publish'=>1]);
                }
                ])->asArray()->all();

        $short = Yii::$app->params['shortName'];
        $sub = Yii::$app->params['listSubs'][Yii::$app->params['subInx']];
        $lable = strtoupper($sub['lable']);
        $this->view->title = "Онлайн-курсы по подготовке к экзаменам ОГЭ и ЕГЭ ".date('Y')." | $lable / $short";
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => "$lable / $short - это сборник качественных онлайн-курсов, которые включают в себя подробнейшие уроки по всем темам и заданиям выбранного экзамена."
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'курсы, онлайн-курсы, уроки, подготовка, экзамены, ОГЭ, ЕГЭ, '.$lable.', '.Yii::$app->params['commonKeyWords'],
        ]);
        $this->view->registerLinkTag(['rel' => 'canonical', 'href' => "$sub[link]courses"]);
        return $this->render('index', [
            'model' => $crs,
        ]);
    }

    public function actionDetails($id = 0)
    {
        if ($id <= 0)
            throw new HttpException(404);

        $model = Course::find()->where(['id'=>$id, 'publish'=>1])->with([
            'author' => function ($query) {
                $query->select(['id', 'ava', 'username', 'name', 'surname'])->with([
                    'teacher' => function ($query) {
                        $query->select(['user_id', 'video']);
                    }
                ]);
            },
            'modules' => function ($query) {
                $query->select(['id', 'course_id', 'title', 'free'])->where(['publish'=>1]);
            },
            'modules.lessons' => function ($query) {
                $query->select(['id', 'module_id', 'examtest_id', 'examwrite_id', 'free'])->where(['publish'=>1]);
            },
            'modules.lessons.test' => function ($query) {
                $query->select(['id', 'lesson_id'])->where(['publish'=>1])->with(['questions']);
            },
            'modules.lessons.write' => function ($query) {
                $query->select(['id', 'lesson_id'])->where(['publish'=>1]);
            }, 
            'webinars' => function ($query) {
                $query->select(['id', 'title', 'ava'])->where(['publish'=>1]);
            }
            ])->asArray()->limit(1)->one();

        if (!$model)
            throw new HttpException(404);

        // $gmt = $_COOKIE['GMT'];
        // if ($gmt)
        //     $model->start_at = strtotime($gmt.' hours', $model->start_at);
        // $model->strDate = date('d.m.Y H:i', $model->start_at);

        $myId = 0;
        $isPrem = 0;
        $model['old_cost'] = $model['cost'];
        $user = Yii::$app->user->identity;
        if (!Yii::$app->user->isGuest) {
            $myId = $user->id;
            $access = Student::find()->where(['learner_id'=>$myId, 'course_id'=>$id])->asArray()->limit(1)->one();
            $isPrem = (isset($access) && $access['end_at'] > time());

            if (!$isPrem) {
                $model['cost'] = $this->getSale($user, $model['id'], $model['cost']);
                // $invite = $user->invite_code;
                // $stats = json_decode($user->statistics, true);
                // if ($model['cost'] >= 1500 && !empty($invite) && ($invite != 'SPENT' || !empty($stats[1]['saleCode']))) {
                //     $code = $invite;
                //     if (!empty($stats[1]['saleCode'])) {
                //         if ($stats[1]['saleEnd'] > time() && !in_array($course['id'], $stats[1]['saleCourses'])) {
                //             $code = $stats[1]['saleCode'];
                //         } else {
                //             unset($stats[1]['saleCode']);
                //             unset($stats[1]['saleEnd']);
                //             unset($stats[1]['saleCourses']);
                //             $user->statistics = json_encode($stats);
                //             $user->update();
                //         }
                //     }
                //     $code = Code::find()->where(['code'=>$code])->asArray()->limit(1)->one();
                    
                //     if ($code && $code['end_at'] > time()) {
                //         $props = CodeType::getPropsArr();
                //         $props = $props[$code['type']];
                //         if ($props['sale_cost'] > 0) {
                //             $model['cost'] -= $props['sale_cost'];
                //             if ($model['cost'] < 0)
                //                 $model['cost'] = 0;
                //         }

                //         if ($model['cost'] > 0 && $props['sale_percent'] > 0)
                //             $model['cost'] -= (int)($model['cost'] / 100 * $props['sale_percent']);
                //     }
                // } else {    
                //     $isStudent = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$model['id']])->asArray()->limit(1)->one();
                //     if ($isStudent && ($isStudent['end_at']+(3*24*3600)) > time())
                //         $model['cost'] -= (int)($model['cost'] / 100 * $this->endCourseSale);
                // }
            } // end isPrem
        } // end isGuest

        $short = Yii::$app->params['shortName'];
        $this->view->title = "$model[title] | Онлайн-курс / $short";
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => substr(strip_tags($model['desc']), 0, 300),
        ]);
        $keys = str_replace($model['title'],'',[',','.','!','?','$','"','(',')','-','+','_','=','/','|']);
        $keys = str_replace($keys,', ',' ');
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => "курс, онлайн-курс, $keys, ".Yii::$app->params['commonKeyWords'],
        ]);

        return $this->render('details', [
            'model' => $model,
            'isPrem' => $isPrem,
            'isCreator' => (($model['author_id'] > 0 && $model['author_id'] == $myId) || Yii::$app->user->can('admin')) ? true : false,
            'access' => $access
        ]);
    }

    public function getSale($user, $course_id, $cost) {
        $invite = $user->invite_code;
        $stats = json_decode($user->statistics, true);
        if ($cost >= 1500 && !empty($invite) && ($invite != 'SPENT' || !empty($stats[1]['saleCode']))) {
            $code = $invite;
            if (!empty($stats[1]['saleCode'])) {
                if ($stats[1]['saleEnd'] > time() && !in_array($course['id'], $stats[1]['saleCourses'])) {
                    $code = $stats[1]['saleCode'];
                } else {
                    unset($stats[1]['saleCode']);
                    unset($stats[1]['saleEnd']);
                    unset($stats[1]['saleCourses']);
                    $user->statistics = json_encode($stats);
                    $user->update();
                }
            }
            $code = Code::find()->where(['code'=>$code])->asArray()->limit(1)->one();
            
            if ($code && $code['end_at'] > time()) {
                $props = CodeType::getPropsArr();
                $props = $props[$code['type']];
                if ($props['sale_cost'] > 0) {
                    $cost -= $props['sale_cost'];
                    if ($cost < 0)
                        $cost = 0;
                }

                if ($cost > 0 && $props['sale_percent'] > 0)
                    $cost -= (int)($cost / 100 * $props['sale_percent']);
            }
        } else {    
            $isStudent = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$course_id])->asArray()->limit(1)->one();
            if ($isStudent && ($isStudent['end_at']+(3*24*3600)) > time())
                $cost -= (int)($cost / 100 * $this->endCourseSale);
        }

        return $cost;
    }

    public function actionModule($id = 0, $lesson = 0)
    {
        if ($id <= 0)
            throw new HttpException(404);

        $model = Module::find()->where(['id'=>$id])
            ->with([
                'course' => function ($query) {
                    $query->select(['id','author_id','title','free','cost'])->where(['publish'=>1]);
                },
                'lessons' => function ($query) {
                    $query->select(['id','module_id','free'])->where(['publish'=>1])->orderBy('place');
                },
            ])->asArray()->limit(1)->one();

        if (!$model)
            throw new HttpException(404);
        
        // Проверка доступа к курсу и модулю
        $isPrem = false;
        $isCreator = false;
        $isGuest = Yii::$app->user->isGuest;
        if (!$isGuest) {
            $user = Yii::$app->user->identity;
            $access = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$model['course_id']])->asArray()->limit(1)->one();
            $isPrem = (isset($access) && $access['end_at'] > time());
            $isCreator = (($model['course']['author_id'] > 0 && $model['course']['author_id'] == $user->id) || Yii::$app->user->can('admin'));
            $access = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$id])->asArray()->limit(1)->one();
            $isPrem = (isset($access) && $access['end_at'] > time());
            $model['course']['old_cost'] = $model['course']['cost'];

            if (!$isPrem)
                $model['course']['cost'] = $this->getSale($user, $model['course']['id'], $model['course']['cost']);
        } // end isGuest

        if (!$isGuest) {
            $stats = json_decode($user->statistics, true);
            $stats = $stats[Yii::$app->params['subInx']]['courses'][$model['course']['id']]['modules'];
        }

        $getLess = ($lesson > 0);
        foreach ((array)$model['lessons'] as $less) {
            if ($lesson == 0 && $isPrem && !$stats[$model['id']]['lessons'][$less['id']]['end']) {
                $lesson = $less;
            } else if ($lesson == $less['id']) {
                $lesson = $less;
                break;
            }
        }

        if ($lesson == 0)
            $lesson = $model['lessons'][0];

        if (!$isPrem && !$isCreator && !$model['free'] && !$model['course']['free'] && !$lesson['free'])
            throw new HttpException(404);

        // Проверка завершения предыдущего модуля
        if (!$isGuest) { // && !$model['free'] && !$model['course']['free'] && !$hasFreeLesson
            $prevEnd = 1;
            $listModules = Module::find()->select(['id','course_id','place'])
                ->where(['course_id'=>$model['course_id'], 'publish'=>1])
                ->orderBy('place')->asArray()->all();

            foreach ($listModules as $module) {
                if ($module['id'] == $model['id'])
                    break;
                $prevEnd = (!empty($stats)) ? $stats[$module['id']]['end'] : 0;
            }

            if (!$prevEnd && !$isCreator)
                throw new HttpException(404);
        }

        $short = Yii::$app->params['shortName'];
        $title = ($getLess) ? $lesson['title'] : $model['title'];
        $this->view->title = ($getLess)
            ? "$title | Урок / $short"
            : "$title | Модуль / $short";
        $desc = ($getLess) ? $lesson['desc'] : $model['desc'];
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => substr(strip_tags($desc), 0, 300),
        ]);
        $keys = str_replace($title,'',[',','.','!','?','$','"','(',')','-','+','_','=','/','|']);
        $keys = str_replace($keys,', ',' ');
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => "$keys, курс, онлайн-курс, модуль, ".Yii::$app->params['commonKeyWords'],
        ]);

        return $this->render('module', [
            'model' => $model,
            'lesson' => Lesson::find()->where(['id'=>$lesson['id']])
                ->with([
                    'test' => function ($query) { $query->select(['id'])->where(['publish'=>1]); },
                    'test.questions' => function ($query) { $query->select(['id', 'examtest_id']); },
                    'write' => function ($query) { $query->select(['id'])->where(['publish'=>1]); },
                    'write.answers' => function ($query) use ($myId) {
                        $query->select(['id','examwrite_id','user_id','check'])->where(['user_id'=>$myId])->limit(1);
                    },
                ])->asArray()->limit(1)->one(),
            'isPrem' => $isPrem,
            'isCreator' => $isCreator,
            'access' => $access,
            'stats' => $stats[$model['id']],
        ]);
    }

    public function actionLesson()
    {
        $id = $_POST['id'];
        $myId = Yii::$app->user->identity->id;
        if ($id <= 0)
            return $this->renderPartial('_lesson', [ 'model' => null ]);

        $model = Lesson::find()->where(['id'=>$id, 'publish'=>1])
            ->with([
                'module' => function ($query) { $query->select(['id','course_id','free'])->where(['publish'=>1]); },
                'module.course' => function ($query) { $query->select(['id','free','cost'])->where(['publish'=>1]); },
                'test' => function ($query) { $query->select(['id','lesson_id'])->where(['publish'=>1]); },
                'test.questions' => function ($query) { $query->select(['id', 'examtest_id']); },
                'write' => function ($query) { $query->select(['id', 'lesson_id'])->where(['publish'=>1]); },
                'write.answers' => function ($query) use ($myId) {
                    $query->select(['id','examwrite_id','user_id','check'])
                        ->where(['user_id'=>$myId])->orderBy(['id'=>SORT_DESC])->limit(1);
                },
            ])->asArray()->limit(1)->one();

        if (!$model)
            return $this->renderPartial('_lesson', [ 'model' => null ]);

        // Проверка доступа к курсу и уроку
        $isPrem = false;
        $isCreator = false;
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $access = Student::find()->where(['learner_id'=>$myId, 'course_id'=>$model['module']['course_id']])->asArray()->limit(1)->one();
            $isPrem = (isset($access) && $access['end_at'] > time());
            $isCreator = (($model['module']['course']['author_id'] > 0 && $model['module']['course']['author_id'] == $myId) || Yii::$app->user->can('admin'));
            $model['module']['course']['old_cost'] = $model['module']['course']['cost'];

            if (!$isPrem)
                $model['module']['course']['cost'] = $this->getSale($user, $model['module']['course']['id'], $model['module']['course']['cost']);
        } // end isGuest

        if (!$model['free'] && !$model['module']['free'] && !$model['module']['course']['free'] && !$isPrem && !$isCreator)
            return $this->renderPartial('_lesson', [ 'model' => null ]);

        // Проверка завершения предыдущего модуля
        if (!Yii::$app->user->isGuest) {
            $stats = json_decode($user->statistics, true);
            $stats = $stats[Yii::$app->params['subInx']]['courses'][$model['module']['course_id']]['modules'][$model['module']['id']]['lessons'];
        }

        $listLessons = Lesson::find()->select(['id','module_id','place','free'])
            ->where(['module_id'=>$model['module_id']])
            ->orderBy('place')->asArray()->all();

        $prevEnd = 1;
        $nextAcc = 0;
        foreach ($listLessons as $lKey => $lesson) {
            if ($lesson['id'] == $model['id']) {
                $nextAcc = ($model['module']['course']['free'] || $model['module']['free']
                    || (!empty($listLessons[$lKey+1]) && $listLessons[$lKey+1]['free'])) ? 1 : 0;
                break;
            }
            
            if (!empty($stats))
                $prevEnd = $stats[$lesson['id']]['end'];
        }

        if (!$prevEnd && !$isCreator)
            return $this->renderPartial('_lesson', [ 'model' => null ]);

        $isPrem = (isset($access) && $access['end_at'] > time());
        return $this->renderPartial('_lesson', [
            'model' => $model,
            'isPrem' => $isPrem,
            'access' => $access,
            'stats' => $stats[$model['id']],
            'nextAcc' => ($isPrem || $nextAcc),
        ]);
    }

    public function actionGetTestWrite()
    {
        $id = $_POST['id'];
        $type = $_POST['type'];
        $myId = Yii::$app->user->identity->id;
        if ($id <= 0 || empty($type))
            return 0;

        if ($type == 'test')
            $model = Test::find()->where(['id'=>$id, 'publish'=>1])
                ->with([
                    'questions', 'questions.answers',
                    'lesson' => function ($query) { $query->select(['id', 'module_id', 'free']); },
                    'lesson.module' => function ($query) { $query->select(['id', 'course_id', 'free']); },
                    'lesson.module.course' => function ($query) { $query->select(['id', 'subject_id', 'author_id', 'free']); },
                ])->asArray()->limit(1)->one();
        else
            $model = Write::find()->where(['id'=>$id, 'publish'=>1])
                ->with([
                    'answers' => function ($query) use ($myId) {
                        $query->select(['id','examwrite_id','user_id','check'])->where(['user_id' => $myId, 'check' => 0])->limit(1);
                    },
                    'lesson' => function ($query) { $query->select(['id', 'module_id', 'free']); },
                    'lesson.module' => function ($query) { $query->select(['id', 'course_id', 'free']); },
                    'lesson.module.course' => function ($query) { $query->select(['id', 'subject_id', 'author_id', 'free']); },
                ])->asArray()->limit(1)->one();

        if (!$model)
            return 0;

        if ($type == 'write' && $model['answers'][0]['id'] > 0)
            return 2;

        $access = Student::find()->where([
            'learner_id'=>$myId,
            'course_id'=>$model['lesson']['module']['course_id']
        ])->asArray()->limit(1)->one();

        return $this->renderPartial('_testing', [
            'model' => $model,
            'type' => $type,
            'isStudent' => ((isset($access) && $access['end_at'] > time()) || $model['free'] || $model['module']['free'] || $model['module']['course']['free']),
            'isAuthor' => ($model['module']['course']['author_id'] == Yii::$app->user->id || Yii::$app->user->can('admin')),
            'access' => $access,
            // 'stats' => $stats[Yii::$app->params['subInx']]['courses'][$model['module']['course_id']]['modules'][$model['module']['id']]['lessons'][$model['id']]
        ]);
    }

    public function actionCheckExercise()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // Получаем ответы
        $type = $_POST['type'];
        $pTest = $_POST['Test'];
        $pWrite = $_POST['Write'];
        $myId = Yii::$app->user->identity->id;

        // Проверяем важные данные
        if ( empty($pTest) && empty($pWrite) )
            return ['req'=>0];

        if ($type == 'test')
            $model = Test::find()
                ->select(['id','lesson_id','qst_exp','correct_answers'])
                ->where(['id'=>$pTest['id'], 'publish' => 1])
                ->with([
                    'lesson' => function ($query) {$query->select(['id','module_id']);},
                    'lesson.module' => function ($query) {$query->select(['id','course_id']);},
                ])
                ->asArray()->limit(1)->one();
        else if ($type == 'write')
            $model = Write::find()
                ->select(['id','lesson_id'])
                ->where(['id'=>$pWrite['id'], 'publish' => 1])
                ->with([
                    'answers' => function ($query) {
                        $query->select(['id','examwrite_id','user_id','check'])->where(['user_id'=>$myId, 'check'=>0])->limit(1);
                    },
                    'lesson' => function ($query) {$query->select(['id','module_id']);},
                    'lesson.module' => function ($query) {$query->select(['id','course_id']);},
                ])
                ->asArray()->limit(1)->one();

        if (!$model || ($type == 'write' && $model['answers'][0]['id'] > 0))
            return ['req'=>0];

        // Если пользователь авторизирован, получаем его модель
        $isGuest = Yii::$app->user->isGuest;
        $user = (!$isGuest) ? Yii::$app->user->identity : null;
        $isPrem = ($user)
            ? Student::find()->where([
                'learner_id' => $user->id,
                'course_id' => $model['lesson']['module']['course_id']
            ])->limit(1)->exists() : false;

        
        $exp = 0;
        $points = 0;
        $percent = 0;
        // устанавливаем флаг ошибки, id задания и свичуем тип задания
        switch ($type) {
            case 'test': // если Тест
                // декодируем json
                $userAnswers = json_decode($pTest['answers'], true);
                $rightAnswers = json_decode($model['correct_answers'], true);

                // Перебираем массив с правильными ответами
                $err = false;
                // $qst_points = (int)($model['fullexam_points']/count($rightAnswers));
                foreach ((array)$rightAnswers as $qstId => $ansArr) {
                    $local_err = true;
                    if ($userAnswers[$qstId]) { // если пользователь ответил на вопрос
                        $diff_u = array_diff($userAnswers[$qstId], $ansArr['answers']); // вычитаем массивы
                        $diff_t = array_diff($ansArr['answers'], $userAnswers[$qstId]); // вычитаем массивы
                        if (!$diff_u && !$diff_t) { // если останется хоть один элемент в массивах, тогда ученик допустил ошибку
                            $local_err = false;
                            $exp += ($ansArr['hard']) ? $model['qst_exp']*2 : $model['qst_exp'];
                            ++$points;
                        }
                    }

                    // if ($ansArr['themes'])
                    //     $stats['themes'][($local_err)?'err':'corr'] = array_merge($stats['themes'][($local_err)?'err':'corr'], $ansArr['themes']);

                    ++$percent;
                    if ($local_err)
                        $err = true;
                }
                if ($percent != 0)
                    $percent = ($points/$percent) * 100;
                // $task_stat['corr'] = (!$rightAnswers || $err)?0:1;
                break;

            case 'write':
                if ($isGuest || !$isPrem)
                    return ['req'=>0];

                $files = $_FILES['Write'];
                $filesSort = [
                    'names' => $files['name']['answer_files'],
                    'types' => $files['type']['answer_files'],
                    'tmp_names' => $files['tmp_name']['answer_files'],
                    'errors' => $files['error']['answer_files'],
                    'sizes' => $files['size']['answer_files'],
                ];

                $ans = strip_tags($pWrite['answer']);
                if (iconv_strlen($ans) > Reply::COUNT_CHARS)
                    $pWrite['answer'] = mb_strimwidth($ans, 0, Reply::COUNT_CHAR-1);
                
                $reply = new Reply;
                $reply->user_id = Yii::$app->user->id;
                $reply->examwrite_id = $pWrite['id'];
                $reply->text = $ans;
                if ($filesSort['names'][0] != '') {
                    for ($i=0; $i < count($filesSort['names']); $i++) { 
                        if ($filesSort['sizes'][$i] > Reply::FILE_SIZE)
                            return ['req'=>0];

                        $isType = false;
                        foreach (Reply::FILE_TYPES as $type) {
                            $offset = strlen($filesSort['names'][$i])-strlen($type)-2;
                            if (stripos($filesSort['names'][$i], ".$type", $offset) >= 0)
                                $isType = true;
                        }
                        
                        if (!$isType)
                            return ['req'=>0];

                        $reply->answerFiles[] = [
                            'name' => $filesSort['names'][$i],
                            'type' => $filesSort['types'][$i],
                            'tmp_name' => $filesSort['tmp_names'][$i],
                            'error' => $filesSort['errors'][$i],
                            'size' => $filesSort['sizes'][$i],
                        ];
                    }
                }
                
                if (!empty($reply->answerFiles))
                    $reply->createZip();

                // return $reply;
                $reply->save();

                break;
        }

        // Если тип не письменный, т.к. он проверяется учителем и пользователь авторизирован для записи в статистику
        if (!$isGuest) {
            $stats = json_decode($user->statistics, true);
            $statLesson = (array)$stats[Yii::$app->params['subInx']]['courses'][$model['lesson']['module']['course_id']]['modules'][$model['lesson']['module_id']]['lessons'][$model['lesson']['id']];
            $statLesson['end'] = ($statLesson['end'] || ($type == 'test' && $percent >= 75)) ? 1 : 0; // ПРОВЕРЯЕМ ПРОЦЕНТ

            if ($type == 'test') {
                // добавляем опыт
                if (isset($statLesson['test']['exp'])) {
                    if ($statLesson['test']['exp'] > $exp)
                        $exp = $statLesson['test']['exp'];
                    else
                        $user->addExp($exp - $statLesson['test']['exp']);
                } else
                    $user->addExp($exp);
                    
                $statLesson['test'] = [
                    'completed' => 1,
                    'exp' => $exp,
                    'points' => ($statLesson['test']['points'] > $points) ? $statLesson['test']['points'] : $points,
                ];
            } else {
                $statLesson['write'] = [
                    'completed' => 1,
                    'exp' => 0,
                    'right' => 0,
                ];
            }
            
            $stats[Yii::$app->params['subInx']]['courses'][$model['lesson']['module']['course_id']]['modules'][$model['lesson']['module_id']]['lessons'][$model['lesson']['id']] = $statLesson;
            $user->statistics = json_encode($stats);
            // сохраняем модель
            $user->update();
        }

        return ['req'=>1, 'percent'=>$percent, 'points'=>$points, 'exp' => $exp];
    }

    // для тестов
    public function actionSubscribe()
    {
        $id = $_GET['id'];
        $user = Yii::$app->user->identity;
        if (empty($id) || $id < 1 || Yii::$app->user->isGuest)
            return 0;

        $now = time();
        $model = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$id])->limit(1)->one();
        if (!$model) {
            $model = new Student;
            $model->learner_id = $user->id;
            $model->course_id = $id;
            $model->start_at = $now;
            $model->end_at = $now+(30*24*60*60);
            $model->save();

            $stats = json_decode($user->statistics, true);
            if (empty($stats[Yii::$app->params['subInx']]['courses'][$id])) {
                $stats[Yii::$app->params['subInx']]['courses'][$id] = [
                    'end' => 0,
                    'modules' => [],
                ];
                $user->statistics = json_encode($stats);
                $user->save();
            }
        } else if ($model->end_at < $now) {
            $model->start_at = $now;
            $model->end_at = $now+(30*24*60*60);
            $model->update();
        }
        return 1;
    }

    // $stats - статистика проверяемого курса
    public function checkAccess($stats) {

    }
}
