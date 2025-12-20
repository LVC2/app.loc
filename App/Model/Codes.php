<?php

// Файл: App/Model/Codes.php

declare(strict_types=1);

namespace App\Model;

use Engine\Core\Model;

/**
 * Модель для работы с таблицей 'codes'.
 */
class Codes extends Model
{
    /**
     * Получает список кодов с фильтрацией и пагинацией.
     *
     * @param int $page Текущая страница.
     * @param int $limit Элементов на страницу.
     * @param array $filters Дополнительные фильтры (например, 'tag', 'search').
     * @return array<string, mixed>
     */
    public function getPaginatedCodes(int $page, int $limit, array $filters = []): array
    {
        $offset = ($page - 1) * $limit;

        $where = "WHERE status = 1";
        $params = [];

        // 1. Обработка фильтрации по тегам/поиску
        if (!empty($filters['search'])) {
            // Экранируем строку поиска для предотвращения SQL-инъекций и корректного LIKE
            $search = '%' . $this->db->connection->real_escape_string($filters['search']) . '%';
            $where .= " AND (title LIKE '{$search}' OR description LIKE '{$search}')";
        }

        // 2. Получаем общее количество
        $totalSql = "SELECT COUNT(id) FROM codes {$where}";
        $total = (int)$this->db->fetch($totalSql)['COUNT(id)']; // fetchCount() в Database.php уже реализован

        // 3. Получаем сами коды
        $sql = "SELECT id, user_id, title, description, created_at FROM codes 
                {$where} 
                ORDER BY created_at DESC 
                LIMIT {$limit} OFFSET {$offset}";

        $codes = $this->db->fetchAll($sql);

        // 4. Генерируем данные пагинации
        $totalPages = (int)ceil($total / $limit);

        return [
            'codes' => $codes,
            'pagination' => [
                'totalItems' => $total,
                'perPage' => $limit,
                'currentPage' => $page,
                'totalPages' => $totalPages,
            ]
        ];
    }

    /**
     * Получает один код по ID.
     *
     * @param int $id ID кода.
     * @return array<string, mixed>|null
     */
    public function getCodeById(int $id): ?array
    {
        $safeId = $this->db->connection->real_escape_string((string)$id);

        $sql = "SELECT * FROM codes WHERE id = {$safeId} AND status = 1";
        return $this->db->fetch($sql);
    }

    /**
     * Удаляет код.
     * * @param int $id ID кода.
     * @return bool
     */
    public function deleteCode(int $id): bool
    {
        // В реальном приложении здесь должна быть проверка прав пользователя!
        $safeId = $this->db->connection->real_escape_string((string)$id);

        // В продакшене лучше использовать "мягкое" удаление (status = 0)
        $sql = "UPDATE codes SET status = 0 WHERE id = {$safeId}";
        return (bool)$this->db->query($sql);
    }
}