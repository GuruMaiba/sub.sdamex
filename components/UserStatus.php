<?php

namespace app\components;

class UserStatus
{
    public const INACTIVE = 0;
    public const PERMANENT_BAN = 1;
    public const TEMPORARY_BAN = 2;
    public const DELETE_ACCOUNT = 3;
    public const LOGIN_ERROR = 5;
    public const ACTIVE = 10;

    public static function getStatusArr() {
        return [
            self::ACTIVE => 'Активный',
            self::LOGIN_ERROR => 'Ошибка регистрации',
            self::TEMPORARY_BAN => 'Временный бан',
            self::DELETE_ACCOUNT => 'Аккаунт удалён',
            self::PERMANENT_BAN => 'Вечный бан',
            self::INACTIVE => 'Почта не подтверждена',
        ];
    }

    public static function getStatusLable($status, $default = null) {
        $constArr = self::getStatusArr();
        return isset($constArr[$status]) ? $constArr[$status] : $default;
    }
}
