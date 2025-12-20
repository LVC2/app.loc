<?php
declare(strict_types=1);

namespace App\Api;

use Engine\Core\Api;
use App\Model\ForumModel;

/**
 * API-контроллер для главной страницы.
 * Здесь обрабатываются запросы вида /api/home/...
 */
class Home extends Api
{
    protected ForumModel $forumModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->forumModel = new ForumModel($this->app);
    }

    /**
     * Эндпоинт: Получает последние темы форума.
     * Маршрут: /api/home/latestTopics
     * @return void
     */
    public function latestTopicsAction(): void
    {
        $latestTopics = $this->forumModel->getLatestTopics();

        $this->jsonResponse($latestTopics);
    }
}
