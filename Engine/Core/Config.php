<?php
declare(strict_types=1);

namespace Engine\Core;

use Engine\ConfigError;
use Throwable;

/**
 * Класс для загрузки, хранения и доступа к конфигурационным данным.
 */
class Config
{
    /**
     * @var array<string, array<string, mixed>> Хранилище конфигурационных данных.
     */
    protected array $config = [];

    /**
     * @var string Путь к директории с файлами конфигурации.
     */
    protected string $configPath;

    /**
     * Конструктор.
     * @param string $rootPath Корневой путь проекта.
     */
    public function __construct(string $rootPath)
    {
        $this->configPath = rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        $this->loadConfig();
    }

    /**
     * Загружает все PHP-файлы из директории конфигурации.
     * @throws ConfigError Если директория конфигурации не найдена.
     */
    protected function loadConfig(): void
    {
        if (!is_dir($this->configPath)) {
            throw new ConfigError("Директория конфигурации не найдена: {$this->configPath}");
        }

        $files = glob($this->configPath . '*.php');

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $data = require $file;
            if (is_array($data)) {
                $this->config[$name] = $data;
            } else {
                error_log("Файл конфигурации '{$name}.php' не вернул массив. Файл пропущен.");
            }
        }
    }

    /**
     * Получает значение конфигурации по имени файла и ключу.
     * @param string $name Имя конфигурационного файла (ключ первого уровня).
     * @param string|null $key Ключ внутри конфигурационного массива (ключ второго уровня).
     * @return mixed|null
     */
    public function get(string $name, ?string $key = null): mixed
    {
        if (!isset($this->config[$name])) {
            return null;
        }

        return $key === null ? $this->config[$name] : ($this->config[$name][$key] ?? null);
    }
}