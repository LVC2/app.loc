<?php
declare(strict_types=1);

namespace App\Controller;

use Engine\Core\Controller;

/**
 * Контроллер для раздела "Библиотека кодов".
 */
class Codes extends Controller
{
    /**
     * @var \App\Model\Codes
     */
    protected \App\Model\Codes $model;

    public function __construct(\Engine\Start $app)
    {
        parent::__construct($app);
        // Инициализируем модель при создании контроллера
        $this->model = new \App\Model\Codes($app);
    }

    /**
     * Метод для рендеринга основной HTML-оболочки (для Vue.js).
     * Соответствует роуту GET /codes или GET /
     */
    public function index(): void
    {
        $this->renderShell('Home.php');
    }

    /**
     * API: Получение списка кодов с пагинацией и фильтрацией.
     * Соответствует роуту GET /api/codes
     */
    public function apiList(): void
    {
        $filtered = $this->app->filter->auto();

        $page = (int)($filtered['page'] ?? 1);
        $limit = (int)($filtered['limit'] ?? 20);

        $data = $this->model->getPaginatedCodes($page, $limit, $filtered);

        $this->jsonResponse([
            'status' => 'success',
            'codes' => $data['codes'],
            'pagination' => $data['pagination']
        ]);
    }

    /**
     * API: Получение одного кода по ID.
     * Соответствует роуту GET /api/codes/{id}
     */
    public function apiGet(int $id): void
    {
        $code = $this->model->getCodeById($id);

        if ($code === null) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Code not found'], 404);
            return;
        }

        $this->jsonResponse(['status' => 'success', 'code' => $code]);
    }

    /**
     * API: Создание нового кода.
     * Соответствует роуту POST /api/codes
     */
    public function apiCreate(): void
    {
        if ($this->app->user->isGuest()) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Authorization required'], 401);
            return;
        }

        $filtered = $this->app->filter()->auto();

        $title = $this->app->getDB()->connection()->real_escape_string($filtered['title'] ?? '');
        $content = $this->app->getDb()->connection()->real_escape_string($filtered['content'] ?? '');
        $userId = $this->app->getUser()->get('id');

        if (empty($title) || empty($content)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Title and content are required'], 400);
            return;
        }

        $sql = "INSERT INTO codes (user_id, title, content, created_at, status) 
                VALUES ({$userId}, '{$title}', '{$content}', NOW(), 1)";

        $this->app->getDB()->query($sql);
        $newId = $this->app->db->last_id();

        if ($newId) {
            $this->jsonResponse(['status' => 'success', 'message' => 'Code created', 'id' => $newId], 201);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }

    /**
     * API: Удаление кода.
     * Соответствует роуту DELETE /api/codes/{id}
     */
    public function apiDelete(int $id): void
    {
        if (!$this->app->user->hasRole('admin') && $this->app->user->get('id') !== $this->model->getCodeById($id)['user_id']) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Permission denied'], 403);
            return;
        }

        if ($this->model->deleteCode($id)) {
            $this->jsonResponse(['status' => 'success', 'message' => 'Code deleted']);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Failed to delete code'], 500);
        }
    }
}