<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/Engine/Autoload.php';
registerPsr4Autoloader(ROOT_PATH);

use Engine\Start;
use Engine\TemplateError;
use Engine\DatabaseError;
use Engine\ConfigError;

/**
 * Обрабатывает все Throwable (Exception и Error) и выводит в стилизованном шаблоне.
 * @param Throwable $e Исключение или ошибка.
 * @return void
 */
function handleCriticalError(Throwable $e): void
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    static $executed = false;
    if ($executed) {
        return;
    }
    $executed = true;

    http_response_code(500);

    $errorTitle = ($e instanceof TemplateError) ? "Ошибка загрузки шаблона" : "Критический сбой приложения";
    $errorMessage = $e->getMessage();
    $errorDetails = [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];

    $errorTemplatePath = ROOT_PATH . '/App/Template/ErrorTemplate.php';

    if (file_exists($errorTemplatePath)) {
        ob_start();
        require $errorTemplatePath;
        echo ob_get_clean();
    } else {
        echo "<h1>Резервный сбой (500)</h1>";
        echo "<p>Произошла ошибка: " . htmlspecialchars($errorMessage) . "</p>";
    }

    exit(1);
}

/**
 * Глобальный обработчик исключений, вызываемый PHP.
 */
set_exception_handler('handleCriticalError');

/**
 * Обработчик завершения работы, ловит Fatal Errors (сбои компиляции/парсинга).
 */
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_RECOVERABLE_ERROR])) {
        $e = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        handleCriticalError($e);
    }
});

try {
    $app = new Start(ROOT_PATH);
    $app->router->dispatch();

} catch (ConfigError $e) {
    handleCriticalError($e);
} catch (DatabaseError $e) {
    handleCriticalError($e);
} catch (TemplateError $e) {
    handleCriticalError($e);
} catch (Throwable $e) {
    handleCriticalError($e);
}