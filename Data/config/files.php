<?php

// Файл: Data/config/files.php

return [
    // Общие настройки загрузки
    'upload_max_size' => 1048576 * 5, // 5 MB в байтах
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/gif'],

    // Пути сохранения
    'path_base' => ROOT_PATH . '/Files/', // Корневая папка для всех загрузок
    'path_public' => '/Files/', // Публичный URL-путь для браузера

    // Конкретные настройки
    'avatars' => [
        'path' => 'avatars/',
        'max_width' => 200,
        'max_height' => 200,
        'default_image' => '/Data/image/default_avatar.png',
    ],

    'gallery' => [
        'path' => 'gallery/',
        'max_width' => 1200,
    ],
];