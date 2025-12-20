<?php

// Файл: Engine/Core/Page.php

declare(strict_types=1);

namespace Engine\Core;

/**
 * Класс для управления данными страницы (метатеги, пагинация, ресурсы).
 */
class Page
{
    protected string $title = '';
    protected string $description = '';
    protected string $keywords = '';
    protected array $scripts = [];
    protected array $styles = [];
    protected array $pagination = [];

    /**
     * Устанавливает заголовок страницы.
     * * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Получает заголовок страницы.
     * * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Добавляет путь к CSS-файлу.
     * * @param string $path Путь к файлу (относительно корня сайта).
     */
    public function addStyle(string $path): void
    {
        if (!in_array($path, $this->styles)) {
            $this->styles[] = $path;
        }
    }

    /**
     * Возвращает HTML-теги для всех CSS-файлов.
     * * @return string
     */
    public function getStyles(): string
    {
        $html = '';
        foreach ($this->styles as $path) {
            $html .= "<link rel=\"stylesheet\" href=\"{$path}\">\n";
        }
        return $html;
    }

    /**
     * Добавляет путь к JavaScript-файлу.
     * * @param string $path
     */
    public function addScript(string $path): void
    {
        if (!in_array($path, $this->scripts)) {
            $this->scripts[] = $path;
        }
    }

    /**
     * Возвращает HTML-теги для всех JavaScript-файлов.
     * * @return string
     */
    public function getScripts(): string
    {
        $html = '';
        foreach ($this->scripts as $path) {
            // Vue.js и другие скрипты обычно идут в конце <body>
            $html .= "<script src=\"{$path}\"></script>\n";
        }
        return $html;
    }

    /**
     * Генерирует данные пагинации.
     * * @param int $totalItems Общее количество элементов.
     * @param int $perPage Элементов на страницу.
     * @param int $currentPage Текущая страница.
     * @param string $baseUrl Базовый URL для ссылок пагинации.
     */
    public function setPagination(int $totalItems, int $perPage, int $currentPage, string $baseUrl): void
    {
        $totalPages = (int)ceil($totalItems / $perPage);

        $this->pagination = [
            'totalItems' => $totalItems,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'baseUrl' => $baseUrl,
            'nextPage' => $currentPage < $totalPages ? $currentPage + 1 : null,
            'prevPage' => $currentPage > 1 ? $currentPage - 1 : null,
            // Дополнительная логика для генерации списка страниц
        ];
    }

    /**
     * Возвращает данные пагинации.
     * * @return array
     */
    public function getPaginationData(): array
    {
        return $this->pagination;
    }
}