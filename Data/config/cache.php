<?php

// Файл: config/cache.php

return [
    'driver' => 'redis',
    'host' => '127.0.0.1',
    'port' => 6379, // Стандартный порт Redis
    'prefix' => 'app_cache_',
    'ttl' => 3600, // Время жизни кэша по умолчанию (1 час)
];