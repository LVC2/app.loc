<?php

// Файл: Engine/Core/Cache.php

declare(strict_types=1);

namespace Engine\Core;

use Redis;
use RedisException;

/**
 * Класс для работы с Redis-кэшем.
 */
class Cache
{
    protected ?Redis $redis = null;
    protected string $keyPrefix = 'app:';

    /**
     * Конструктор. Устанавливает соединение с Redis.
     * * @param array $config Конфигурационные данные Redis.
     */
    public function __construct(array $config)
    {
        try {
            $this->redis = new Redis();
            $this->redis->connect(
                $config['host'] ?? '127.0.0.1',
                $config['port'] ?? 6379,
                (float)($config['timeout'] ?? 0.0)
            );
            $this->redis->setOption(Redis::OPT_PREFIX, $this->keyPrefix);

            // Если Redis не отвечает, $this->redis->connect() может вернуть false,
            // но в PHP >= 8.0 это чаще выбрасывает исключение при отсутствии подключения.
        } catch (RedisException $e) {
            // Если Redis недоступен, мы можем логировать ошибку, но не прерывать работу
            // чтобы сайт продолжал работать через БД.
            error_log("Redis connection error: " . $e->getMessage());
            $this->redis = null;
        }
    }

    /**
     * Проверяет доступность Redis.
     * * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->redis !== null;
    }

    /**
     * Устанавливает значение в кэш.
     * * @param string $key Ключ.
     * @param mixed $value Значение.
     * @param int $ttl Время жизни в секундах (по умолчанию 1 час).
     * @return bool
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        if (!$this->isAvailable()) return false;

        // Сериализуем данные
        $data = serialize($value);

        return $this->redis->set($key, $data, $ttl);
    }

    /**
     * Получает значение из кэша.
     * * @param string $key Ключ.
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        if (!$this->isAvailable()) return null;

        $data = $this->redis->get($key);

        if ($data === false) {
            return null;
        }

        // Десериализуем данные
        return unserialize($data);
    }

    /**
     * Удаляет ключ из кэша.
     * * @param string $key Ключ.
     * @return int
     */
    public function delete(string $key): int
    {
        if (!$this->isAvailable()) return 0;
        return $this->redis->del($key);
    }
}