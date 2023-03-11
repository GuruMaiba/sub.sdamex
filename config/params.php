<?php

$params = [
    'adminEmail' => 'team@sdamex.ru',           // Основная почта (ILoveMyWork4321)
    'mailingEmail' => 'norepeat@sdamex.ru',     // Рассылочная почта (%9RnXRKAx4N7&NEg)
    'yandexEmail' => 'sdamex.team@yandex.ru',   // Почта Yandex (ilovemywork2020)
    'shortName' => 'SDAMEX',                    // Короткое имя сайта
    'subInx' => 3,                              // Индекс активного предмета
    'isLang' => false,                          // Если предмет лингвистический
    'listSubs' => [     // Список предметов
        1 => [
            'name' => 'MAIN',
            'lable' => 'Главная страница',
            'link' => (YII_ENV_DEV) ? 'http://sdamex.loc/' : 'https://sdamex.ru/',
            'isActive' => true,
        ], // Главная страница
        2 => [
            'name' => 'ENGLISH',
            'lable' => 'Английский язык',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://eng.sdamex.ru/',
            'isActive' => false,
        ], // Английский
        3 => [
            'name' => 'RUSSIAN',
            'lable' => 'Русский язык',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://rus.sdamex.ru/',
            'isActive' => true,
        ], // Русский
        4 => [
            'name' => 'LITERATURE',
            'lable' => 'Литература',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://ltr.sdamex.ru/',
            'isActive' => false,
        ], // Литература
        5 => [
            'name' => 'MATHEMATICS',
            'lable' => 'Математика',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://mth.sdamex.ru/',
            'isActive' => true,
        ], // Математика
        6 => [
            'name' => 'SOCIAL SCIENCE',
            'lable' => 'Обществознание',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://sub.sdamex.ru/',
            'isActive' => false,
        ], // Обществознание
        7 => [
            'name' => 'PHYSICS',
            'lable' => 'Физика',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://phs.sdamex.ru/',
            'isActive' => false,
        ], // Физика
        8 => [
            'name' => 'CHEMISTRY',
            'lable' => 'Химия',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://chm.sdamex.ru/',
            'isActive' => false,
        ], // Химия
        9 => [
            'name' => 'HISTORY',
            'lable' => 'История',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://hst.sdamex.ru/',
            'isActive' => false,
        ], // История
        10 => [
            'name' => 'BIOLOGY',
            'lable' => 'Биология',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://bio.sdamex.ru/',
            'isActive' => false,
        ], // Биология
        11 => [
            'name' => 'INFORMATICS',
            'lable' => 'Информатика',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://inf.sdamex.ru/',
            'isActive' => false,
        ], // Информатика
        12 => [
            'name' => 'GEOGRAPHY',
            'lable' => 'Георграфия',
            'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://geo.sdamex.ru/',
            'isActive' => false,
        ], // Георграфия
        // 13 => [
        //     'name' => 'DEUTSCH',
        //     'lable' => 'Немецкий',
        //     'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://sub.sdamex.ru/',
        //     'isActive' => false,
        // ], // Немецкий
        // 14 => [
        //     'name' => 'SPANISH',
        //     'lable' => 'Испанский',
        //     'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://sub.sdamex.ru/',
        //     'isActive' => false,
        // ], // Испанский
        // 15 => [
        //     'name' => 'FRENCH',
        //     'lable' => 'Французкий',
        //     'link' => (YII_ENV_DEV) ? 'http://sub.sdamex.loc/' : 'https://sub.sdamex.ru/',
        //     'isActive' => false,
        // ], // Французкий
    ],
    'user.timeTokenExpire' => 30 * 24 * 3600,   // Время жизни токена
    // 'teampsPath' => 'css/images/teamps/',       // Временная папка для изображений ???
    'secretKey' => 'jjCDGOPYuau9HKqG9OjggJPjK8Y9n4I0l5c9O5cK5GqjeNdOl53f3eRMxva43dDF0pBmoUXGtXLt5GkJ9H-rNQ==',
    // 'tinify-api-token' => (YII_ENV_DEV) ? 'yrWjZdSbSYSqcMKnL4MpqY1Hlsyk3YYM' : 'snJSKhnShv0m1YvyyHl9tNdZTrbn6jjv',
    'commonKeyWords' => 'sdamex, sdam exam, сдамекс, сдам, сдам экзамен',
];

$sub = $params['listSubs'][$params['subInx']];

if ($sub && $sub['isActive'])
    $params['shortName'] = "$sub[name] $params[shortName]";

return $params;