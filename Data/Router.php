<?php
// Файл: Data/Router.php (ИСПРАВЛЕННЫЙ И ДОПОЛНЕННЫЙ)

/**
 * Конфигурация маршрутов приложения.
 * Переменная $router — это экземпляр Engine\Core\Router.
 */

// 1. **Публичные маршруты (для рендеринга оболочки)**
// ВСЕ эти маршруты должны рендерить Home.php, позволяя Vue-Router управлять URL.
// Мы используем 'Index' в качестве контроллера по умолчанию.

$router->add('', 'Index', 'index', 'GET', ['namespace' => 'App\Controller']); // Главная страница (/)
$router->add('login', 'Index', 'index', 'GET', ['namespace' => 'App\Controller']);
$router->add('codes', 'Index', 'index', 'GET', ['namespace' => 'App\Controller']);
$router->add('profile', 'Index', 'index', 'GET', ['namespace' => 'App\Controller']);

// Маршруты с ID: /codes/123
// NOTE: Регулярное выражение, используемое в add, должно быть корректно обработано вашим Router.
$router->add('codes\/(\d+)', 'Index', 'index', 'GET', ['namespace' => 'App\Controller']);


// =================================================================
// 2. **API Маршруты (для Vue.js)**
// Предполагаем, что API-контроллеры находятся в пространстве имен App\Api.
// =================================================================

// 2.1. API для Модуля 'Codes'
// Контроллер: App\Api\Codes.php
$codesNamespace = ['namespace' => 'App\Api'];

// Список и создание
$router->add('api/codes', 'Codes', 'apiList', 'GET', $codesNamespace);
$router->add('api/codes', 'Codes', 'apiCreate', 'POST', $codesNamespace);

// Просмотр, обновление, удаление (Используем (\d+) для захвата ID)
// NOTE: Ваш Router должен передавать ID ($1) в соответствующий метод контроллера.
$router->add('api/codes\/(\d+)', 'Codes', 'apiGet', 'GET', $codesNamespace);
$router->add('api/codes\/(\d+)', 'Codes', 'apiDelete', 'DELETE', $codesNamespace);
// $router->add('api/codes\/(\d+)', 'Codes', 'apiUpdate', 'PUT', $codesNamespace);

// 2.2. API для Форума (Модуль "Последние темы")
// Контроллер: App\Api\Home.php
// ЭТОТ МАРШРУТ МЫ СОЗДАЛИ В ПРЕДЫДУЩЕМ ОТВЕТЕ
$router->add('api/forum/latest', 'Home', 'latestTopicsAction', 'GET', $codesNamespace);


// 3. **Маршруты авторизации (API)**
// Контроллер: App\Api\Auth.php
$authNamespace = ['namespace' => 'App\Api'];

$router->add('api/auth/register', 'Auth', 'apiRegister', 'POST', $authNamespace);
$router->add('api/auth/login', 'Auth', 'apiLogin', 'POST', $authNamespace);
$router->add('api/auth/logout', 'Auth', 'apiLogout', 'POST', $authNamespace);