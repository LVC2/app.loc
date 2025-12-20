<?php
declare(strict_types=1);

namespace Engine\Core;

/**
 * Класс-обертка для управления сессиями PHP.
 */
class Session
{
    /**
     * Инициализирует сессию, если она еще не запущена.
     * @return void
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Получает значение из сессии по ключу.
     * @param string $key Ключ сессии.
     * @param mixed $default Значение по умолчанию, если ключ не существует.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Устанавливает значение в сессию.
     * @param string $key Ключ сессии.
     * @param mixed $value Значение.
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Удаляет ключ из сессии.
     * (Исправляет ошибку Call to undefined method Engine\Core\Session::remove())
     * @param string $key Ключ сессии.
     * @return void
     */
    public static function remove(string $key): void
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Полностью уничтожает текущую сессию.
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}