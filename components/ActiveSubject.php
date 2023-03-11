<?php

namespace app\components;

class ActiveSubject
{
    public const MAIN = 0;              // Главная страница
    public const ENGLISH = 1;           // Английский
    public const RUSSIAN = 2;           // Русский
    public const LITERATURE = 3;        // Литература
    public const MATHEMATICS = 4;       // Математика
    public const SOCIAL_SCIENCE = 5;    // Обществознание
    public const PHYSICS = 6;           // Физика
    public const CHEMISTRY = 7;         // Химия
    public const HISTORY = 8;           // История
    public const BIOLOGY = 9;           // Биология
    public const INFORMATICS = 10;      // Информатика
    public const GEOGRAPHY = 11;        // Георграфия
    public const DEUTSCH = 12;          // Немецкий
    public const SPANISH = 13;          // Испанский
    public const FRENCH = 14;           // Французкий

    public static function getTypesArr() {
        return [
            self::MAIN => 'Главная страница',
            self::ENGLISH => 'Английский язык',
            self::RUSSIAN => 'Русский язык',
            self::LITERATURE => 'Литература',
            self::MATHEMATICS => 'Математика',
            self::SOCIAL_SCIENCE => 'Обществознание',
            self::PHYSICS => 'Физика',
            self::CHEMISTRY => 'Химия',
            self::HISTORY => 'История',
            self::BIOLOGY => 'Биология',
            self::INFORMATICS => 'Информатика',
            self::GEOGRAPHY => 'География',
            self::DEUTSCH => 'Немецкий язык',
            self::SPANISH => 'Испанский язык',
            self::FRENCH => 'Францезкий язык',
        ];
    }

    public static function getTypeLable($exam, $default = null) {
        $constArr = self::getTypesArr();
        return isset($constArr[$exam]) ? $constArr[$exam] : $default;
    }
}
