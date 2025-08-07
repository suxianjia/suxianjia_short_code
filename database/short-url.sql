-- 短链接数据表结构
CREATE TABLE IF NOT EXISTS `short_urls` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `long_url` TEXT NOT NULL,
  `short_code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `hits` INT UNSIGNED DEFAULT 0,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_short_code` (`short_code`),
  KEY `idx_user` (`user_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;