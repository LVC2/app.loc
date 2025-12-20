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
        $this->db = new Database($this->config->get('database'));
        $this->cache = new Cache($this->config->get('cache'));
        $this->auth = new Auth($this->db, $this->cache);
        $this->user = new User($this->db, $this->auth, $this->cache);
        $this->router = new Router($this);
    }

    /**
     * Получает корневой путь приложения.
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Получает данные о бане пользователя.
     * @return array
     */
    public function getBanData(): array
    {
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
        return new class {
            public function isBanned($fullData = false): ?array {
                return null;
            }
        };
    }
    /**
     * Возвращает экземпляр шаблонизатора (Template).
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