<?php

// Файл: Engine/Autoload.php

/**
 * Регистрирует автозагрузчик, совместимый с PSR-4.
 *
 * @param string $rootPath Корневой путь проекта (ROOT_PATH).
 */
function registerPsr4Autoloader(string $rootPath): void
{
    // Регистрируем функцию-автозагрузчик
    spl_autoload_register(function (string $className) use ($rootPath) {

        // 1. Указываем корневые директории для пространств имен
        $namespaces = [
            'Engine\\' => $rootPath . '/Engine/',
            'App\\' => $rootPath . '/App/',
        ];

        foreach ($namespaces as $namespace => $directory) {
            // Проверяем, начинается ли имя класса с текущего пространства имен
            if (str_starts_with($className, $namespace)) {

                // Удаляем пространство имен из имени класса
                $relativeClass = substr($className, strlen($namespace));

                // Заменяем разделители пространств имен на разделители директорий
                $file = $directory . str_replace('\\', '/', $relativeClass) . '.php';

                // Если файл существует, подключаем его
                if (is_file($file)) {
                    require $file;
                    return;
                }
            }
        }
    });
}

// В index.php нужно будет вызвать: registerPsr4Autoloader(ROOT_PATH);