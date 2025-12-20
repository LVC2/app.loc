<?php

// Файл: Engine/Core/Filter.php

declare(strict_types=1);

namespace Engine\Core;

/**
 * Класс для безопасной фильтрации входных данных (POST, GET).
 */
class Filter
{
    /**
     * Автоматически определяет тип запроса (GET/POST) и возвращает очищенный массив.
     * Использует FILTER_SANITIZE_STRING по умолчанию для общей очистки.
     * * @return array<string, mixed> Фильтрованный массив данных.
     */
    public function auto(): array
    {
        $type = match ($_SERVER['REQUEST_METHOD']) {
            'POST' => INPUT_POST,
            default => INPUT_GET,
        };

        $source = match ($type) {
            INPUT_POST => $_POST,
            default => $_GET,
        };

        return $this->filterArray($source, $type);
    }

    /**
     * Фильтрует массив входных данных.
     * * @param array $data Массив для фильтрации ($_POST, $_GET).
     * @param int $type Тип входных данных (INPUT_POST, INPUT_GET).
     * @return array<string, mixed> Очищенный массив.
     */
    protected function filterArray(array $data, int $type): array
    {
        $filteredData = [];
        foreach (array_keys($data) as $key) {
            // Базовый фильтр: очистка строки (удаляет HTML-теги)
            $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS; // Лучше, чем STRING (устарел)

            // Если ключ похож на ID, используем INTEGER
            if (stripos($key, 'id') !== false || stripos($key, 'count') !== false) {
                $filter = FILTER_SANITIZE_NUMBER_INT;
            }

            $value = filter_input($type, $key, $filter);

            if ($value !== false && $value !== null) {
                // Если после фильтрации осталось что-то не пустое
                $filteredData[$key] = $value;
            }
        }
        return $filteredData;
    }
}