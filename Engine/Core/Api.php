<?php

declare(strict_types=1);

namespace Engine\Core;

use Engine\Start;

/**
 * Базовый класс для всех API-контроллеров.
 * Обеспечивает стандартизированный вывод JSON.
 */
abstract class Api extends Controller
{
    /**
     * Выводит данные в формате JSON и завершает выполнение скрипта.
     * @param array $data Данные для отправки.
     * @param int $statusCode HTTP-код ответа.
     * @return void
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }
}
