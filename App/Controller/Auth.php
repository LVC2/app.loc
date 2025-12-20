<?php

// Файл: App/Controller/Auth.php

declare(strict_types=1);

namespace App\Controller;

use Engine\Core\Controller;

class Auth extends Controller
{
    /**
     * API: Регистрация нового пользователя.
     * Соответствует роуту POST /api/auth/register
     */
    public function apiRegister(): void
    {
        $filtered = $this->app->filter->auto();
        $model = new \App\Model\AuthModel($this->app);

        // Базовая проверка
        $email = $filtered['email'] ?? '';
        $name = $filtered['name'] ?? '';
        $password = $filtered['password'] ?? '';

        if (empty($email) || empty($name) || strlen($password) < 6) {
            $this->jsonResponse(['success' => false, 'message' => 'Проверьте поля и минимальную длину пароля (6 символов).'], 400);
            return;
        }

        $result = $model->register($email, $name, $password);

        if ($result['success']) {
            $this->jsonResponse($result, 201);
        } else {
            // Если бан, возвращаем 403 Forbidden
            $statusCode = isset($result['ban']) ? 403 : 409;
            $this->jsonResponse($result, $statusCode);
        }
    }

    /**
     * API: Вход пользователя.
     * Соответствует роуту POST /api/auth/login
     */
    public function apiLogin(): void
    {
        $filtered = $this->app->filter->auto();
        $model = new \App\Model\AuthModel($this->app);

        $login = $filtered['login'] ?? ''; // email или latname
        $password = $filtered['password'] ?? '';
        $remember = (bool)($filtered['remember'] ?? false);

        if (empty($login) || empty($password)) {
            $this->jsonResponse(['success' => false, 'message' => 'Необходимо заполнить все поля.'], 400);
            return;
        }

        $result = $model->login($login, $password, $remember);

        if ($result['success']) {
            $this->jsonResponse($result, 200);
        } else {
            // Если бан, возвращаем 403 Forbidden
            $statusCode = isset($result['ban']) ? 403 : 401;
            $this->jsonResponse($result, $statusCode);
        }
    }

    /**
     * API: Выход пользователя.
     * Соответствует роуту POST /api/auth/logout
     */
    public function apiLogout(): void
    {
        $this->app->auth->logout();
        $this->jsonResponse(['success' => true, 'message' => 'Выход выполнен успешно.'], 200);
    }
}