<?php

namespace app\components;

class CodeType
{
    public const ALL = 1;           // Стандартный
    // public const EXCLUSIVE = 2;     // Эксклюзивный

    public static function getTypesArr() {
        return [
            self::ALL => 'Стандартный',
            // self::EXCLUSIVE => 'Эксклюзивный',
        ];
    }

    public static function getPropsArr() {
        return [
            self::ALL => [
                'sale_percent' => 10,   // процентная скидка
                'sale_cost' => 0,       // кол. рублей
                'free_access' => [      // материалы в подарок
                    'lessons' => 0,      // уроки
                    'courses' => [],    // ids курсов
                    'webinars' => [],   // ids вебинаров
                ]
            ],
            // self::EXCLUSIVE => [
            //     'sale_percent' => 10,
            //     'sale_cost' => 0,
            //     'free_access' => [
            //         'lessons' => 1,
            //         'courses' => [],
            //         'webinars' => [],
            //     ]
            // ],
        ];
    }

    public static function getTypeLable($type, $default = null) {
        $constArr = self::getTypesArr();
        return isset($constArr[$type]) ? $constArr[$type] : $default;
    }
}
