<?php

namespace app\controllers;

use Yii;
use app\models\{User, Theme};
use app\models\course\{Lesson, Student};
use app\models\webinar\Webinar;
use app\models\exam\{Fullexam, Section, Exercise, Result};
use app\models\exam\test\{Test, Question, Answer};
use app\models\exam\write\{Write, Reply};
use app\models\exam\correlate\{Correlate, Pair};
use app\models\exam\addition\Addition;
use yii\db\Expression;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use app\components\ExamType;
// use yii\filters\VerbFilter;

class ExamsController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index'             => ['GET'],
                    'delete-homework'   => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['delete-homework'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['delete-homework'],
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->request->post('appKey') === Yii::$app->params['secretKey']);
                        },
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['delete-homework'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    // YANDEX TRANSLATE
    public function actionTranslate()
    {
        // Официальная документация - https://tech.yandex.ru/translate/
        // Настройки:
        // Ключ, получить тут: https://tech.yandex.ru/keys/get/?service=trnsl
        $yt_api_key = "trnsl.1.1.20190509T170340Z.a8547da29d09b7b4.78c87134dd69791ca2087b7d70de0b21c35441d2";
        $yt_lang = "en-ru"; // Перевод с английского на русский
        $yt_text = Yii::$app->request->post('text');

        $yt_link = "https://translate.yandex.net/api/v1.5/tr.json/translate?key=".$yt_api_key."&text=".$yt_text."&lang=".$yt_lang;

        // получаем данные в JSON: {"code":200,"lang":"en-ru","text":["какой-то текст"]}
        $result = file_get_contents($yt_link);
        $result = json_decode($result, true); // Преобразуем в массив
        return $result['text'][0];
    }

    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        $accesses = [];

        // if (!$_COOKIE['work']) {
        //     throw new HttpException(403, 'Для использования функций сайта, вам необходимо активировать работу сookie.');
        // }

        $model = Fullexam::find()->where(['and',
            ['publish' => 1],
            ['in','subject_id', [ 1, Yii::$app->params['subInx'] ]]
        ])->with([
            'sections' => function ($query) {
                $query->where(['publish'=>1]);
            },
            'sections.exercises' => function ($query) {
                $query->where(['publish'=>1]);
            }
            ])->asArray()->all();

        if ($isGuest) {
            if ($_COOKIE['work']) {
                if (!$_COOKIE['fullexam_list']) {
                    $defList = [];
                    foreach ($model as $fl)
                        $defList[$fl['id']] = ['last_date'=>time(), 'number_attempts'=>0];
                    setcookie("fullexam_list", json_encode($defList), time()+365*24*3600, "/");
                    $attemptsList = $defList;
                } else 
                    $attemptsList = json_decode($_COOKIE['fullexam_list'], true);
            } else
                throw new HttpException(403, 'Для использования функций сайта, вам необходимо активировать работу сookie.');
        } else {
            $user = Yii::$app->user->identity;
            $fullStats = json_decode($user->statistics, true);
            $attemptsList = $fullStats[Yii::$app->params['subInx']]['exams']['list'];
            if ($model != []) {
                $courses_id = [];
                foreach ($model as $fl)
                    $courses_id[] = $fl['course_id'];
                $accesses = Student::find()->where(['and',
                    ['learner_id' => $user->id],
                    ['in','course_id', $courses_id]
                ])->asArray()->all();
            }
        }

        $short = Yii::$app->params['shortName'];
        $lable = Yii::$app->params['listSubs'][Yii::$app->params['subInx']]['lable'];
        $this->view->title = "Онлайн-тесты по подготовке к экзаменам ОГЭ и ЕГЭ ".date('Y')." | ".strtoupper($lable)." / $short";
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => "Множество вариантов пробных онлайн-тестов для подготовки к выпускным экзаменам. Решение экзаменационных тестов онлайн."
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => 'тесты, онлайн-тесты, подготовка, экзамены, ОГЭ, ЕГЭ, '.$lable.', '.Yii::$app->params['commonKeyWords'],
        ]);

        return $this->render('index', [
            'model' => $model,
            'themes' => Theme::find()->asArray()->all(),
            'attemptsList' => $attemptsList,
            'accesses' => $accesses,
        ]);
    }

    public function actionDetails($id, $exercise = 0, $result = 0)
    {
        if ($id <= 0)
            throw new NotFoundHttpException('Страница не найдена!');

        $this->layout = false;
        $user = Yii::$app->user->identity;
        $isGuest = Yii::$app->user->isGuest;
        $isPrem = false;
        $isResult = ($result > 0);
        $sub = Yii::$app->params['subInx'];

        if ($exercise > 0 && !$isResult) {
            $model = Exercise::find()->where(['id'=>$exercise, 'publish'=>1])
                ->with([
                    'section' => function ($query) {
                        $query->select(['id','fullexam_id','name'])->where(['publish'=>1]);
                    },
                    'section.fullexam' => function ($query) {
                        $query->select(['id','subject_id','course_id','name'])->where(['publish'=>1]);
                    }
                ])->asArray()->limit(1)->one();

            if (!$model || $model['section'] == null || $model['section']['fullexam'] == null)
                throw new NotFoundHttpException('Страница не найдена!');

            if (!$isGuest) {
                $isPrem = Student::find()->where([
                    'learner_id' => $user->id,
                    'course_id' => $model['section']['fullexam']['course_id']
                ])->asArray()->limit(1)->one();
                $isPrem = ($isPrem && $isPrem['end_at'] > time());
                $stats = json_decode(Yii::$app->user->identity->statistics, true);
                $stats = $stats[$sub]['exams'][$model['section']['fullexam']['id']][$model['section']['id']][$model['id']];
                if (!$stats)
                    $stats = User::DEF_STAT_EXE;
            }

            switch ($model['type']) {
                case ExamType::TEST:
                    if (!$isGuest)
                        $model['tests'] = Test::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->andWhere(['not in', 'id', $stats['completed_list']])
                            ->with(['questions', 'questions.answers'])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    if (!$model['tests'])
                        $model['tests'] = Test::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->with(['questions', 'questions.answers'])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    $task_id = $model['tests']['id'];
                    break;
                case ExamType::WRITE:
                    if (!$isGuest)
                        $model['writes'] = Write::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->andWhere(['not in', 'id', $stats['completed_list']])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    if (!$model['writes'])
                        $model['writes'] = Write::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    $task_id = $model['writes']['id'];
                    break;
                case ExamType::CORRELATE:
                    if (!$isGuest)
                        $model['correlates'] = Correlate::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->andWhere(['not in', 'id', $stats['completed_list']])
                            ->with(['pairs'])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    if (!$model['correlates'])
                        $model['correlates'] = Correlate::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->with(['pairs'])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    $task_id = $model['correlates']['id'];
                    break;
                case ExamType::ADDITION:
                    if (!$isGuest)
                        $model['additions'] = Addition::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->andWhere(['not in', 'id', $stats['completed_list']])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    if (!$model['additions'])
                        $model['additions'] = Addition::find()
                            ->where(['exercise_id'=>$model['id'],'publish'=>1])
                            ->orderBy(new Expression('rand()'))->asArray()->limit(1)->one();

                    $task_id = $model['additions']['id'];
                    break;
            }

            $type = 'exercise';
        } else {
            if ($isResult && !$isGuest) {
                $resultModel = Result::find()->where(['id'=>$result, 'user_id'=>$user->id])->asArray()->limit(1)->one();
                if (!$resultModel)
                    throw new HttpException(404, 'Результаты не найдены!');

                $resultModel['answers'] = json_decode($resultModel['answers'], true);
                $tasks = [
                    'tests' => [],
                    'correlates' => [],
                    'additions' => [],
                    'writes' => [],
                ];

                foreach ($resultModel['answers']['sections'] as $sec_id => $sec) {
                    foreach ($sec['exercises'] as $exe_id => $exe) {
                        switch ($exe['type']) {
                            case ExamType::TEST:
                                $tasks['tests'][] = $exe['test']['id'];
                                break;
                            case ExamType::CORRELATE:
                                $tasks['correlates'][] = $exe['correlate']['id'];
                                break;
                            case ExamType::ADDITION:
                                $tasks['additions'][] = $exe['addition']['id'];
                                break;
                            case ExamType::WRITE:
                                $tasks['writes'][] = $exe['write']['id'];
                                break;
                        }
                    }
                }
                
                $model = Fullexam::find()->where(['id'=>$id, 'publish'=>1])
                    ->with([
                        'sections' => function ($query) {
                            $query->where(['publish'=>1])
                                ->orderBy('place');
                        },
                        'sections.exercises' => function ($query) use ($tasks) {
                            $query->where(['publish'=>1, 'fullexam'=>1])->with([
                                'tests' => function ($query) use ($tasks) {
                                    $query->where(['publish'=>1])
                                        ->andWhere(['in', 'id', $tasks['tests']])    
                                        ->with(['questions', 'questions.answers']);
                                },
                                'writes' => function ($query) use ($tasks) {
                                    $query->where(['publish'=>1])
                                        ->andWhere(['in', 'id', $tasks['writes']]);
                                },
                                'correlates' => function ($query) use ($tasks) {
                                    $query->where(['publish'=>1])
                                        ->andWhere(['in', 'id', $tasks['correlates']])    
                                        ->with(['pairs']);
                                },
                                'additions' => function ($query) use ($tasks) {
                                    $query->where(['publish'=>1])
                                        ->andWhere(['in', 'id', $tasks['additions']]);
                                },
                            ])->orderBy('place');
                        },
                    ])->asArray()->limit(1)->one();

                if (!$model)
                    throw new HttpException(404);

                $isPrem = Student::find()->where(['learner_id' => $user->id, 'course_id' => $model['course_id']])->asArray()->limit(1)->one();
                $isPrem = ($isPrem && $isPrem['end_at'] > time());
            } else {
                $model = Fullexam::find()->where(['id'=>$id, 'publish'=>1])
                    ->with([
                        'sections' => function ($query) {
                            $query->where(['publish'=>1])
                                ->orderBy('place');
                        },
                        'sections.exercises' => function ($query) {
                            $query->where(['publish'=>1, 'fullexam'=>1])->with([
                                'tests' => function ($query) {
                                    $query->where(['publish'=>1])
                                        ->with(['questions', 'questions.answers'])
                                        ->orderBy(new Expression('rand()'));
                                },
                                'writes' => function ($query) {
                                    $query->where(['publish'=>1])
                                        ->orderBy(new Expression('rand()'));
                                },
                                'correlates' => function ($query) {
                                    $query->where(['publish'=>1])
                                        ->with(['pairs'])
                                        ->orderBy(new Expression('rand()'));
                                },
                                'additions' => function ($query) {
                                    $query->where(['publish'=>1])
                                        ->orderBy(new Expression('rand()'));
                                },
                            ])->orderBy('place');
                        },
                    ])->asArray()->limit(1)->one();
                if (!$model)
                    throw new HttpException(404);

                if ($isGuest) {
                    if ($_COOKIE['work']) {
                        $def = [$model['id'] => ['last_date'=>0, 'number_attempts'=>0]];
                        if (!$_COOKIE['fullexam_list']) {
                            setcookie("fullexam_list", json_encode($def), time()+365*24*3600, "/");
                            $attempt = $def;
                        } else {
                            $fullStats = json_decode($_COOKIE['fullexam_list'], true);
                            if ((array)$fullStats[$model['id']] == [])
                                $fullStats[$model['id']] = $def[$model['id']];
                            $attempt = $fullStats[$model['id']];
                        }
                    } else
                        throw new HttpException(403, 'Для использования функций сайта, вам необходимо активировать работу сookie.');
                } else {
                    $isPrem = Student::find()->where(['learner_id' => $user->id, 'course_id' => $model['course_id']])->asArray()->limit(1)->one();
                    $isPrem = ($isPrem && $isPrem['end_at'] > time());
                    // DEBUG: Сделать получение статы из куки для не премиального
                    $fullStats = json_decode($user->statistics, true);
                    $attempt = $fullStats[$sub]['exams']['list'][$model['id']];
                }
    
                if ((array)$attempt != [] && $attempt['last_date'] && (int)($attempt['last_date']/(24*3600)) == (int)(time()/(24*3600))) {
                    if ( (!$isPrem && $attempt['number_attempts'] < Fullexam::MAX_TRY_FREE)
                        || ($isPrem && $attempt['number_attempts'] < Fullexam::MAX_TRY_PREM) )
                        $attempt['number_attempts'] += ($isPrem)?0:1;
                    else
                        throw new HttpException(403, 'Вы превысили допустимое количество попыток на сегодня, сделайте перерыв, отдохните и придиходите завтра с новыми силами!');
                } else {
                    $attempt['last_date'] = time();
                    $attempt['number_attempts'] = ($isPrem)?0:1;
                }
    
                if ($isGuest) {
                    $fullStats[$model['id']] = $attempt;
                    setcookie("fullexam_list", json_encode($fullStats), time()+365*24*3600, "/");
                } else {
                    $fullStats[$sub]['exams']['list'][$model['id']] = $attempt;
                    $user->statistics = json_encode($fullStats);
                    $user->save();
                }
            }
            $type = 'fullexam';
        }

        return $this->render('details', [
            'model' => $model,
            'type' => $type, // DEBUG: ДОБАВИТЬ ОШИБКИ ВО VIEW
            'task_id' => $task_id,
            'isPrem' => $isPrem,
            'isResult' => ($result > 0 && $resultModel)?true:false,
            'resultModel' => $resultModel
        ]);
    }

    public function actionCheckFullexam()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // Получаем ответы
        $fullexam_post = $_POST['Fullexam'];
        // return $this->debug($fullexam_post);

        if ( empty($fullexam_post) )
            return ['req'=>0];

        $tasks = [
            'tests'         => [],
            'correlates'    => [],
            'additions'     => [],
            'writes'        => [],
        ];

        foreach ((array)$fullexam_post['sections'] as $sec_id => $sec) {
            foreach ((array)$sec['exercises'] as $exe_id => $exe) {
                switch ($exe['type']) {
                    case ExamType::TEST:
                        $tasks['tests'][] = $exe['test']['id'];
                        break;
                    case ExamType::WRITE:
                        $tasks['writes'][] = $exe['write']['id'];
                        break;
                    case ExamType::CORRELATE:
                        $tasks['correlates'][] = $exe['correlate']['id'];
                        break;
                    case ExamType::ADDITION:
                        $tasks['additions'][] = $exe['addition']['id'];
                        break;
                }
            }
        }
        
        $fullexam_db = Fullexam::find()->where(['id'=>$fullexam_post['id'], 'publish'=>1])
            ->with([
                'sections' => function ($query) {
                    $query->where(['publish'=>1]);
                },
                'sections.exercises' => function ($query) use ($tasks) {
                    $query->where(['publish'=>1, 'fullexam'=>1])
                        ->with([
                            'tests' => function ($query) use ($tasks) {
                                $query->where(['publish'=>1])
                                    ->andWhere(['in', 'id', $tasks['tests']])    
                                    ->with(['questions']);
                            },
                            'writes' => function ($query) use ($tasks) {
                                $query->where(['publish'=>1])
                                    ->andWhere(['in', 'id', $tasks['writes']]);
                            },
                            'correlates' => function ($query) use ($tasks) {
                                $query->where(['publish'=>1])
                                    ->andWhere(['in', 'id', $tasks['correlates']])    
                                    ->with(['pairs']);
                            },
                            'additions' => function ($query) use ($tasks) {
                                $query->where(['publish'=>1])
                                    ->andWhere(['in', 'id', $tasks['additions']]);
                            },
                        ]);
                },
            ])->asArray()->limit(1)->one();

        if (!$fullexam_db)
            return ['req'=>0];

        $isGuest = Yii::$app->user->isGuest;
        $isPrem = false;

        if (!$isGuest) {
            // Если пользователь авторизирован, получаем его модель
            $user = Yii::$app->user->identity;
            $isPrem = Student::find()->where([
                'learner_id' => $user->id,
                'course_id' => $fullexam_db['course_id']
            ])->asArray()->limit(1)->one();
            $isPrem = ($isPrem && $isPrem['end_at'] > time());

            $fullStats = json_decode($user->statistics, true);
            $exam = $fullStats[Yii::$app->params['subInx']]['exams']['list'][$fullexam_db['id']];

            if ((array)$exam != [] && $exam['last_date'] && (int)($exam['last_date']/(24*3600)) == (int)(time()/(24*3600))) {
                if ( (!$isPrem && $exam['number_attempts'] <= Fullexam::MAX_TRY_FREE)
                    || ($isPrem && $exam['number_attempts'] < Fullexam::MAX_TRY_PREM) )
                    $exam['number_attempts'] += ($isPrem)?1:0;
                else 
                    return ['req'=>0];
            } else {
                $exam['last_date'] = time();
                $exam['number_attempts'] = 1;
            }

            $fullStats[Yii::$app->params['subInx']]['exams']['list'][$fullexam_db['id']] = $exam;
        }

        $stats = Exercise::STATS_EXAM_ARR;
        $stats['id'] = $fullexam_db['id'];
        foreach ($fullexam_db['sections'] as $sect) {
            foreach ($sect['exercises'] as $exe) {
                // устанавливаем флаг ошибки, id задания и свичуем тип задания
                $exe_post = $fullexam_post['sections'][$sect['id']]['exercises'][$exe['id']];
                if (!$exe_post)
                    return ['req'=>0];

                $task_stat = Exercise::STAT_EXAM_TASK;
                switch ($exe_post['type']) {
                    case ExamType::TEST: // если Тест
                        // получаем тест
                        $test = $exe['tests'][0];
                        if ($test) {
                            $task_stat['task'] = $test['id'];

                            // декодируем json
                            $userAnswers = json_decode($exe_post['test']['answers'], true);
                            $rightAnswers = json_decode($test['correct_answers'], true);

                            // Перебираем массив с правильными ответами
                            $err = false;
                            $qst_points = (int)($exe['fullexam_points']/count($rightAnswers));
                            $countRight = 0;
                            foreach ($rightAnswers as $qstId => $ansArr) {
                                $local_err = true;
                                $stats['max_exp'] += $test['qst_exp'];
                                if ($userAnswers[$qstId]) { // если пользователь ответил на вопрос
                                    $diff_u = array_diff($userAnswers[$qstId], $ansArr['answers']); // вычитаем массивы
                                    $diff_t = array_diff($ansArr['answers'], $userAnswers[$qstId]); // вычитаем массивы
                                    if (!$diff_u && !$diff_t) { // если останется хоть один элемент в массивах, тогда ученик допустил ошибку
                                        $local_err = false;
                                        $exe_post['test']['exp'] += $test['qst_exp'];
                                        $exe_post['test']['points'] += $qst_points;
                                        ++$countRight;
                                    }
                                }

                                if ($ansArr['themes'])
                                    $stats['themes'][($local_err)?'err':'corr'] = array_merge($stats['themes'][($local_err)?'err':'corr'], $ansArr['themes']);

                                if ($local_err)
                                    $err = true;
                            }

                            if ($qst_points == 0 && count($rightAnswers) == $countRight)
                                $stats['points'] += $exe['fullexam_points'];
                            else
                                $stats['points'] += $exe_post['test']['points'];

                            $stats['exp'] += $exe_post['test']['exp'];
                            $task_stat['corr'] = (!$rightAnswers || $err)?0:1;
                        }
                        break;

                    case ExamType::CORRELATE:
                        // получаем тест
                        $correlate = $exe['correlates'][0];
                        if ($correlate) {
                            $task_stat['task'] = $correlate['id'];
                        
                            // декодируем json
                            $userAnswers = json_decode($exe_post['correlate']['answers'], true);
                            $correlate['themes'] = json_decode($correlate['themes'], true);
                            
                            // Перебираем массив с правильными ответами
                            $err = false;
                            $pair_points = (int)($exe['fullexam_points']/count($correlate['pairs']));
                            $countRight = 0;
                            foreach ($correlate['pairs'] as $i => $pair) {
                                $local_err = true;
                                $stats['max_exp'] += $correlate['pair_exp'];
                                if ($userAnswers[$pair['id']] && $pair['id'] == $userAnswers[$pair['id']]) { // если пользователь соотнёс верно
                                    $local_err = false;
                                    $exe_post['correlate']['exp'] += $correlate['pair_exp'];
                                    $exe_post['correlate']['points'] += $pair_points;
                                    ++$countRight;
                                }

                                if ($correlate['themes'][$pair['id']])
                                    $stats['themes'][($local_err)?'err':'corr'] = array_merge($stats['themes'][($local_err)?'err':'corr'], $correlate['themes'][$pair['id']]);

                                if ($local_err)
                                    $err = true;
                            }

                            if ($pair_points == 0 && count($rightAnswers) == $countRight)
                                $stats['points'] += $exe['fullexam_points'];
                            else
                                $stats['points'] += $exe_post['correlate']['points'];

                            $stats['exp'] += $exe_post['correlate']['exp'];
                            $task_stat['corr'] = (!$correlate['pairs'] || $err)?0:1;
                        }
                        break;

                    case ExamType::ADDITION:
                        // получаем тест
                        $addition = $exe['additions'][0];
                        if ($addition) {
                            $task_stat['task'] = $addition['id'];

                            // декодируем json
                            $userAnswers = $exe_post['addition']['answers'];
                            $rightAnswers = [];
                            $i = 0;
                            preg_replace_callback(
                                '/\_\(([\s\S]*?)\)/',
                                function ($matches) use (&$rightAnswers) {
                                    $arr = explode('/',$matches[1]);
                                    $answ = [ 'corr' => $arr[1], 'theme' => (!empty($arr[2])) ? [(int)$arr[2]] : [] ];
                                    if (!empty($arr[0]))
                                        $rightAnswers[$arr[0]] = $answ;
                                    else
                                        $rightAnswers[] = $answ;
                                },
                                $addition['text']
                            );

                            // Перебираем массив с правильными ответами
                            $err = false;
                            $word_points = (int)($exe['fullexam_points']/count($rightAnswers));
                            $countRight = 0;
                            foreach ($rightAnswers as $word => $ansArr) {
                                $local_err = true;

                                if (!empty($userAnswers[$word])) {
                                    $corr = false;
                                    if (strpos($ansArr['corr'], '_') !== false) {
                                        $ansList = explode('_', $ansArr['corr']);
                                        foreach ($ansList as $corrVal) {
                                            if (mb_strtolower(trim($userAnswers[$word])) == mb_strtolower(trim($corrVal))) {
                                                $corr = true;
                                                break;
                                            }
                                        }
                                    } else if (mb_strtolower(trim($userAnswers[$word])) == mb_strtolower(trim($ansArr['corr'])))
                                        $corr = true;
                
                                    // если пользователь правильно ответил на вопрос
                                    if ($corr) {
                                        $local_err = false;
                                        $exe_post['addition']['exp'] += $addition['word_exp'];
                                        $exe_post['addition']['points'] += $word_points;
                                        ++$countRight;
                                    }
                                }

                                $stats['max_exp'] += $addition['word_exp'];

                                if (!empty($ansArr['theme']))
                                    $stats['themes'][($local_err)?'err':'corr'] = array_merge($stats['themes'][($local_err)?'err':'corr'], $ansArr['theme']);

                                if ($local_err)
                                    $err = true;
                            }

                            if ($word_points == 0 && count($rightAnswers) == $countRight)
                                $stats['points'] += $exe['fullexam_points'];
                            else
                                $stats['points'] += $exe_post['addition']['points'];

                            $stats['exp'] += $exe_post['addition']['exp'];
                            $task_stat['corr'] = (!$rightAnswers || $err)?0:1;
                        }
                        break;

                    case ExamType::WRITE: 
                        if ($isPrem) {
                            // получаем практическое задание
                            $write = $exe['writes'][0];
                            $task_stat['skip'] = 1;
                            if ($write) {
                                $task_stat['task'] = $write['id'];
                                $stats['max_exp'] += $write['exp'];
                                $ans = strip_tags($exe_post['write']['answer']);
                                if (iconv_strlen($ans) > Reply::COUNT_CHARS)
                                    $exe_post['write']['answer']
                                        = mb_strimwidth($ans, 0, Reply::COUNT_CHAR-1);

                                $files = $_FILES['Fullexam'];
                                $filesSort = [
                                    'names' => $files['name']['sections'][$sect['id']]['exercises'][$exe['id']]['write']['answerFiles'],
                                    'types' => $files['type']['sections'][$sect['id']]['exercises'][$exe['id']]['write']['answerFiles'],
                                    'tmp_names' => $files['tmp_name']['sections'][$sect['id']]['exercises'][$exe['id']]['write']['answerFiles'],
                                    'errors' => $files['error']['sections'][$sect['id']]['exercises'][$exe['id']]['write']['answerFiles'],
                                    'sizes' => $files['size']['sections'][$sect['id']]['exercises'][$exe['id']]['write']['answerFiles'],
                                ];
                                
                                $reply = new Reply;
                                if ($filesSort['names'][0] != '') {
                                    for ($i=0; $i < count($filesSort['names']); $i++) { 
                                        $isType = false;
                                        foreach (Reply::FILE_TYPES as $type) {
                                            $offset = strlen($filesSort['names'][$i])-strlen($type)-2;
                                            if (stripos($filesSort['names'][$i], ".$type", $offset) >= 0)
                                                $isType = true;
                                        }

                                        if ($isType && $filesSort['sizes'][$i] <= Reply::FILE_SIZE) {
                                            $reply->answerFiles[] = [
                                                'name' => $filesSort['names'][$i],
                                                'type' => $filesSort['types'][$i],
                                                'tmp_name' => $filesSort['tmp_names'][$i],
                                                'error' => $filesSort['errors'][$i],
                                                'size' => $filesSort['sizes'][$i],
                                            ];
                                        }
                                    }
                                }
                                
                                if (!empty($reply->answerFiles))
                                    $exe_post['write']['archiveFile'] = $reply->createZip();
                            }
                        }
                        break;
                }
                $fullexam_post['sections'][$sect['id']]['exercises'][$exe['id']] = $exe_post;
                $stats['sections'][$sect['id']]['exercises'][$exe['id']] = $task_stat;
            }
        }

        if (!$isGuest) {
            if ($isPrem) {
                $result = new Result;
                $result->fullexam_id = $fullexam_db['id'];
                $result->user_id = $user->id;
                $fullexam_post['user_exp'] = $stats['exp'];
                $fullexam_post['max_exp'] = $stats['max_exp'];
                $fullexam_post['user_points'] = $stats['points'];
                $fullexam_post['max_points'] = $fullexam_db['max_points'];
                $result->answers = json_encode($fullexam_post);
                $result->save();

                $examsStats['full_last']['id'] = $fullexam_db['id'];
                $examsStats['full_last']['date'] = time();
                $examsStats['full_last']['points'] = $stats['points'];
                $fullStats[Yii::$app->params['subInx']]['exams'] = $examsStats;
            }

            // обновляем статистику
            $user->statistics = $this->exeStatUpdate($fullStats, $stats);
            // добавляем опыт
            $user->addExp($stats['exp']);
            // сохраняем модель
            $user->save();
        }

        return ['req'=>1, 'exp'=>$stats['exp'], 'points'=>$stats['points']]; // возвращаем успех
    }

    public function actionCheckExercise()
    {
        // return $this->debug($_POST).$this->debug($_FILES);
        Yii::$app->response->format = Response::FORMAT_JSON;
        // Получаем ответы
        $exercise = $_POST['Exercise'];
        $full = Fullexam::find()->select(['id','subject_id','course_id','publish'])
            ->where(['id'=>$exercise['fullexam_id'], 'publish' => 1])->asArray()->limit(1)->one();

        // Проверяем важные данные
        if (
            empty($exercise)
            || empty($exercise['id'])
            || empty($exercise['type'])
            || empty($exercise['fullexam_id'])
            || empty($exercise['section_id'])
            || !$full
            || !Section::find()->where(['id'=>$exercise['section_id'], 'publish' => 1])->limit(1)->exists()
            || !$model = Exercise::find()->select('fullexam_points')
                ->where(['id'=>$exercise['id'], 'publish' => 1])->asArray()->limit(1)->one()
        )
            return ['req'=>0];
        
        // Если пользователь авторизирован, получаем его модель
        $isGuest = Yii::$app->user->isGuest;
        $isPrem = false;
        if (!$isGuest) {
            $user = Yii::$app->user->identity;
            $isPrem = Student::find()->where(['learner_id' => $user->id, 'course_id' => $full['course_id']])->asArray()->limit(1)->one();
            $isPrem = ($isPrem && $isPrem['end_at'] > time());
        }

        // устанавливаем флаг ошибки, id задания и свичуем тип задания
        $stats = Exercise::STATS_EXAM_ARR;
        $stats['id'] = $exercise['fullexam_id'];
        $task_stat = Exercise::STAT_EXAM_TASK;
        switch ($exercise['type']) {
            case ExamType::TEST: // если Тест
                $task_stat['task'] = $exercise['test']['id'];
                // получаем тест
                $test = Test::find()
                    ->select(['id','qst_exp','correct_answers'])
                    ->where(['id'=>$task_stat['task'], 'publish' => 1])
                    ->asArray()->limit(1)->one();
                if (!$test)
                    return ['req'=>0];

                // декодируем json
                $userAnswers = json_decode($exercise['test']['answers'], true);
                $rightAnswers = json_decode($test['correct_answers'], true);

                // Перебираем массив с правильными ответами
                $err = false;
                $qst_points = (int)($model['fullexam_points']/count($rightAnswers));
                $countRight = 0;
                foreach ($rightAnswers as $qstId => $ansArr) {
                    $local_err = true;
                    if ($userAnswers[$qstId]) { // если пользователь ответил на вопрос
                        $diff_u = array_diff($userAnswers[$qstId], $ansArr['answers']); // вычитаем массивы
                        $diff_t = array_diff($ansArr['answers'], $userAnswers[$qstId]); // вычитаем массивы
                        if (!$diff_u && !$diff_t) { // если останется хоть один элемент в массивах, тогда ученик допустил ошибку
                            $local_err = false;
                            $stats['exp'] += $test['qst_exp'];
                            $stats['points'] += $qst_points;
                            ++$countRight;
                        }
                    }

                    if ($ansArr['themes'])
                        $stats['themes'][($local_err)?'err':'corr'] = array_merge($stats['themes'][($local_err)?'err':'corr'], $ansArr['themes']);

                    if ($local_err)
                        $err = true;
                }

                if ($qst_points == 0 && count($rightAnswers) == $countRight)
                    $stats['points'] = $model['fullexam_points'];

                $task_stat['corr'] = (!$rightAnswers || $err)?0:1;
                break;

            case ExamType::CORRELATE:
                $task_stat['task'] = $exercise['correlate']['id'];
                // получаем тест
                $correlate = Correlate::find()
                    ->select(['id','pair_exp','themes','qst_hidden'])
                    ->where(['id'=>$task_stat['task'], 'publish' => 1])
                    ->with(['pairs'])
                    ->asArray()->limit(1)->one();
                if (!$correlate)
                    return ['req'=>0];
                
                // декодируем json
                $userAnswers = json_decode($exercise['correlate']['answers'], true);
                $correlate['themes'] = json_decode($correlate['themes'], true);

                $err = false;
                $countQst = 0;
                foreach ($correlate['pairs'] as $pair) {
                    if ($correlate['qst_hidden'] || !empty($pair['qst_text']))
                        ++$countQst;
                }

                $pair_points = (int)($model['fullexam_points']/$countQst);
                $countRight = 0;
                // Перебираем массив с правильными ответами
                foreach ($correlate['pairs'] as $i => $pair) {
                    $local_err = true;
                    if ($userAnswers[$pair['id']] && $pair['id'] == $userAnswers[$pair['id']]) { // если пользователь соотнёс верно
                        $local_err = false;
                        $stats['exp'] += $correlate['pair_exp'];
                        $stats['points'] += $pair_points;
                        ++$countRight;
                    }

                    if ($correlate['themes'][$pair['id']])
                        $stats['themes'][($local_err)?'err':'corr'] = array_merge($stats['themes'][($local_err)?'err':'corr'], $correlate['themes'][$pair['id']]);

                    if ($local_err)
                        $err = true;
                }

                if ($pair_points == 0 && $countQst == $countRight)
                    $stats['points'] = $model['fullexam_points'];

                $task_stat['corr'] = (!$correlate['pairs'] || $err)?0:1;
                break;

            case ExamType::ADDITION:
                $task_stat['task'] = $exercise['addition']['id'];
                $addition = Addition::find()
                    ->select(['id', 'text', 'word_exp'])
                    ->where(['id'=>$task_stat['task'], 'publish' => 1])
                    ->asArray()->limit(1)->one();
                if (!$addition)
                    return ['req'=>0];

                // декодируем json
                $userAnswers = $exercise['addition']['answers'];
                $rightAnswers = [];
                preg_replace_callback(
                    '/\_\(([\s\S]*?)\)/',
                    function ($matches) use (&$rightAnswers) {
                        $arr = explode('/',$matches[1]);
                        $answ = [ 'corr' => $arr[1], 'theme' => (!empty($arr[2])) ? [(int)$arr[2]] : [] ];
                        if (!empty($arr[0]))
                            $rightAnswers[$arr[0]] = $answ;
                        else
                            $rightAnswers[] = $answ;
                    },
                    $addition['text']
                );

                // Перебираем массив с правильными ответами
                $err = false;
                $word_points = (int)($model['fullexam_points']/count($rightAnswers));
                $countRight = 0;
                foreach ($rightAnswers as $word => $ansArr) {
                    $local_err = true;

                    if (!empty($userAnswers[$word])) {
                        $corr = false;
                        if (strpos($ansArr['corr'], '_') !== false) {
                            $ansList = explode('_', $ansArr['corr']);
                            foreach ($ansList as $corrVal) {
                                if (mb_strtolower(trim($userAnswers[$word])) == mb_strtolower(trim($corrVal))) {
                                    $corr = true;
                                    break;
                                }
                            }
                        } else if (mb_strtolower(trim($userAnswers[$word])) == mb_strtolower($ansArr['corr']))
                            $corr = true;
    
                        // если пользователь правильно ответил на вопрос
                        if ($corr) {
                            $local_err = false;
                            $stats['exp'] += $addition['word_exp'];
                            $stats['points'] += $word_points;
                            ++$countRight;
                        }
                    }

                    if (!empty($ansArr['theme']))
                        $stats['themes'][($local_err)?'err':'corr'] = array_merge($stats['themes'][($local_err)?'err':'corr'], $ansArr['theme']);

                    if ($local_err)
                        $err = true;
                }

                if ($word_points == 0 && count($rightAnswers) == $countRight)
                    $stats['points'] = $model['fullexam_points'];

                $task_stat['corr'] = (!$rightAnswers || $err)?0:1;
                // return $this->debug($stats);
                break;

            case ExamType::WRITE:
                if ($isGuest || !$isPrem)
                    return ['req'=>0];

                $task_stat['task'] = $exercise['write']['id'];
                if (Write::find()->where(['id'=>$task_stat['task'], 'publish' => 1])->limit(1)->exists()) {
                    $task_stat['skip'] = 1;
                    $ans = $exercise['write']['answer'];

                    if (isset($ans)) {
                        $ans = strip_tags($ans);
                        if (iconv_strlen($ans) > Reply::COUNT_CHARS)
                            $ans = mb_strimwidth($ans, 0, Reply::COUNT_CHAR-1);
                    }

                    $files = $_FILES['Exercise'];
                    $filesSort = [
                        'names' => $files['name']['write']['answerFiles'],
                        'types' => $files['type']['write']['answerFiles'],
                        'tmp_names' => $files['tmp_name']['write']['answerFiles'],
                        'errors' => $files['error']['write']['answerFiles'],
                        'sizes' => $files['size']['write']['answerFiles'],
                    ];
                    
                    $reply = new Reply;
                    $reply->user_id = Yii::$app->user->id;
                    $reply->examwrite_id = $task_stat['task'];
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
                        // $reply->uploadAnswerFiles();
                    $reply->save();
                } else
                    return ['req'=>0];
                break;
            
            default: // Заглушка на всякий
                return ['req'=>0];
        }
        $stats['sections'][$exercise['section_id']]['exercises'][$exercise['id']] = $task_stat;

        // Если тип не письменный, т.к. он проверяется учителем и пользователь авторизирован для записи в статистику
        if (!$isGuest) {
            // обновляем статистику
            $user->statistics = $this->exeStatUpdate(json_decode($user->statistics, true), $stats);
            // добавляем опыт
            $user->addExp($stats['exp']);
            // сохраняем модель
            $user->save();
        }

        return ['req'=>1, 'exp'=>$stats['exp'], 'points'=>$stats['points']]; // возвращаем успех
    }

    public function actionDeleteHomework()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $names = $_POST['file'];
        if (empty($names) || $names == [])
            return 0;

        foreach ((array)$names as $name) {
            $url = Yii::getAlias("@fileWrites/$name");
            if (file_exists($url)) 
                unlink($url);
        }
        
        return 1;
    }

    public function actionSkip($exercise, $task)
    {
        $model = Exercise::find()->select(['id','section_id','type'])->where(['id'=>$exercise, 'publish'=>1])
            ->with([
                'section' => function ($query) {
                    $query->select(['id','fullexam_id'])->where(['publish'=>1]);
                },
                'section.fullexam' => function ($query) {
                    $query->select(['id'])->where(['publish'=>1]);
                }
            ])->asArray()->limit(1)->one();

        

        if ($model == null || $model['section'] == null || $model['section']['fullexam'] == null)
            throw new NotFoundHttpException('Страница не найдена!');

        switch ($model['type']) {
            case ExamType::TEST: // если Тест
                if (Test::find()->where(['id'=>$task, 'publish'=>1])->limit(1)->exists())
                    break;

            case ExamType::CORRELATE:
                if (Correlate::find()->where(['id'=>$task, 'publish'=>1])->limit(1)->exists())
                    break;

            case ExamType::ADDITION:
                if (Addition::find()->where(['id'=>$task, 'publish'=>1])->limit(1)->exists())
                    break;

            case ExamType::WRITE:
                if (Write::find()->where(['id'=>$task, 'publish'=>1])->limit(1)->exists())
                    break;
            
            default:
            return 1;
                throw new NotFoundHttpException('Страница не найдена!');
        }

        // Если тип не письменный, т.к. он проверяется учителем и пользователь авторизирован для записи в статистику
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $stats = [
                'id' => $model['section']['fullexam']['id'],
                'points' => 0,
                'exp' => 0,
            ];
            $stats['sections'][$model['section']['id']]['exercises'][$model['id']] = [
                'task' => $task,
                'corr' => 0,
                'skip' => 1,
            ];
            
            // обновляем статистику
            $user->statistics = $this->exeStatUpdate(json_decode($user->statistics, true), $stats);
            // сохраняем модель
            $user->save();
        }

        return $this->redirect(['/exam/'.$model['section']['fullexam']['id'], 'exercise'=>$model['id']]);
    }

    // public function actionSubscribe($id)
    // {
    //     $user = Yii::$app->user->identity;
    //     $student = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$id])->limit(1)->one();
    //     if (!$student) {
    //         $student = new Student;
    //         $student->learner_id = $user->id;
    //         $student->course_id = $id;
    //     }
    //     $student->start_at = time();
    //     $student->end_at = time()+(1*24*60*60);
    //     $student->save();
    //     return $this->debug($student);
    // }
}
