<?php

namespace app\components;

class ExamType
{
    public const TEST = 1;      // Тестирование
    public const CORRELATE = 2; // Соотношение А-1, B-2, C-3...
    public const ADDITION = 3;  // Заполнение пропусков
    public const WRITE = 4;     // Практическое задание

    public static function getTypesArr() {
        return [
            self::TEST => 'Тестовое задание',
            self::CORRELATE => 'Задание для сопоставления высказываний по смыслу',
            self::ADDITION => 'Задание на заполнение пропусков',
            self::WRITE => 'Практическое задание',
        ];
    }

    public static function getTypeLable($exam, $default = null) {
        $constArr = self::getTypesArr();
        return isset($constArr[$exam]) ? $constArr[$exam] : $default;
    }
}
