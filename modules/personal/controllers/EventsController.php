<?php

namespace app\modules\personal\controllers;
use Yii;

use yii\base\InvalidParamException;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use app\controllers\AppController;
use app\models\{User, Teacher};
use app\models\teacher\{Time, TimeEdit, Appointment, AppointmentArchive, Student};
use app\components\{AppointmentStatus};
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class EventsController extends AppController
{
    public function actionIndex()
    {
        $user = Yii::$app->user->identity; // Вытаскиваем из БД
        $is = []; // создаём массив проверок
        $gmt = 2; // default часовой пояс

        if (!$user) // почти не возможно, но на всякий))
            throw new NotFoundHttpException('Пользователь не найден!');

        // получаем часовой пояс из куки
        if (isset($_COOKIE['GMT']))
            $gmt = $_COOKIE['GMT'];

        // Проверяем учитель ли это
        if (Teacher::find()->where(['user_id'=>Yii::$app->user->identity->id])->limit(1)->exists()) {
            $is['teacher'] = true; // Если да
            // Вытаскиваем все записи учеников, предварительно отсортировав
            $appointments = $user->teacher->getActiveAppointments()->with('student')->asArray()->all();
        } else {
            $is['teacher'] = false; // Если нет
            // Вытаскиваем все записи учителей, предварительно отсортировав
            $appointments = $user->getActiveAppointments()->with('teacher.user')->asArray()->all();
        }
        // return $this->debug(date('d F Y H:i', $appointments[0]['begin_date']));

        $days = []; // наша модель
        $gmt = ($gmt*60*60);

        // Полученные записи перебираем
        foreach ($appointments as $key => $app) {
            // Текущее время
            $now = time() + $gmt;
            // + часовой пояс
            $app['begin_date'] += $gmt;
            $app['end_date'] += $gmt;
            // Получаем текстовое представление даты
            $day = $this->ruMonth(date('d F Y', $app['begin_date']));

            if ($now > $app['end_date']) {
                $tName = 'past';
            } else {
                $tName = 'future';
            }

            // Если это учитель
            if ($is['teacher']) {
                $user_id = $app['student_id'];
                $ava = $app['student']['ava'];
                $name = $app['student']['name'];
                $username = $app['student']['username'];
                $active = $app['student']['skype'];
            // Если пользователь
            } else {
                $user_id = $app['teacher_id'];
                $ava = $app['teacher']['user']['ava'];
                $name = $app['teacher']['user']['name'];
                $username = $app['teacher']['user']['username'];
                $active = $app['teacher']['rating'];
            }

            // Если нет имени, то используем Ник
            if ($name == '') {
                $name = $username;
                $username = null;
            }

            // Добавляем в массив новую запись
            $days[$tName][$day][] = [
                // 'id' => [ $app['id'] ],
                'id' => $app['archive_id'],
                'time' => date('H:i', $app['begin_date']).' - '.date('H:i', $app['end_date']),
                'user_id' => $user_id,
                'ava' => $ava,
                'name' => $name,
                'nick' => $username,
                'active' => $active,
            ];
        }

        // ProjectEmployee::deleteAll(['and',
        //     [ 'employee_id'=>$this->id],
        //     ['in', 'project_id', [2,5,7]]]
        // );
        // return $this->debug($days);

        return $this->render('index', [
            'model' => $days,
            'is' => $is,
        ]);
    } // end index

    public function actionEventStatus()
    {
        $id = $_POST['id'];
        $type = $_POST['type'];
        if (isset($id) && isset($type)) {
            $myId = Yii::$app->user->identity->id;
            $event = AppointmentArchive::find()->where(['id' => $id])
                ->andWhere(['or', 'student_id='.$myId, 'teacher_id='.$myId])->with('appointment')->limit(1)->one();

            if ($event) {
                $isTeacher = ($event->teacher_id == $myId) ? true : false;
                $text = $_POST['text'];
                $hasStudent = Student::find()->where([
                    'teacher_id' => $event->teacher_id,
                    'student_id' => $event->student_id
                    ])->limit(1)->exists();
                    $status = $event->status;
                    switch ($type) {
                        case 'succ':
                            if ($isTeacher) {
                                switch ($event->status) {
                                    case AppointmentStatus::ACTIVE:
                                        $status = AppointmentStatus::TEACHER_SUCCESS;
                                        break;
                                    case AppointmentStatus::STUDENT_SUCCESS:
                                        $status = AppointmentStatus::ALL_SUCCESS;
                                        break;
                                    case AppointmentStatus::STUDENT_ERROR:
                                        $status = AppointmentStatus::TEACHER_SUCCESS_STUDENT_ERROR;
                                        break;
                                }
                            } else {
                                switch ($event->status) {
                                    case AppointmentStatus::ACTIVE:
                                        $status = AppointmentStatus::STUDENT_SUCCESS;
                                        break;
                                    case AppointmentStatus::TEACHER_SUCCESS:
                                        $status = AppointmentStatus::ALL_SUCCESS;
                                        break;
                                    case AppointmentStatus::TEACHER_DIFFICULT:
                                        $status = AppointmentStatus::STUDENT_SUCCESS_TEACHER_DIFFICULT;
                                        break;
                                    case AppointmentStatus::TEACHER_ERROR:
                                        $status = AppointmentStatus::STUDENT_SUCCESS_TEACHER_ERROR;
                                        break;
                                }
                            }
                            break;

                        case 'diff':
                            if (empty($text))
                                return 0;
                            if ($isTeacher) {
                                switch ($event->status) {
                                    case AppointmentStatus::ACTIVE:
                                        $status = AppointmentStatus::TEACHER_DIFFICULT;
                                        break;
                                    case AppointmentStatus::STUDENT_SUCCESS:
                                        $status = AppointmentStatus::STUDENT_SUCCESS_TEACHER_DIFFICULT;
                                        break;
                                    case AppointmentStatus::STUDENT_ERROR:
                                        $status = AppointmentStatus::ALL_ERROR;
                                        break;
                                }
                                $event->teacher_message = ($key == 0) ? $text : null;
                            }
                            break;

                        case 'err':
                            if (empty($text))
                                return 0;
                            if ($isTeacher) {
                                switch ($event->status) {
                                    case AppointmentStatus::ACTIVE:
                                        $status = AppointmentStatus::TEACHER_ERROR;
                                        break;
                                    case AppointmentStatus::STUDENT_SUCCESS:
                                        $status = AppointmentStatus::STUDENT_SUCCESS_TEACHER_ERROR;
                                        break;
                                    case AppointmentStatus::STUDENT_ERROR:
                                        $status = AppointmentStatus::ALL_ERROR;
                                        break;
                                }
                                $event->teacher_message = ($key == 0) ? $text : null;
                            } else {
                                switch ($event->status) {
                                    case AppointmentStatus::ACTIVE:
                                        $status = AppointmentStatus::STUDENT_ERROR;
                                        break;
                                    case AppointmentStatus::TEACHER_SUCCESS:
                                        $status = AppointmentStatus::TEACHER_SUCCESS_STUDENT_ERROR;
                                        break;
                                    case AppointmentStatus::TEACHER_DIFFICULT:
                                        $status = AppointmentStatus::ALL_ERROR;
                                        break;
                                    case AppointmentStatus::TEACHER_ERROR:
                                        $status = AppointmentStatus::ALL_ERROR;
                                        break;
                                }
                                $event->student_message = ($key == 0) ? $text : null;
                            }
                            break;
                    } // end switch

                    if ($key == 0 && ($type == 'succ' || $type == 'diff') && $isTeacher && !$hasStudent) {
                        $student = new Student();
                        $student->teacher_id = $event->teacher_id;
                        $student->student_id = $event->student_id;
                        $student->save();
                        $teacher = Teacher::find()->where(['user_id' => $event->teacher_id])->limit(1)->one();
                        $teacher->updateCounters(['student_count' => 1]);
                    }

                    $event->appointment->status = $status;
                    $event->status = $status;
                    if ($status == AppointmentStatus::ALL_ERROR
                        || $status == AppointmentStatus::ALL_SUCCESS
                        || $status == AppointmentStatus::TEACHER_SUCCESS_STUDENT_ERROR
                        || $status == AppointmentStatus::STUDENT_SUCCESS_TEACHER_ERROR
                        || $status == AppointmentStatus::STUDENT_SUCCESS_TEACHER_DIFFICULT)
                        $event->appointment->delete();
                    else
                        $event->appointment->update();
                
                    $event->update();
                // } // end foreach

                return 1;
            } // if events
        } // if ids && type
        return 0;
    } // end POST event-status
} // end controller
