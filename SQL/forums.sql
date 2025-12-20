-- =================================================================
-- 1. ТАБЛИЦА КАТЕГОРИЙ (Объединяет forum_r и forum_pr)
-- =================================================================
CREATE TABLE `app`.`forum_cat` (
                                   `id` INT UNSIGNED NOT NULL,
                                   `parent_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'NULL для корневых категорий (forum_r)',
                                   `level` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0: Категория (forum_r), 1: Раздел (forum_pr)',
                                   `name` VARCHAR(100) NOT NULL,
                                   `description` VARCHAR(255) NULL DEFAULT NULL,
                                   `topics_count` INT UNSIGNED NOT NULL DEFAULT 0,
                                   `posts_count` INT UNSIGNED NOT NULL DEFAULT 0,
                                   PRIMARY KEY (`id`),
                                   INDEX `idx_parent_id` (`parent_id`),
                                   CONSTRAINT `fk_forum_cat_parent`
                                       FOREIGN KEY (`parent_id`)
                                           REFERENCES `app`.`forum_cat` (`id`)
                                           ON DELETE CASCADE
                                           ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- 2. ТАБЛИЦА ПУБЛИЧНЫХ ТЕМ (forum_the)
-- =================================================================
CREATE TABLE `app`.`forum_topics` (
                                      `id` INT UNSIGNED NOT NULL,
                                      `category_id` INT UNSIGNED NOT NULL COMMENT 'Ссылка на forum_cat.id (Раздел)',
                                      `user_id` INT UNSIGNED NOT NULL COMMENT 'Автор темы (forum_the.id_us)',
                                      `name` VARCHAR(255) NOT NULL,
                                      `is_closed` TINYINT NOT NULL DEFAULT 0 COMMENT 'forum_the.closed',
                                      `is_pinned` TINYINT NOT NULL DEFAULT 0 COMMENT 'forum_the.type (1: Прилепленная)',
                                      `is_active` TINYINT NOT NULL DEFAULT 1 COMMENT 'forum_the.activ',
                                      `created_at` INT UNSIGNED NOT NULL COMMENT 'forum_the.time',
                                      `last_post_at` INT UNSIGNED NOT NULL COMMENT 'forum_the.last_time',
                                      `last_post_user_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'ID пользователя последнего поста',
                                      `posts_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'forum_the.opv + 1 (с учетом первого поста)',
                                      `real_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'forum_the.realid (если есть)',
                                      PRIMARY KEY (`id`),
                                      INDEX `idx_category_id` (`category_id`),
                                      INDEX `idx_user_id` (`user_id`),
                                      CONSTRAINT `fk_public_topic_category`
                                          FOREIGN KEY (`category_id`)
                                              REFERENCES `app`.`forum_cat` (`id`)
                                              ON DELETE RESTRICT
                                              ON UPDATE CASCADE,
                                      CONSTRAINT `fk_public_topic_user`
                                          FOREIGN KEY (`user_id`)
                                              REFERENCES `app`.`users` (`id`)
                                              ON DELETE RESTRICT
                                              ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- 3. ТАБЛИЦА ПУБЛИЧНЫХ СООБЩЕНИЙ (forum_msg)
-- =================================================================
CREATE TABLE `app`.`forum_posts` (
                                     `id` INT UNSIGNED NOT NULL,
                                     `topic_id` INT UNSIGNED NOT NULL COMMENT 'forum_msg.id_the',
                                     `user_id` INT UNSIGNED NOT NULL COMMENT 'forum_msg.id_us',
                                     `content` TEXT NULL DEFAULT NULL COMMENT 'forum_msg.msg',
                                     `quote_text` TEXT NULL DEFAULT NULL COMMENT 'forum_msg.cit',
                                     `file_path` VARCHAR(255) NULL DEFAULT NULL COMMENT 'forum_msg.files',
                                     `is_deleted` TINYINT NOT NULL DEFAULT 0 COMMENT 'forum_msg.del',
                                     `is_private` TINYINT NOT NULL DEFAULT 0 COMMENT 'forum_msg.privat',
                                     `private_to_user_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'forum_msg.komu',
                                     `like_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'forum_msg.like',
                                     `created_at` INT UNSIGNED NOT NULL COMMENT 'forum_msg.time',
                                     `edited_by_user_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'forum_msg.who_edit',
                                     `edited_at` INT UNSIGNED NULL DEFAULT NULL COMMENT 'forum_msg.time_edit',
                                     `edit_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'forum_msg.kol_edit',
                                     PRIMARY KEY (`id`),
                                     INDEX `idx_topic_id` (`topic_id`),
                                     INDEX `idx_user_id` (`user_id`),
                                     CONSTRAINT `fk_public_post_topic`
                                         FOREIGN KEY (`topic_id`)
                                             REFERENCES `app`.`forum_topics` (`id`)
                                             ON DELETE CASCADE
                                             ON UPDATE CASCADE,
                                     CONSTRAINT `fk_public_post_user`
                                         FOREIGN KEY (`user_id`)
                                             REFERENCES `app`.`users` (`id`)
                                             ON DELETE RESTRICT
                                             ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- 4. ТАБЛИЦА АДМИН-ТЕМ (adm_forum_them)
-- =================================================================
CREATE TABLE `app`.`a_forum_topics` (
                                        `id` INT UNSIGNED NOT NULL,
                                        `user_id` INT UNSIGNED NOT NULL COMMENT 'Автор темы (adm_forum_them.id_us)',
                                        `name` VARCHAR(255) NOT NULL COMMENT 'adm_forum_them.name',
                                        `is_closed` TINYINT NOT NULL DEFAULT 0 COMMENT 'adm_forum_them.closed',
                                        `created_at` BIGINT UNSIGNED NOT NULL COMMENT 'adm_forum_them.time',
                                        `last_post_at` BIGINT UNSIGNED NOT NULL COMMENT 'adm_forum_them.last_time',
                                        PRIMARY KEY (`id`),
                                        INDEX `idx_admin_topic_user_id` (`user_id`),
                                        CONSTRAINT `fk_admin_topic_user`
                                            FOREIGN KEY (`user_id`)
                                                REFERENCES `app`.`users` (`id`)
                                                ON DELETE RESTRICT
                                                ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =================================================================
-- 5. ТАБЛИЦА АДМИН-СООБЩЕНИЙ (adm_forum_msg)
-- =================================================================
CREATE TABLE `app`.`a_forum_posts` (
                                       `id` INT UNSIGNED NOT NULL,
                                       `topic_id` INT UNSIGNED NOT NULL COMMENT 'adm_forum_msg.id_them',
                                       `user_id` INT UNSIGNED NOT NULL COMMENT 'adm_forum_msg.id_us',
                                       `content` TEXT NULL DEFAULT NULL COMMENT 'adm_forum_msg.msg',
                                       `quote_text` TEXT NULL DEFAULT NULL COMMENT 'adm_forum_msg.cit',
                                       `file_path` VARCHAR(255) NULL DEFAULT NULL COMMENT 'adm_forum_msg.files',
                                       `file_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'adm_forum_msg.filename',
                                       `created_at` BIGINT UNSIGNED NOT NULL COMMENT 'adm_forum_msg.time',
                                       PRIMARY KEY (`id`),
                                       INDEX `idx_admin_post_topic_id` (`topic_id`),
                                       INDEX `idx_admin_post_user_id` (`user_id`),
                                       CONSTRAINT `fk_admin_post_topic`
                                           FOREIGN KEY (`topic_id`)
                                               REFERENCES `app`.`a_forum_topics` (`id`)
                                               ON DELETE CASCADE
                                               ON UPDATE CASCADE,
                                       CONSTRAINT `fk_admin_post_user`
                                           FOREIGN KEY (`user_id`)
                                               REFERENCES `app`.`users` (`id`)
                                               ON DELETE RESTRICT
                                               ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Таблица для отслеживания тем, на которые подписан пользователь (Избранное)
CREATE TABLE `app`.`forum_subscriptions` (
                                             `user_id` INT UNSIGNED NOT NULL,
                                             `topic_id` INT UNSIGNED NOT NULL,
                                             `subscribed_at` INT UNSIGNED NOT NULL,
                                             PRIMARY KEY (`user_id`, `topic_id`),
                                             INDEX `idx_topic_user` (`topic_id`, `user_id`),
                                             CONSTRAINT `fk_sub_user`
                                                 FOREIGN KEY (`user_id`)
                                                     REFERENCES `app`.`users` (`id`)
                                                     ON DELETE CASCADE
                                                     ON UPDATE CASCADE,
                                             CONSTRAINT `fk_sub_topic`
                                                 FOREIGN KEY (`topic_id`)
                                                     REFERENCES `app`.`forum_topics` (`id`)
                                                     ON DELETE CASCADE
                                                     ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Таблица для хранения оценок (лайков/дизлайков) постов
CREATE TABLE `app`.`forum_post_ratings` (
                                            `post_id` INT UNSIGNED NOT NULL COMMENT 'Ссылка на forum_posts.id',
                                            `user_id` INT UNSIGNED NOT NULL COMMENT 'Пользователь, поставивший оценку',
                                            `rating` TINYINT NOT NULL COMMENT 'Оценка: 1 (Лайк), -1 (Дизлайк)',
                                            `rated_at` INT UNSIGNED NOT NULL COMMENT 'Время, когда была поставлена оценка',

                                            PRIMARY KEY (`post_id`, `user_id`),
                                            INDEX `idx_user_post` (`user_id`, `post_id`),

                                            CONSTRAINT `fk_rating_post`
                                                FOREIGN KEY (`post_id`)
                                                    REFERENCES `app`.`forum_posts` (`id`)
                                                    ON DELETE CASCADE
                                                    ON UPDATE CASCADE,
                                            CONSTRAINT `fk_rating_user`
                                                FOREIGN KEY (`user_id`)
                                                    REFERENCES `app`.`users` (`id`)
                                                    ON DELETE CASCADE
                                                    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;