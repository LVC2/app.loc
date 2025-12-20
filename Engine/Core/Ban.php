<?php

// Файл: Engine/Core/Ban.php

declare(strict_types=1);

namespace Engine\Core;

use Engine\Start;

class Ban
{
    protected Start $app;

    public function __construct(Start $app)
    {
        $this->app = $app;
    }

    /**
     * Генерирует уникальный идентификатор "SOFT" (программное обеспечение/браузер).
     * * @return string SHA256 хеш.
     */
    protected function generateSoftHash(): string
    {
        // Для простоты используем комбинацию User-Agent и части IP.
        // В реальной жизни нужно добавить разрешение экрана, часовой пояс, Canvas Fingerprint и т.д.
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ipPart = substr($this->getCurrentIp(), 0, 10);

        return hash('sha256', $userAgent . $ipPart . date('Ymd')); // Добавляем дату, чтобы хеш менялся
    }

    /**
     * Получает текущий IP пользователя.
     * * @return string
     */
    protected function getCurrentIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Проверяет, забанен ли текущий пользователь (по IP или SOFT).
     * * @param bool $checkUserId Дополнительно проверять по ID пользователя, если он авторизован.
     * @return array<string, mixed>|null Возвращает данные бана или null.
     */
    public function isBanned(bool $checkUserId = false): ?array
    {
        $db = $this->app->db;
        $ip = $db->connection->real_escape_string($this->getCurrentIp());
        $softHash = $db->connection->real_escape_string($this->generateSoftHash());
        $currentTime = time();

        $where = "ip = '{$ip}' OR soft_hash = '{$softHash}'";

        if ($checkUserId && $this->app->user->isAuthorized()) {
            $userId = $this->app->user->get('id');
            $where .= " OR user_id = {$userId}";
        }

        $sql = "SELECT * FROM ban_list 
                WHERE ({$where}) 
                AND is_active = 1 
                AND (time_end IS NULL OR time_end > {$currentTime}) 
                LIMIT 1";

        $banData = $db->fetch($sql);

        if ($banData && ($banData['time_end'] === null || $banData['time_end'] > $currentTime)) {
            return $banData; // Бан активен
        }

        return null;
    }

    /**
     * Накладывает бан.
     * * @param array $options Опции бана: ['ip', 'soft_hash', 'user_id', 'duration', 'reason', 'admin_id'].
     * @return bool
     */
    public function addBan(array $options): bool
    {
        $db = $this->app->db;

        $timeStart = time();

        // Время окончания: NULL для бессрочного, или time() + duration (в секундах)
        $timeEnd = $options['duration'] > 0 ? $timeStart + $options['duration'] : null;

        // Подготовка данных
        $ip = $options['ip'] ?? null;
        $softHash = $options['soft_hash'] ?? null;
        $userId = $options['user_id'] ?? null;
        $reason = $db->connection->real_escape_string($options['reason'] ?? 'Нарушение правил');
        $adminId = $options['admin_id'] ?? 1; // ID администратора (по умолчанию 1)

        $sql = "INSERT INTO ban_list (user_id, ip, soft_hash, reason, admin_id, time_start, time_end)
                VALUES (
                    " . ($userId ? $userId : 'NULL') . ",
                    " . ($ip ? "'" . $db->connection->real_escape_string($ip) . "'" : 'NULL') . ",
                    " . ($softHash ? "'" . $db->connection->real_escape_string($softHash) . "'" : 'NULL') . ",
                    '{$reason}',
                    {$adminId},
                    {$timeStart},
                    " . ($timeEnd ? $timeEnd : 'NULL') . "
                )";

        return (bool)$db->query($sql);
    }
}