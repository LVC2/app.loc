-- Файл: Data/db_schema/001_create_users_table.sql (ФИНАЛЬНАЯ ВЕРСИЯ V6.1)

CREATE TABLE IF NOT EXISTS `users` (

    -- ---------------------------------------
    -- 1. ИДЕНТИФИКАЦИЯ И СТАТУС
    -- ---------------------------------------
                                       `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                       `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Статус: 0=Неактивен/Ожидает, 1=Активен, 2=Заблокирован',
                                       `role` VARCHAR(50) NOT NULL DEFAULT 'user',

    -- ---------------------------------------
    -- 2. АВТОРИЗАЦИОННЫЕ ДАННЫЕ
    -- ---------------------------------------
                                       `email` VARCHAR(150) NOT NULL UNIQUE,
                                       `phone_number` VARCHAR(15) DEFAULT NULL COMMENT 'Номер телефона для авторизации и 2FA',

                                       `old_password_hash` VARCHAR(200) DEFAULT NULL COMMENT 'Старый пароль для миграции',
                                       `password_hash` VARCHAR(255) DEFAULT NULL COMMENT 'Новый пароль (password_hash)',
                                       `token` CHAR(250) DEFAULT NULL COMMENT 'Активный токен текущей сессии',

    -- ---------------------------------------
    -- 3. ПОДТВЕРЖДЕНИЕ И МОДЕРАЦИЯ
    -- ---------------------------------------
                                       `reg_type` VARCHAR(20) NOT NULL DEFAULT 'web' COMMENT 'Тип регистрации: web, telegram, admin',
                                       `moderator_id` INT UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, который одобрил/активировал аккаунт',

                                       `email_verified_at` INT UNSIGNED DEFAULT NULL COMMENT 'Timestamp подтверждения Email',
                                       `phone_verified_at` INT UNSIGNED DEFAULT NULL COMMENT 'Timestamp подтверждения телефона',

    -- ---------------------------------------
    -- 4. ПРОФИЛЬ И КЛАНЫ
    -- ---------------------------------------
                                       `name` VARCHAR(100) NOT NULL DEFAULT '',
                                       `latname` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Латинизированный ник/логин',
                                       `sex` TINYINT UNSIGNED DEFAULT NULL,
                                       `bday` INT UNSIGNED DEFAULT NULL COMMENT 'Дата рождения (timestamp)',
                                       `about` TEXT DEFAULT NULL,
                                       `clan_id` VARCHAR(10) DEFAULT NULL COMMENT 'Идентификатор клана/группы',
                                       `avatar_path` VARCHAR(255) DEFAULT NULL COMMENT 'Путь к файлу аватара',
                                       `title_icon_path` VARCHAR(255) DEFAULT NULL COMMENT 'Путь к иконке/звезде рядом с ником',

    -- ---------------------------------------
    -- 5. ТЕЛЕГРАМ-ИНТЕГРАЦИЯ
    -- ---------------------------------------
                                       `telegram_id` BIGINT DEFAULT NULL UNIQUE COMMENT 'ID пользователя в Telegram',
                                       `telegram_notify_enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Общая настройка уведомлений в Telegram',

    -- ---------------------------------------
    -- 6. ФИНАНСЫ И СТАТИСТИКА
    -- ---------------------------------------
                                       `rur` DECIMAL(11, 2) NOT NULL DEFAULT 0.00 COMMENT 'Баланс',
                                       `bonus` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Бонусные баллы',
                                       `postforum` INT UNSIGNED NOT NULL DEFAULT 0,
                                       `postcomm` INT UNSIGNED NOT NULL DEFAULT 0,
                                       `upload_count` INT UNSIGNED NOT NULL DEFAULT 0,
                                       `download_count` INT UNSIGNED NOT NULL DEFAULT 0,

    -- ---------------------------------------
    -- 7. ВРЕМЕННЫЕ МЕТКИ И ОНЛАЙН-АКТИВНОСТЬ
    -- ---------------------------------------
                                       `created_at` INT UNSIGNED NOT NULL COMMENT 'Дата регистрации',
                                       `last_ping_at` INT UNSIGNED NOT NULL COMMENT 'Время последнего активного посещения/пинга (для статуса онлайн)',
                                       `current_location_url` VARCHAR(255) DEFAULT NULL COMMENT 'URL страницы или тема, которую сейчас просматривает пользователь',

    -- ---------------------------------------
    -- 8. БЕЗОПАСНОСТЬ И ГЕО
    -- ---------------------------------------
                                       `last_ip` VARCHAR(45) DEFAULT NULL,
                                       `last_soft` VARCHAR(255) DEFAULT NULL,
                                       `login_attempts` INT UNSIGNED NOT NULL DEFAULT 0,

                                       `verification_code` CHAR(6) DEFAULT NULL,
                                       `verification_expires_at` INT UNSIGNED DEFAULT NULL,

                                       `last_activity_country` VARCHAR(100) DEFAULT NULL COMMENT 'Страна последнего входа',
                                       `last_activity_city` VARCHAR(100) DEFAULT NULL COMMENT 'Город последнего входа',

    -- ---------------------------------------
    -- 9. НАСТРОЙКИ
    -- ---------------------------------------
                                       `setting` TEXT DEFAULT NULL COMMENT 'JSON для всех мелких настроек'

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица пользователей системы';

-- Создание индексов (ОБНОВЛЕНО: добавлен UNIQUE INDEX для phone_number)
CREATE UNIQUE INDEX idx_email ON users (email);
CREATE UNIQUE INDEX idx_latname ON users (latname);
CREATE INDEX idx_last_ping ON users (last_ping_at);
CREATE UNIQUE INDEX idx_telegram_id ON users (telegram_id);
-- Добавленный индекс:
CREATE UNIQUE INDEX idx_phone_number ON users (phone_number);