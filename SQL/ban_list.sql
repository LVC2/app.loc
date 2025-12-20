CREATE TABLE `ban_list` (
                            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `user_id` INT UNSIGNED NULL, -- NULL, так как возможен бан только по IP/soft_hash
                            `ip` VARCHAR(45) NULL,       -- IPv4 (15) или IPv6 (45)
                            `soft_hash` VARCHAR(255) NULL,
                            `reason` VARCHAR(255) NOT NULL,
                            `admin_id` INT UNSIGNED NULL, -- admin_id
                            `time_start` INT UNSIGNED NOT NULL,
                            `time_end` INT UNSIGNED NULL,
                            `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    -- Индексы
                            INDEX `idx_user_id` (`user_id`),
                            INDEX `idx_ip` (`ip`),
                            INDEX `idx_soft_hash` (`soft_hash`),

    -- Связь с таблицей пользователей
                            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);