<?php
declare(strict_types=1);

namespace Engine\Core;

/**
 * Класс для управления авторизацией пользователей.
 */
class Auth
{
    protected Database $db;
    protected Cache $cache;

    public const SESSION_KEY = 'user_id';
    public const COOKIE_KEY = 'remember_token';

    /**
     * @param Database $db
     * @param Cache $cache
     */
    public function __construct(Database $db, Cache $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
    }

    /**
     * Получает ID текущего авторизованного пользователя из сессии.
     * @return int
     */
    public function getUserId(): int
    {
        // Session::get() должен существовать
        $userId = Session::get(self::SESSION_KEY);
        return is_numeric($userId) && $userId > 0 ? (int)$userId : 0;
    }

    /**
     * Проверяет, авторизован ли пользователь.
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->getUserId() > 0;
    }

    /**
     * Выход пользователя из системы.
     * (Исправляет ошибку Call to undefined method Engine\Core\Auth::logout())
     * @return void
     */
    public function logout(): void
    {
        // 1. Очистка сессии (Session::remove() должен существовать)
        Session::remove(self::SESSION_KEY);

        // 2. Удаление куки "Запомнить меня"
        if (isset($_COOKIE[self::COOKIE_KEY])) {
            setcookie(self::COOKIE_KEY, '', time() - 3600, '/');
            unset($_COOKIE[self::COOKIE_KEY]);
        }
    }

    /**
     * Устанавливает сессию по известному ID пользователя (используется после проверки токена).
     * @param int $userId
     * @return void
     */
    public function restoreSessionById(int $userId): void
    {
        Session::set(self::SESSION_KEY, $userId);
    }

    /**
     * Верифицирует пароль пользователя.
     * @param int $userId ID пользователя.
     * @param string $password Пароль, введенный пользователем.
     * @return bool
     */
    public function verifyPassword(int $userId, string $password): bool
    {
        $sql = "SELECT pass, nw_pass FROM users WHERE id = {$userId}";
        $hashes = $this->db->fetch($sql);

        if (!$hashes) return false;

        // Проверка нового хеша (password_hash/verify)
        if (!empty($hashes['nw_pass']) && password_verify($password, $hashes['nw_pass'])) {
            return true;
        }

        // Проверка старого хеша (md5(md5($pass)))
        $oldHash = md5(md5($password));
        if (!empty($hashes['pass']) && $oldHash === $hashes['pass']) {
            return true;
        }

        return false;
    }
}