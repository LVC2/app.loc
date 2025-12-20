<?php
declare(strict_types=1);

namespace Engine\Core;

/**
 * Класс для управления подключением и запросами к базе данных (MariaDB/MySQL).
 */
class Database
{
    /**
     * @var \mysqli Объект соединения mysqli.
     */
    protected \mysqli $connection;

    /**
     * Конструктор устанавливает соединение с базой данных и выбирает нужную схему.
     * * @param array $config Конфигурация базы данных (host, username, password, database, charset).
     * @throws \Exception Если соединение или выбор базы данных не удался.
     */
    public function __construct(array $config)
    {
        // 1. Устанавливаем соединение с сервером
        // Передача только host, user, pass (без database)
        $this->connection = new \mysqli(
            $config['host'],
            $config['username'],
            $config['password']
        // Не передаем 'database' здесь, чтобы явно вызвать select_db ниже
        );

        if ($this->connection->connect_error) {
            throw new \Exception("Ошибка подключения к MariaDB: " . $this->connection->connect_error);
        }

        // 2. ЯВНО ВЫБИРАЕМ БАЗУ ДАННЫХ (Устранение ошибки "No database selected")
        if (!$this->connection->select_db($config['database'])) {
            throw new \Exception("Ошибка выбора базы данных '{$config['database']}': " . $this->connection->error);
        }

        // 3. Устанавливаем кодировку
        $this->connection->set_charset($config['charset'] ?? 'utf8mb4');
    }

    /**
     * Получает объект соединения mysqli для прямого использования.
     * (Требуется классом User для выполнения запросов)
     * * @return \mysqli
     */
    public function getConnection(): \mysqli
    {
        return $this->connection;
    }

    /**
     * Выполняет запрос к базе данных.
     * * @param string $sql SQL-запрос.
     * @return \mysqli_result|bool Результат запроса или false при ошибке.
     */
    public function query(string $sql): \mysqli_result|bool
    {
        $result = $this->connection->query($sql);

        if (!$result) {
            // В продакшене лучше логгировать, а не выводить
            error_log("DB Query Error: " . $this->connection->error . " | Query: " . $sql);
            return false;
        }

        return $result;
    }

    /**
     * Выполняет запрос и извлекает все строки.
     * * @param string $sql SQL-запрос.
     * @return array|null Массив результатов или null.
     */
    public function fetchAll(string $sql): ?array
    {
        $result = $this->query($sql);
        if ($result && $result instanceof \mysqli_result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            return $data;
        }
        return null;
    }

    /**
     * Выполняет запрос и извлекает одну строку.
     * * @param string $sql SQL-запрос.
     * @return array|null Ассоциативный массив или null.
     */
    public function fetch(string $sql): ?array
    {
        $result = $this->query($sql);
        if ($result && $result instanceof \mysqli_result) {
            $data = $result->fetch_assoc();
            $result->free();
            return $data;
        }
        return null;
    }
}