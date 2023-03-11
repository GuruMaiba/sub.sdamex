<?php

namespace app\modules\personal\controllers;
use Yii;

use yii\helpers\Url;
use yii\base\InvalidParamException;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use app\controllers\AppController;
use app\models\{User, Teacher};
use app\models\teacher\{Time, TimeEdit, Appointment, AppointmentArchive, Student};
// use yii\filters\VerbFilter;
// use yii\filters\AccessControl;

class ProfileController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index'             => ['GET'],
                    'change-phrase'      => ['POST'],
                    'appointment'       => ['POST'],
                    'change-skype'      => ['POST'],
                    'set-videolink'     => ['POST'],
                    'change-time'       => ['POST'],
                    'edit-time'         => ['POST'],
                    'change-timelock'   => ['POST'],
                    'change-aboutme'    => ['POST'],
                    'review'            => ['POST'],
                    'review-delete'     => ['POST'],
                ],
            ],
            'accessIndex' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return (Yii::$app->request->get('id') > 0 || !Yii::$app->user->isGuest);
                        },
                    ],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'except' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['change-phrase', 'set-videolink', 'change-time', 'edit-time', 'change-timelock', 'change-aboutme'],
                        'roles' => ['updateProfile'],
                        'roleParams' => ['id' => Yii::$app->request->post('id')],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($id = 0)
    {
        $myId = Yii::$app->user->identity->id;
        $id = ($id == 0 && !Yii::$app->user->isGuest) ? $myId : $id;
        if ($id == 0)
            throw new NotFoundHttpException('Пользователь не найден!');

        $user = User::find()->where(['id' => $id])->with([
            'teacher' => function ($query) {
                $query->with([
                    'time', 'timeEdits',
                    'activeAppointments.student' => function ($query) {
                        $query->select(['id','ava','username','name','skype']);
                    },
                    'students' => function ($query) {
                        // ->andWhere(['is_review'=>true])
                        // ->orWhere(['student_id'=>$myId])
                        // $query->with([
                        //     'student' => function ($query) {
                        //         $query->select(['id','ava','username']);
                        //     },
                        // ]);
                    },
                ]);
            },
        ])->asArray()->limit(1)->one();

        if ($user == null)
            throw new NotFoundHttpException('Пользователь не найден!');

        $user['role'] = current(Yii::$app->authManager->getRolesByUser($user['id']));

        $updateProfile = Yii::$app->user->can('updateProfile', ['id' => $id]);
        $is = [
            'ownProfile'    => ($updateProfile) ? 1 : 0,
            'admin'         => (Yii::$app->user->can('admin')) ? 1 : 0,
            'teacher'       => ($user['teacher']) ? 1 : 0,
            'student'       => 0
        ];

        $weeks = [];
        if ($is['teacher']) {
            $days = [ 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс' ];
            $gmt = (isset($_COOKIE['GMT'])) ? $_COOKIE['GMT'] : 3;
            $day = 24*60*60;
            $now = time();
            $now -= ($now%$day);
            $w = (int)date('w', $now);
            $w = ($w == 0) ? 7 : $w;

            $tTime = [];
            foreach ($user['teacher']['time'] as $key => $time) {
                $h = $time['hour'] + $gmt;
                
                $period = $this->period($h);
                if ($period == '')
                    return false;
    
                if ($h < 0)
                    $time['day'] = (($time['day']-1) > 0) ? $time['day']-1 : 7;
    
                $tTime[$time['day']][$period][($h*(60*60)) + ($time['min']*60)] = [
                    'time' => $this->strTime($h, $time['min']),
                    'class' => 'active'
                ];
            }
            // $this->debug($tTime);
    
            $eTime = [];
            foreach ($user['teacher']['timeEdits'] as $key => $time) {
                $time['date'] += ($gmt*60*60);
                $hm = $time['date'] % $day;
                $h = intdiv($hm, 60*60);
                $m = ($hm % (60*60))/60;
    
                $period = $this->period($h);
                if ($period == '')
                    return false;
    
                $time['date'] -= $hm;
                $eTime[$time['date']][$period][$hm] = [
                    'time' => $this->strTime($h, $m),
                    'class' => ($time['add_or_del']) ? 'add' : 'blc'
                ];
            }
            // return $this->debug($eTime);
    
            $aTime = [];
            foreach ($user['teacher']['activeAppointments'] as $key => $time) {
                $time['begin_date'] += ($gmt*60*60);
                $time['end_date'] += ($gmt*60*60);
                $difference = $time['end_date'] - $time['begin_date'];
                $ts = Appointment::getTimeStep();

                for ($i=0; $i < ($difference / $ts); $i++) { 
                    $date = $time['begin_date']+($ts*$i);
                    $hm = $date % $day;
                    $h = intdiv($hm, 60*60);
                    $m = ($hm % (60*60))/60;
        
                    $period = $this->period($h);
                    if ($period == '')
                        return false;
        
                    $date -= $hm;
                    $aTime[$date][$period][$hm] = [
                        'time' => $this->strTime($h, $m),
                        'class' => 'busy',
                        'id' => $time['student']['id'],
                        'ava' => $time['student']['ava'],
                        'skype' => $time['student']['skype'],
                        'sub' => $time['subject_id'],
                    ];
                }
            }
            // return $this->debug($aTime);
    
            for ($i=0; $i < 4; $i++) {
                $time = $now + ($i * 7 * $day);
                $start = $time - (($w - 1) * $day);
                $end = $time + ((7 - $w) * $day);
            
                if ($i == 0)
                    $weeks[$i]['now'] = $now;
                $weeks[$i]['range'] = date('d F Y', $start) . ' - ' . date('d F Y', $end);
                $weeks[$i]['range'] = $this->ruMonth($weeks[$i]['range']);
    
                $j = 0;
                for ($tDay=$start; $tDay <= $end; $tDay+=$day) {
                    $weeks[$i]['days'][$j]['name'] = $days[$j];
                    $weeks[$i]['days'][$j]['date'] = $tDay;
                    $weeks[$i]['days'][$j]['status'] = (($i == 0 && $tDay == $now) || ($i > 0 && $j == 0)) ? 'active' : '';
    
                    if ($tDay >= $now) {
                        if ($updateProfile) {
                            // $weeks[$i]['days'][$j]['status'] = ($j == 0) ? 'active' : '';
                            $weeks[$i]['days'][$j]['night'] =
                                $this->teacherTime(0,6,$tTime[$j+1]['night'],$eTime[$tDay]['night'],$aTime[$tDay]['night']);
                            $weeks[$i]['days'][$j]['morning'] =
                                $this->teacherTime(6,12,$tTime[$j+1]['morning'],$eTime[$tDay]['morning'],$aTime[$tDay]['morning']);
                            $weeks[$i]['days'][$j]['dinner'] =
                                $this->teacherTime(12,18,$tTime[$j+1]['dinner'],$eTime[$tDay]['dinner'],$aTime[$tDay]['dinner']);
                            $weeks[$i]['days'][$j]['evening'] =
                                $this->teacherTime(18,24,$tTime[$j+1]['evening'],$eTime[$tDay]['evening'],$aTime[$tDay]['evening']);
                        } else {
                            $tg = time()+($gmt*60*60)+($user['teacher']['time_lock']*60*60);
                            $weeks[$i]['days'][$j]['night'] =
                                $this->viewTime($tDay,$tg,$tTime[$j+1]['night'],$eTime[$tDay]['night'],$aTime[$tDay]['night']);
                            $weeks[$i]['days'][$j]['morning'] =
                                $this->viewTime($tDay,$tg,$tTime[$j+1]['morning'],$eTime[$tDay]['morning'],$aTime[$tDay]['morning']);
                            $weeks[$i]['days'][$j]['dinner'] =
                                $this->viewTime($tDay,$tg,$tTime[$j+1]['dinner'],$eTime[$tDay]['dinner'],$aTime[$tDay]['dinner']);
                            $weeks[$i]['days'][$j]['evening'] =
                                $this->viewTime($tDay,$tg,$tTime[$j+1]['evening'],$eTime[$tDay]['evening'],$aTime[$tDay]['evening']);
                        }
                    } else {
                        $weeks[$i]['days'][$j]['status'] = 'disable';
                    }
                    $j++;
                } // end for days
            } // end for weeks
            // $this->debug($weeks);

            // $countReview = 0;
            $user['teacher']['reviews'] = [];
            $user['teacher']['countReviews'] = 0;
            $user['teacher']['countStudents'] = 0;
            foreach ($user['teacher']['students'] as $key => &$student) {
                $student['review_date'] += ($gmt*60*60);
                $student['review_date'] = date('d F Y H:i', $student['review_date']);
                $student['review_date'] = $this->ruMonth($student['review_date']);

                if ($student['student_id'] == $myId) {
                    $myReview = $student;
                    $is['student'] = 1;
                }

                if ($student['is_review']) {
                    if ($student['student_id'] != $myId)
                        $user['teacher']['reviews'][] = $student;
                    ++$user['teacher']['countReviews'];
                }
                ++$user['teacher']['countStudents'];
            }
            unset($student);
            unset($user['teacher']['students']);
        } else { // end if teacher
            $sub = Yii::$app->params['subInx'];
            $user['statistics'] = json_decode($user['statistics'], true);
            $user['statistics'][$sub]['count_likes'] = 0; 
            $teachers = (array)$user['statistics'][$sub]['teachers'];
            if ($teachers != []) {
                $idTeachers = array_keys($teachers);
                $teachersList = User::find()->select(['id', 'ava', 'username', 'name'])->where(['in', 'id', $idTeachers])->asArray()->all();
                foreach ($teachersList as $t) {
                    $teachers[$t['id']]['ava'] = $t['ava'];
                    $teachers[$t['id']]['username'] = $t['username'];
                    $teachers[$t['id']]['name'] = $t['name'];
                    $user['statistics'][$sub]['count_likes'] += $teachers[$t['id']]['count_likes'];
                }
                $user['statistics'][$sub]['teachers'] = $teachers;
            }
        }

        $this->view->title = "Профиль $user[username] | ".Yii::$app->params['shortName'];
        $this->view->registerMetaTag([
            'name' => 'description',
            'content' => ($user['phrase']) ? $user['phrase'] : "Профиль пользователя $user[username] на сайте образовательного проекта SDAMEX.",
        ]);
        $this->view->registerMetaTag([
            'name' => 'keywords',
            'content' => "Профиль, пользователь, $user[username], ".Yii::$app->params['commonKeyWords'],
        ]);

        $sign = ($gmt >= 0) ? '+' : '-';
        $gmt = (10 > $gmt && $gmt > -10) ? "${sign}0${gmt}:00" : "${sign}${gmt}:00";

        $lvl = new User;
        $lvl->id = $user['id'];
        $user['level'] = $lvl->level;

        return $this->render('index', [
            'model' => $user,
            'is' => $is,
            'weeks' => $weeks,
            'my' => [
                'skype' => ($is['teacher'] && !$is['ownProfile']) ? Yii::$app->user->identity->skype : '',
                'review' => $myReview
            ],
            'strgmt' => $gmt
        ]);
    }

    public function actionChangePhrase()
    {
        $id = (int)$_POST['id'];
        $txt = $_POST['txt'];
        if (empty($id))
            return 0;

        $user = User::find()->where(['id'=>$id])->limit(1)->one();
        if ($user) {
            $user->phrase = $txt;
            $user->update();
            return 1;
        }
        return 0;
    }

    public function actionAppointment()
    {
        $id = (int)$_POST['id'];
        $sub = (int)$_POST['subject_id'];
        $day = (int)$_POST['day'];
        $time = $_POST['time'];
        $gmt = (int)$_POST['GMT'];
        $now = time();
        $skype = $_POST['skype'];
        $type = $_POST['type'];
        $timeArr = $this->workTimeArr($day, $time, $gmt);

        if (isset($id) && isset($sub) && isset($day) && isset($time) && isset($gmt) && isset($skype) && isset($type)) {
            $user = Yii::$app->user->identity;

            $teacher = Teacher::find()->select(['user_id', 'subjects', 'time_lock'])->where(['user_id'=>$id])->limit(1)->one();
            $tSub = json_decode($teacher->subjects, true);

            if (($timeArr['day'] <= ($now + ($teacher->time_lock*60*60)) && $type != 'del')) // || !in_array($sub, $tSub)
                return 4; // Если время уже прошло

            if ($user && $teacher) {
                $teacherTime = Time::find()->where([
                    'teacher_id' => $id,
                    'day' => $timeArr['numDay'],
                    'hour' => $timeArr['h'],
                    'min' => $timeArr['m']
                    ])->limit(1)->one();
    
                $editTime = TimeEdit::find()->where([
                    'teacher_id' => $id,
                    'date' => $timeArr['day']
                    ])->limit(1)->one();

                if ($teacherTime != null || ($editTime != null && $editTime->add_or_del)) {
                    $entry = AppointmentArchive::find()->where([
                        'teacher_id'=>$id,
                        'begin_date'=>$timeArr['day']
                        ])->with('appointment')->limit(1)->one();

                    if ( $entry == null && $type == 'add') {
                        if ($user->teacher_class <= 0)
                            return 1; // нет доступных записей
                        $ts = Appointment::getTimeStep();
                        // $entry = AppointmentArchive::find()->where([
                        //         'teacher_id'=>$id, 
                        //         'student_id'=>$user->id,
                        //         'subject_id'=>$sub,
                        //         // 'end_date'=>$timeArr['day'] // DEBUG: СДЕЛАТЬ ПОСЛЕДНЮЮ ДАТУ
                        //     ])->andWhere([
                        //         'or',
                        //         ['end_date'=>$timeArr['day']],
                        //         ['begin_date'=>$timeArr['day']+$ts]
                        //     ])->with('appointment')->limit(1)->one();
                        // return $this->debug($entry);

                        // if ($entry == null || $entry->subject_id != $sub) {
                            $entry = new Appointment();
                            $archive = new AppointmentArchive();
    
                            $entry->teacher_id = $id;
                            $entry->student_id = $user->id;
                            $entry->subject_id = $sub;
                            $entry->begin_date = $timeArr['day'];
                            $entry->end_date = ($timeArr['day']+$ts);
                            $archive->teacher_id = $id;
                            $archive->student_id = $user->id;
                            $archive->subject_id = $sub;
                            $archive->begin_date = $timeArr['day'];
                            $archive->end_date = ($timeArr['day']+$ts);
    
                            $archive->save();
                            $entry->archive_id = $archive->id;
                            $entry->save();
                        // } else {
                        //     $entry->appointment->end_date = ($timeArr['day']+$ts);
                        //     $entry->end_date = ($timeArr['day']+$ts);
                        //     // return $this->debug($entry->appointment);
                        //     $entry->appointment->update();
                        //     $entry->update();
                        // }

                        $user->skype = $skype;
                        $user->teacher_class--;
                        $user->update();

                        return '<img class="ava" user_id="'.$user->id.'" skype="'.$user->skype.'" subject_id="'.$sub.'" src="'.Url::to('@uAvaSmall/'.$user->ava).'">';
                    } else if ($entry->student_id == $user->id && $type == 'del') {
                        if ($entry->begin_date > ($now + 24*60*60)) {
                            $entry->delete();
                            $user->teacher_class++;
                            $user->update();
                            return 2; // запись удалена
                        } else 
                            return 3; // время отмены прошло
                    }
                } // end if на существование времени
            } // end if user && teacher
        } // end if isset

        return 0;
    } // end appointment

    public function actionChangeSkype()
    {
        $skype = $_POST['skype'];
        if (isset($skype)) {
            $user = User::find()->where(['id'=>Yii::$app->user->identity->id])->limit(1)->one();
            if ($user) {
                $user->skype = $skype;
                $user->save();
                return 1;
            }
        }
        return 0;
    } // end change-skype

    public function actionSetVideolink()
    {
        $id = (int)$_POST['id'];
        $link = $_POST['link'];
        if (isset($link)) {
            $options = Teacher::find()->where(['user_id'=>$id])->limit(1)->one();
            if ($options) {
                $link = $this->frameLinkCreation($link);
                // if (strripos($link, 'youtube')) {
                //     $link = explode('?v=', $link);

                //     if (count($link) < 2)
                //         return 0;

                //     $link = array_pop($link);
                //     $link = explode('&', $link);

                //     $link = "https://www.youtube.com/embed/$link[0]";
                // } else if (strripos($link, 'youtu.be')) {
                //     $link = explode('/', $link);
                //     if (count($link) < 2)
                //         return 0;

                //     $link = array_pop($link);
                //     $link = explode('?', $link);

                //     $link = "https://www.youtube.com/embed/$link[0]";
                // } else if (strripos($link, 'vimeo')) {
                //     $link = explode('/', $link);
                //     if (count($link) < 2)
                //         return 0;

                //     $link = array_pop($link);
                //     $link = explode('#', $link);

                //     $link = "https://player.vimeo.com/video/$link[0]";
                // }
                $options->video = $link;
                $options->save();
                return $link;
            }
        }
        return 0;
    } // end set-videolink

    public function actionChangeTime()
    {
        $id = (int)$_POST['id'];
        $day = (int)$_POST['day'];
        $time = $_POST['time'];
        $gmt = (int)$_POST['GMT'];
        
        if (isset($id) && isset($day) && isset($time) && isset($gmt)) {
            $timeArr = $this->workTimeArr($day, $time, $gmt);

            $teacherTime = Time::find()->where([
                'teacher_id' => $id,
                'day' => $timeArr['numDay'],
                'hour' => $timeArr['h'],
                'min' => $timeArr['m']
                ])->limit(1)->one();

            if ($teacherTime == null) {
                $teacherTime = new Time();
                $teacherTime->teacher_id = $id;
                $teacherTime->day = $timeArr['numDay'];
                $teacherTime->hour = $timeArr['h'];
                $teacherTime->min = $timeArr['m'];
                $teacherTime->save();
                return 1;
            } else {
                $teacherTime->delete();
                return 2;
            }
        }
        return 0;
    }

    public function actionEditTime()
    {
        $id = (int)$_POST['id'];
        $day = (int)$_POST['day'];
        $time = $_POST['time'];
        $gmt = (int)$_POST['GMT'];
        
        if (isset($id) && isset($day) && isset($time) && isset($gmt)) {
            $timeArr = $this->workTimeArr($day, $time, $gmt);
            $teacher = Teacher::find()->select(['time_lock'])->where(['user_id'=>$id])->asArray()->limit(1)->one();
            if (!$teacher)
                return 0;
            
            if ( $timeArr['day'] <= (time() + ($teacher['time_lock']*60*60)) )
                return 2;

            $teacherTime = Time::find()->where([
                'teacher_id' => $id,
                'day' => $timeArr['numDay'],
                'hour' => $timeArr['h'],
                'min' => $timeArr['m']
                ])->limit(1)->one();

            $editTime = TimeEdit::find()->where([
                'teacher_id' => $id,
                'date' => $timeArr['day']
                ])->limit(1)->one();

            if ($editTime == null) {
                $editTime = new TimeEdit();
                $editTime->teacher_id = $id;
                $editTime->date = $timeArr['day'];
                $editTime->add_or_del = ($teacherTime == null) ? true : false;
                $editTime->save();
                return ($teacherTime == null) ? 'crAdd' : 'crBlc';
            } else {
                $req = ($editTime->add_or_del) ? 'delAdd' : 'delBlc';
                $editTime->delete();
                return $req;
            }
        } // if isset...
        return 0;
    } // end edit-time

    public function actionChangeTimelock()
    {
        $id = (int)$_POST['id'];
        $hour = (int)$_POST['hour'];
        if (empty($id) || empty($hour))
            return 0;

        $teacher = Teacher::find()->where(['user_id'=>$id])->limit(1)->one();
        if ($teacher && $hour > 0) {
            $teacher->time_lock = $hour;
            $teacher->update();
            return 1;
        }
        return 0;
    }

    public function actionChangeAboutme()
    {
        $id = (int)$_POST['id'];
        $txt = $_POST['txt'];
        if (empty($id))
            return 0;

        $teacher = Teacher::find()->where(['user_id'=>$id])->limit(1)->one();
        if ($teacher) {
            $teacher->about_me = $txt;
            $teacher->update();
            return 1;
        }
        return 0;
    }

    public function actionReview()
    {
        $studentId = Yii::$app->user->identity->id;
        $teacherId = (int)$_POST['teacher_id'];
        $text = $_POST['review_text'];
        $rating = (int)$_POST['review_rating'];
        $anonymous = (int)$_POST['review_anonymously'];
        $gmt = (int)$_POST['gmt'];
        if (empty($teacherId) || empty($text) || empty($rating) || $rating < 1)
            return 0;

        $review = Student::find()->where(['teacher_id'=>$teacherId, 'student_id'=>$studentId])->limit(1)->one();
        if ($review) {
            if (!$review->is_review)
                $review->review_date = time();
            $review->is_review = true;
            $review->review_text = $text;
            $review->review_rating = $rating;
            $review->review_anonymously = $anonymous;
            $review->update();

            $avg = Student::find()->where(['teacher_id'=>$teacherId])->average('review_rating');
            $teacher = Teacher::findOne($teacherId);
            $teacher->rating = $avg;
            $teacher->update();

            return $avg;
        }
        return 0;
    }

    public function actionReviewDelete()
    {
        $studentId = Yii::$app->user->identity->id;
        $teacherId = (int)$_POST['teacher_id'];
        if (empty($teacherId))
            return 0;

        $review = Student::find()->where(['teacher_id'=>$teacherId, 'student_id'=>$studentId])->limit(1)->one();
        if ($review) {
            $review->is_review = false;
            $review->update();
            return 1;
        }
        return 0;
    }

    public function teacherTime($i, $iMax, $tTime = [], $eTime = [], $aTime = []){
        $arr = [];
        $ts = Appointment::getTimeStep();
        for ($i = $i*60*60; $i < ($iMax*60*60); $i += $ts) {
            $h = intdiv($i, 60*60);
            $m = ($i % (60*60))/60;
            $arr[$i] = ['time' => $this->strTime($h,$m), 'class' => null];
        }
        $arr = $this->teacherTimeArr($arr, $tTime);
        $arr = $this->teacherTimeArr($arr, $eTime);
        $arr = $this->teacherTimeArr($arr, $aTime);
        return $arr;
    }

    public function teacherTimeArr($arr, $time){
        if (isset($time)) {
            foreach ($time as $key => $val)
                $arr[$key] = $val;
        }
        return $arr;
    }

    public function viewTime($day, $now, $tTime = [], $eTime = [], $aTime = []) {
        $arr = [];
        if (isset($tTime)) {
            foreach ($tTime as $key => $val) {
                if (($day+$key) > $now)
                    $arr[$key] = $val;
            }
        }
        if (isset($eTime)) {
            foreach ($eTime as $key => $val) {
                if ($val['class'] == 'add' && ($day+$key) > $now) {
                    $val['class'] = 'active';
                    $arr[$key] = $val;
                } else {
                    unset($arr[$key]);
                }
            }
        }
        if (isset($aTime)) {
            foreach ($aTime as $key => $val) {
                if ($val['id'] == Yii::$app->user->identity->id && ($day+$key) > $now)
                    $arr[$key] = $val;
                else
                    unset($arr[$key]);
            }
        }
        ksort($arr);
        return $arr;
    }

    public function strTime($h, $m) {
        $h = $this->frontZero($h);
        $m = $this->frontZero($m);
        return $h.':'.$m;
    }

    public function frontZero($n) {
        return ($n <= 9) ? '0'.$n : ''.$n;
    }

    public function period($h) {
        $period = '';
        if ($h >= 0 && $h < 6) {
            $period = 'night';
        } else if ($h >= 6 && $h < 12) {
            $period = 'morning';
        } else if ($h >= 12 && $h < 18) {
            $period = 'dinner';
        } else if ($h >= 18 && $h < 24) {
            $period = 'evening';
        }
        return $period;
    }

    // DEBUG: Возможно перенсти в родительский класс
    public function workTimeArr($day, $time, $gmt) {
        $arr = [];
        $arr['numDay'] = date('w', $day);
        $arr['numDay'] = ($arr['numDay'] == 0) ? 7 : $arr['numDay'];
        $time = explode(':',$time);
        $arr['h'] = (int)$time[0]-$gmt;
        $arr['m'] = (int)$time[1];
        $day -= ($day%(24*60*60));
        $day += ($arr['h']*60*60) + ($arr['m']*60);
        $arr['day'] = $day;

        return $arr;
    }
}
