<?php

namespace app\components;

class AppointmentStatus
{
    public const ALL_ERROR = -5;
    public const TEACHER_SUCCESS_STUDENT_ERROR = -4;
    public const TEACHER_DIFFICULT = -3;
    public const STUDENT_ERROR = -2;
    public const TEACHER_ERROR = -1;
    public const ACTIVE = 0;
    public const TEACHER_SUCCESS = 1;
    public const STUDENT_SUCCESS = 2;
    public const STUDENT_SUCCESS_TEACHER_ERROR = 3;
    public const STUDENT_SUCCESS_TEACHER_DIFFICULT = 4;
    public const ALL_SUCCESS = 5;

    public static function getStatusArr() {
        return [
            self::ALL_ERROR => 'Не поняли друг друга',
            self::TEACHER_SUCCESS_STUDENT_ERROR => 'Учитель не явился, но отметил проведённое занятие',
            self::TEACHER_DIFFICULT => 'У учителя возникли сложности',
            self::STUDENT_ERROR => 'Учитель не явился',
            self::TEACHER_ERROR => 'Ученик не явился',
            self::ACTIVE => 'Активный',
            self::TEACHER_SUCCESS => 'Учитель подтвердил проведение занятия',
            self::STUDENT_SUCCESS => 'Ученик подтвердил проведение занятия',
            self::STUDENT_SUCCESS_TEACHER_ERROR => 'Ученик не явился, но отметил проведённое занятие',
            self::STUDENT_SUCCESS_TEACHER_DIFFICULT => 'У учителя возникли сложности',
            self::ALL_SUCCESS => 'Занятие прошло успешно',
        ];
    }

    public static function getStatusLable($status, $default = null) {
        $constArr = self::getStatusArr();
        return isset($constArr[$status]) ? $constArr[$status] : $default;
    }
}
