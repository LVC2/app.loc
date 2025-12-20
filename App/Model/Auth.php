<?php

// Файл: App/Model/AuthModel.php

declare(strict_types=1);

namespace App\Model;

use Engine\Core\Model;
use Engine\Core\Func;

class AuthModel extends Model
{
    /**
     * Выполняет регистрацию нового пользователя.
     * * @param string $email
     * @param string $name
     * @param string $password
     * @return array<string, mixed> Результат: ['success' => bool, 'message' => string]
     */
    public function register(string $email, string $name, string $password): array
    {
        $db = $this->app->db;

        // 1. Проверка на БАН по IP/SOFT перед регистрацией
        $banData = $this->app->ban->isBanned();
        if ($banData) {
            return [
                'success' => false,
                'message' => 'Регистрация заблокирована. Причина: ' . htmlspecialchars($banData['reason']),
                'ban' => $banData
            ];
        }

        // 2. Валидация уникальности
        $safeEmail = $db->connection->real_escape_string($email);
        $checkSql = "SELECT id FROM users WHERE email = '{$safeEmail}' LIMIT 1";
        if ($db->fetch($checkSql)) {
            return ['success' => false, 'message' => 'Пользователь с таким email уже зарегистрирован.'];
        }

        // 3. Генерация латинизированного имени
        $baseLatName = Func::rusToLat($name);
        $uniqueLatName = $this->generateUniqueLatName($baseLatName);

        // 4. Хеширование пароля (новый метод)
        $newPassHash = password_hash($password, PASSWORD_BCRYPT);

        // 5. Запись в БД
        $rday = time();
        $sql = "INSERT INTO users (email, name, latname, nw_pass, rday, lday) 
                VALUES (
                    '{$safeEmail}', 
                    '{$db->connection->real_escape_string($name)}', 
                    '{$uniqueLatName}', 
                    '{$newPassHash}', 
                    {$rday}, 
                    {$rday}
                )";

        if ($db->query($sql)) {
            // Автоматический вход после регистрации
            $userId = $db->last_id();
            $this->app->auth->login($userId);
            return ['success' => true, 'message' => 'Регистрация успешна.', 'userId' => $userId];
        }

        return ['success' => false, 'message' => 'Ошибка базы данных при регистрации.'];
    }

    /**
     * Вход пользователя в систему.
     * * @param string $emailOrLatName
     * @param string $password
     * @param bool $remember
     * @return array<string, mixed>
     */
    public function login(string $emailOrLatName, string $password, bool $remember = false): array
    {
        $db = $this->app->db;
        $safeLogin = $db->connection->real_escape_string($emailOrLatName);

        // 1. Поиск пользователя по email или latname
        $sql = "SELECT id, nw_pass, pass FROM users WHERE email = '{$safeLogin}' OR latname = '{$safeLogin}'";
        $user = $db->fetch($sql);

        if (!$user) {
            return ['success' => false, 'message' => 'Неправильный логин или пароль.'];
        }

        $userId = (int)$user['id'];

        // 2. Проверка пароля (используем логику Auth::verifyPassword)
        if (!$this->app->auth->verifyPassword($userId, $password)) {
            return ['success' => false, 'message' => 'Неправильный логин или пароль.'];
        }

        // 3. Проверка на БАН по User ID
        $banData = $this->app->ban->isBanned(true); // Проверяем по ID
        if ($banData) {
            return [
                'success' => false,
                'message' => 'Ваш аккаунт заблокирован. Причина: ' . htmlspecialchars($banData['reason']),
                'ban' => $banData
            ];
        }

        // 4. Успешный вход
        $this->app->auth->login($userId, $remember);

        // Обновление lday (последнее посещение)
        $db->query("UPDATE users SET lday = " . time() . " WHERE id = {$userId}");

        return ['success' => true, 'message' => 'Вход выполнен.'];
    }

    /**
     * Генерация уникального latname.
     */
    protected function generateUniqueLatName(string $baseLatName): string
    {
        $db = $this->app->db;
        $uniqueLatName = $baseLatName;
        $i = 0;

        while (true) {
            $safeLatName = $app->getDB()->connection->real_escape_string($uniqueLatName);
            $checkSql = "SELECT id FROM users WHERE latname = '{$safeLatName}' LIMIT 1";

            if (!$db->fetch($checkSql)) {
                return $uniqueLatName;
            }

            $i++;
            $uniqueLatName = $baseLatName . $i;

            // Защита от бесконечного цикла
            if ($i > 100) {
                throw new \Exception("Не удалось сгенерировать уникальный latname.");
            }
        }
    }
}