<?php

// Файл: Engine/Core/Cookie.php

declare(strict_types=1);

namespace Engine\Core;

/**
 * Класс для работы с HTTP Cookie.
 */
class Cookie
{
    /**
     * Устанавливает новое значение Cookie.
     * * @param string $key Ключ куки.
     * @param string $value Значение куки.
     * @param int $time Срок жизни куки в секундах (по умолчанию 30 дней).
     * @return bool
     */
    public static function set(
        string  $key,
        string  $value,
        int     $time = 2592000,
        string  $path = '/',
        ?string $domain = null,
        bool    $secure = false,
        bool    $httponly = true
    ): bool
    {
        return setcookie($key, $value, time() + $time, $path, $domain, $secure, $httponly);
    }

    /**
     * Получает значение Cookie по ключу.
     * * @param string $key Ключ куки.
     * @return string|null
     */
    public static function get(string $key): ?string
    {
        return $_COOKIE[$key] ?? null;
    }

    /**
     * Удаляет Cookie.
     * * @param string $key Ключ куки.
     * @return bool
     */
    public static function delete(string $key): bool
    {
        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            // Установка срока действия в прошлое
            return setcookie($key, '', time() - 3600, '/');
        }
        return false;
    }
}