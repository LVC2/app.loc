<?php

// Файл: Data/config/defaultuser.php

return [
    // Настройки, применяемые при регистрации
    'default_role' => 'user',
    'default_status' => 1, // Активен
    'default_post_forum' => 0,
    'default_post_comm' => 0,

    // Изначальные настройки пользователя (JSON по умолчанию)
    'default_settings' => [
        'theme' => 'light',
        'notifications' => true,
        'language' => 'ru',
    ],

    // Данные для гостя (если требуются)
    'guest_data' => [
        'id' => 0,
        'username' => 'Гость',
        'role' => 'guest',
        'status' => 0,
    ]
];