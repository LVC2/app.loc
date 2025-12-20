<?php
declare(strict_types=1);

namespace App\Model;

use Engine\Start;
use Engine\Core\Controller;

/**
 * Модель для взаимодействия с данными форума.
 */
class ForumModel
{
    protected Start $app;

    public function __construct(Start $app)
    {
        $this->app = $app;
    }

    /**
     * Получает 6 последних активных тем форума.
     * @return array
     */
    public function getLatestTopics(): array
    {
        $sql = "
            SELECT
                t.id AS topic_id,
                t.name AS topic_name,
                t.posts_count,
                t.last_post_at,
                c.id AS category_id,
                c.name AS category_name,
                tu.name AS author_name,
                lpu.name AS last_post_user_name
            FROM
                forum_topics t
            INNER JOIN 
                forum_cat c ON t.category_id = c.id
            LEFT JOIN
                users tu ON t.user_id = tu.id
            LEFT JOIN
                users lpu ON t.last_post_user_id = lpu.id
            WHERE
                t.is_active = 1
                AND c.level = 1
            ORDER BY
                t.last_post_at DESC
            LIMIT 6
        ";

        try {
            $result = $this->app->getDb()->query($sql);
            return $result ?: [];

        } catch (\Throwable $e) {
            error_log("DB Error in ForumModel: " . $e->getMessage());
            return [];
        }
    }
}
