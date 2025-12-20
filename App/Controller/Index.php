<?php
// Файл: App/Controller/Index.php (Обновленный)

declare(strict_types=1);

namespace App\Controller;

use Engine\Core\Controller;
use App\Model\Forum; // Подключаем модель

class Index extends Controller
{
    public function indexAction(): void
    {
        // 1. Создаем экземпляр модели
        $forumModel = new Forum($this->app);

        // 2. Получаем последние 6 тем
        $latestPosts = $forumModel->getLatestPosts(6);

        // 3. Рендерим шаблон, передавая данные
        $this->app->getRender()->render('Home.php', [
            'latestPosts' => $latestPosts
        ]);
    }
}