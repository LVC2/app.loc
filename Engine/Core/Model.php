<?php

// Файл: Engine/Core/Model.php

declare(strict_types=1);

namespace Engine\Core;

use Engine\Start;

/**
 * Базовый класс для всех моделей.
 * Обеспечивает доступ к базе данных и другим сервисам ядра.
 */
abstract class Model
{
    /**
     * @var Start Экземпляр ядра приложения.
     */
    protected Start $app;

    /**
     * @var Database Объект базы данных.
     */
    protected Database $db;

    /**
     * Конструктор.
     * * @param Start $app
     */
    public function __construct(Start $app)
    {
        $this->app = $app;
        $this->db = $app->getDB();
    }
}