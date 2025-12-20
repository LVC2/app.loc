<?php
declare(strict_types=1);

namespace Engine\Core;

use Engine\Start;

/**
 * Базовый класс для всех контроллеров приложения.
 * Предоставляет доступ к ядру Start.
 */
abstract class Controller
{
    protected Start $app;

    public function __construct(Start $app)
    {
        $this->app = $app;
    }
}