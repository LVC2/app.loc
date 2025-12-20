<?php
declare(strict_types=1);

namespace Engine;

use Exception;

/**
 * Специализированное исключение для ошибок, связанных с шаблонами
 * (например, файл шаблона не найден, проблемы с рендерингом и т.п.).
 */
class TemplateError extends Exception
{
}