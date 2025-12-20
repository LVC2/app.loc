<?php
declare(strict_types=1);

namespace Engine\Core;

use Engine\Start;
use Engine\TemplateError;

/**
 * Класс шаблонизатора для Full SPA.
 */
class Template
{
    protected Start $app;
    protected string $viewsPath;
    protected string $rootPath;

    public function __construct(Start $app)
    {
        $this->app = $app;
        $this->rootPath = $this->app->getRootPath();

        $this->viewsPath = rtrim($this->rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR;
    }

    /**
     * Рендеринг минимальной HTML-оболочки для загрузки Vue.js.
     * @param string $templateName Имя файла оболочки (например, 'Home.php').
     * @param array $variables Дополнительные переменные.
     * @throws TemplateError В случае, если файл шаблона не найден.
     */
    public function render(string $templateName, array $variables = []): void
    {
        if (pathinfo($templateName, PATHINFO_EXTENSION) !== 'php') {
            $templateName .= '.php';
        }

        $templateFile = $this->viewsPath . $templateName;

        if (!is_file($templateFile)) {
            throw new TemplateError("Шаблон оболочки не найден: {$templateFile}");
        }

        $app = $this->app;

        extract($variables);

        ob_start();
        require $templateFile;
        echo ob_get_clean();
    }

    /**
     * Возвращает полный путь к файлу вида (View) для прямого require.
     * @param string $viewName Имя вида (напр., 'Layout/Sidebar').
     * @return string
     */
    public function getPath(string $viewName): string
    {
        if (pathinfo($viewName, PATHINFO_EXTENSION) !== 'php') {
            $viewName .= '.php';
        }
        return $this->viewsPath . $viewName;
    }
}