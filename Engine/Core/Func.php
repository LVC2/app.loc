<?php

// Файл: Engine/Core/Func.php

declare(strict_types=1);

namespace Engine\Core;

/**
 * Класс, содержащий набор статических вспомогательных функций (Helpers).
 */
class Func
{
    /**
     * Преобразует кириллические символы в латинские (Транслитерация).
     * Используется для создания ЧПУ (SEO URLs) или латинизированных имен/ников.
     * * @param string $string Исходная строка на русском языке.
     * @return string Латинизированная строка.
     */
    public static function rusToLat(string $string): string
    {
        // Карта транслитерации (согласно ГОСТ 7.79-2000, система Б)
        $cyr = [
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'
        ];
        $lat = [
            'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'shch', '', 'y', '', 'e', 'yu', 'ya',
            'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Zh', 'Z', 'I', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Shch', '', 'Y', '', 'E', 'Yu', 'Ya'
        ];

        // 1. Транслитерация
        $string = str_replace($cyr, $lat, $string);

        // 2. Очистка (оставляем только буквы, цифры и дефисы)
        $string = preg_replace('/[^a-zA-Z0-9\s-]/', '', $string);

        // 3. Замена пробелов и множественных дефисов на один дефис
        $string = preg_replace('/[\s-]+/', '-', $string);

        // 4. Удаление начальных/конечных дефисов и приведение к нижнему регистру
        return strtolower(trim($string, '-'));
    }

    /**
     * Генерирует HTML-код для градиентного текста (CSS).
     *
     * @param string $text Исходный текст.
     * @param string $gradient CSS-значение градиента (например, 'linear-gradient(90deg, #ff0000, #ffff00)').
     * @return string HTML-строка с примененными CSS-стилями.
     */
    public static function gradientText(string $text, string $gradient): string
    {
        $style = sprintf(
            'style="background-image: %s; -webkit-background-clip: text; background-clip: text; color: transparent; display: inline-block;"',
            $gradient
        );

        return sprintf('<span %s>%s</span>', $style, htmlspecialchars($text));
    }

    /**
     * Вычисляет возраст по дате рождения (Unix timestamp).
     *
     * @param int $bdayTime Unix timestamp даты рождения.
     * @return int Возраст в годах.
     */
    public static function getAge(int $bdayTime): int
    {
        $bday = date_create('@' . $bdayTime);
        $today = date_create('today');

        // Если дата рождения в будущем, возвращаем 0
        if ($bday > $today) {
            return 0;
        }

        $age = date_diff($bday, $today)->y;

        return $age;
    }
}