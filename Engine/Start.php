<?php
declare(strict_types=1);

namespace Engine;

use Engine\Core\Config;
use Engine\Core\Auth;
use Engine\Core\Cache;
use Engine\Core\Database;
use Engine\Core\Router;
use Engine\Core\User;
use Engine\Core\Template;

class Start
{
    protected string $rootPath;
    protected Database $db;
    protected Config $config;
    protected Cache $cache;
    protected Auth $auth;
    protected User $user;
    public Router $router;

    protected Template $template;
    /**
     * Конструктор.
     * @param string $rootPath Корневой путь приложения.
     */
    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
        $this->config = new Config($this->rootPath);
        $this->template = new Template($this);
        $this->init();
    }

    protected function init(): void
    {
        // 1. Инициализация Database
        $this->db = new Database($this->config->get('database'));

        // 2. Инициализация Cache (заглушка)
        $this->cache = new Cache($this->config->get('cache'));

        // 3. Инициализация Auth
        $this->auth = new Auth($this->db, $this->cache);

        // 4. Инициализация User (зависит от Auth, DB, Cache)
        $this->user = new User($this->db, $this->auth, $this->cache);

        // 5. Инициализация Router (требует объект Start, но обычно не требует User)
        $this->router = new Router($this);
    }

    /**
     * Получает корневой путь приложения.
     * (Исправляет ошибку Cannot access protected property Engine\Start::$rootPath)
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Получает данные о бане пользователя.
     * (Исправляет ошибку Undefined property: Engine\Start::$ban)
     * @return array
     */
    public function getBanData(): array
    {
        // Заглушка, используемая в Home.php для передачи в JS
        return [];
    }
    /**
     * Получает объект подключения к базе данных.
     * @return Database
     */
    public function getDb(): Database
    {
        return $this->db;
    }
    /**
     * Получает объект управления аутентификацией.
     * @return Auth
     */
    public function getAuth(): Auth
    {
        return $this->auth;
    }
    /**
     * Получает объект текущего пользователя.
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    /**
     * Получает объект Ban (Заглушка/Геттер).
     * @return object
     */
    public function getBan(): object
    {
        // Временно возвращаем объект, который имитирует метод isBanned
        // Это необходимо, пока не будет реализован реальный класс Ban и его инициализация.
        return new class {
            public function isBanned($fullData = false): ?array {
                return null;
            }
        };
    }
    /**
     * Возвращает экземпляр шаблонизатора (Template).
     * Используется в View-файлах для получения путей или рендеринга.
     * @return Template
     */
    public function getRender(): Template
    {
        return $this->template;
    }
    public function getConfig(): Template
    {
        return $this->config;
    }
}