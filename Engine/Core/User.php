<?php
declare(strict_types=1);

namespace Engine\Core;

use Exception;

/**
 * Класс, представляющий текущего пользователя с кэшированием.
 */
class User
{
    /**
     * @var array<string, mixed> Данные пользователя, загруженные из БД/кэша.
     */
    protected array $data = [];
    protected Database $db;
    protected Auth $auth;
    protected Cache $cache;
    protected const CACHE_TTL = 900; // 15 минут для данных пользователя

    /**
     * @param Database $db
     * @param Auth $auth
     * @param Cache $cache
     */
    public function __construct(Database $db, Auth $auth, Cache $cache)
    {
        $this->db = $db;
        $this->auth = $auth;
        $this->cache = $cache;

        $this->loadUserData();
    }

    /**
     * Главный метод загрузки данных пользователя с учетом кэша и куки.
     */
    protected function loadUserData(): void
    {
        // getUserId() возвращает 0 для гостя, или реальный ID из сессии.
        $userId = $this->auth->getUserId();

        // 1. Проверка активной сессии и кэша (самый быстрый путь)
        if ($userId > 0) {
            $cacheKey = $this->getUserCacheKey($userId);
            if ($data = $this->cache->get($cacheKey)) {
                $this->data = $data;
                return; // Успех: данные из Кэша
            }
        }

        // 2. Проверка куки "Запомнить меня" (если сессия умерла или только что пришли)
        $token = Cookie::get(Auth::COOKIE_KEY);
        if ($userId === 0 && $token !== null) { // Если гость, но есть токен

            $restoredUserId = $this->getUserIdByToken($token);

            if ($restoredUserId > 0) {
                $userId = $restoredUserId;
                // Восстанавливаем сессию по ID (требует наличия restoreSessionById в Auth)
                $this->auth->restoreSessionById($userId);
            } else {
                // Невалидный токен: сбрасываем куки
                Cookie::delete(Auth::COOKIE_KEY);
            }
        }

        // 3. Загрузка из БД или установка данных гостя
        if ($userId > 0) { // Только если пользователь авторизован
            $data = $this->fetchUserDataFromDb($userId);
            if ($data) {
                $this->data = $data;
                // Записываем в кэш
                $this->cache->set($this->getUserCacheKey($userId), $data, self::CACHE_TTL);
            } else {
                // Пользователь не найден в БД, сбрасываем авторизацию
                $this->auth->logout();
            }
        } else {
            // Если гость (ID=0), устанавливаем минимальные данные гостя
            $this->data = [
                'id' => 0,
                'name' => 'Гость',
                'latname' => 'guest',
                'email' => '',
                'role' => 'guest'
            ];
        }
    }

    /**
     * Загружает данные пользователя из БД.
     * @param int $userId
     * @return array<string, mixed>|null
     */
    protected function fetchUserDataFromDb(int $userId): ?array
    {
        // Строка 94 (ранее): SELECT id, name, latname, email, role, pass, nw_pass FROM users WHERE id = {$safeId}
        $safeId = $this->db->getConnection()->real_escape_string((string)$userId);

        // Используем latname и name, как вы просили.
        $sql = "SELECT id, name, latname, email, role, pass, nw_pass FROM users WHERE id = {$safeId}";
        $userData = $this->db->fetch($sql);

        if ($userData) {
            // Удаляем хеши паролей
            unset($userData['pass'], $userData['nw_pass']);

            // Добавляем alias 'username' для обратной совместимости, если требуется
            if (!isset($userData['username'])) {
                $userData['username'] = $userData['latname'];
            }
        }

        return $userData;
    }

    // --- ПУБЛИЧНЫЕ МЕТОДЫ ---

    /**
     * Получает значение определенного поля из данных пользователя.
     * (Исправляет ошибку Call to undefined method Engine\Core\User::get())
     * @param string $key Ключ поля (например, 'name', 'id').
     * @param mixed $default Значение по умолчанию.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Проверяет, авторизован ли пользователь.
     * (Исправляет ошибку Call to undefined method Engine\Core\User::isAuthorized())
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->auth->isAuthorized();
    }

    /**
     * Проверяет, является ли пользователь гостем.
     * @return bool
     */
    public function isGuest(): bool
    {
        return !$this->isAuthorized();
    }

    /**
     * Формирует ключ кэша для пользователя.
     * @param int $userId
     * @return string
     */
    protected function getUserCacheKey(int $userId): string
    {
        return "user:{$userId}:data";
    }

    /**
     * Заглушка: Поиск ID пользователя по токену "Запомнить меня" в БД.
     * @param string $token
     * @return int
     */
    protected function getUserIdByToken(string $token): int
    {
        return 0;
    }
}