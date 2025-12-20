<?php

// Файл: Data/config/database.php

return [
    'driver' => 'mysql',
    'host' => 'mariadb-11.8',
    'database' => 'app', // Имя вашей базы данных
    'username' => 'root', // Пользователь
    'password' => '',     // Пустой пароль (для локальной разработки, например, в OSPanel)
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '', // Префикс таблиц, если нужен
];