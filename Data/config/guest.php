<?php

// Файл: Data/config/guest.php

return [
    // Уникальный ID для гостя (0 или другой ID, который никогда не будет занят реальным пользователем)
    'id' => 0,

    // Имя, отображаемое для неавторизованных пользователей
    'username' => 'Гость',

    // Роль для проверки прав
    'role' => 'guest',

    // Разрешения для гостя (пример)
    'permissions' => [
        'can_view_codes' => true,
        'can_post_comment' => false,
        'can_register' => true,
        'can_access_admin' => false,
    ],

    // Максимальное количество запросов в минуту (для API, если нужно ограничить)
    'rate_limit' => 60,
];