<?php

// --- КОНФИГУРАЦИЯ ---
// Укажите абсолютный или относительный путь к корневой папке вашего локального сайта
$baseDir = __DIR__;

// Базовый URL, который будет использоваться в теге <loc>
$baseUrl = 'http://localhost/myproject/';

// Путь для сохранения XML-карты сайта
$sitemapPath = 'sitemap_full.xml';

// Расширения файлов, которые должны быть включены в карту сайта
$allowedExtensions = ['html', 'htm', 'php'];

// Элементы, которые нужно ИСКЛЮЧИТЬ ИЗ КАРТЫ (строго по вашему запросу)
$excludeList = ['.osp', 'scan.php'];
// --------------------


$urls = [];
$dom = new DOMDocument('1.0', 'UTF-8');
$urlSet = $dom->createElement('urlset');
$urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

echo "Начало сканирования локальной директории: " . $baseDir . "\n";

try {
    // Рекурсивный итератор для обхода всех подпапок
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        $filename = $file->getFilename();

        // 1. Пропускаем элементы из списка исключения
        if (in_array($filename, $excludeList) || $filename === 'sitemap_full.xml') {
            if ($file->isDir()) {
                // Если это папка, пропускаем все ее содержимое
                $iterator->next();
            }
            continue;
        }

        // 2. Обрабатываем только файлы, а не папки
        if ($file->isFile()) {
            $extension = $file->getExtension();
            $pathName = $file->getPathname();

            // 3. Проверяем расширение
            if (in_array($extension, $allowedExtensions)) {

                // Получаем относительный путь от базовой директории
                $relativePath = substr($pathName, strlen($baseDir));
                $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

                // Формируем полный URL
                $fullUrl = rtrim($baseUrl, '/') . '/' . $relativePath;

                // Добавляем URL в XML
                $urlElement = $dom->createElement('url');
                $urlElement->appendChild($dom->createElement('loc', htmlspecialchars($fullUrl)));

                // Добавляем дату последнего изменения файла
                $urlElement->appendChild($dom->createElement('lastmod', date('Y-m-d', $file->getMTime())));

                // Устанавливаем приоритет
                if ($relativePath === 'index.php' || $relativePath === 'index.html') {
                    $urlElement->appendChild($dom->createElement('priority', '1.0'));
                } else {
                    $urlElement->appendChild($dom->createElement('priority', '0.8'));
                }

                $urlSet->appendChild($urlElement);
                $urls[] = $fullUrl;
            }
        }
    }

} catch (Exception $e) {
    echo "Ошибка при сканировании: " . $e->getMessage() . "\n";
    exit(1);
}

// Сохранение XML-файла
$dom->appendChild($urlSet);
$dom->formatOutput = true;
$xml = $dom->saveXML();

if (file_put_contents($sitemapPath, $xml) !== false) {
    echo "--- Сканирование завершено ---\n";
    echo "Найдено страниц: " . count($urls) . "\n";
    echo "Карта сайта успешно сохранена в: " . $sitemapPath . "\n";
} else {
    echo "Ошибка: Не удалось сохранить файл карты сайта по пути: " . $sitemapPath . "\n";
}

?>