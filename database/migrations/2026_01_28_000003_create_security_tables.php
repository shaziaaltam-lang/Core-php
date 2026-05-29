<?php

/**
 * Migration: Create Security Tables (Login Attempts, Sessions, Password Resets, User Logins)
 * Layer 1: Database & Migrations
 * Version: 7.1.0
 */

return [
    'up' => function($pdo) {
        // Login Attempts Table
        $pdo->exec("
        DROP TABLE IF EXISTS `login_attempts`;
        CREATE TABLE `login_attempts` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(255) NOT NULL,
            `user_id` BIGINT UNSIGNED NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` TEXT NULL,
            `was_successful` TINYINT(1) NOT NULL DEFAULT 0,
            `failure_reason` VARCHAR(100) NULL,
            `attempted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_rate_limit` (`email`, `ip_address`, `attempted_at`),
            INDEX `idx_user_attempts` (`user_id`, `attempted_at`),
            INDEX `idx_cleanup_old` (`attempted_at`),
            INDEX `idx_ip_block` (`ip_address`, `attempted_at`),
            INDEX `idx_login_attempts_cleanup` (`attempted_at`),
            CONSTRAINT `fk_login_attempts_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // User Sessions Table
        $pdo->exec("
        DROP TABLE IF EXISTS `user_sessions`;
        CREATE TABLE `user_sessions` (
            `session_id` VARCHAR(128) NOT NULL,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `fingerprint` VARCHAR(255) NOT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` TEXT NOT NULL,
            `payload` LONGTEXT NOT NULL,
            `last_activity` INT UNSIGNED NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`session_id`),
            INDEX `idx_user_active` (`user_id`, `last_activity`),
            INDEX `idx_fingerprint` (`fingerprint`),
            INDEX `idx_cleanup_expired` (`last_activity`),
            INDEX `idx_sessions_cleanup` (`last_activity`),
            CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Password Resets Table
        $pdo->exec("
        DROP TABLE IF EXISTS `password_resets`;
        CREATE TABLE `password_resets` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(255) NOT NULL,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `token` VARCHAR(64) NOT NULL,
            `used_at` TIMESTAMP NULL DEFAULT NULL,
            `expires_at` TIMESTAMP NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `idx_token_unique` (`token`),
            INDEX `idx_email_unused` (`email`, `used_at`),
            INDEX `idx_expired_cleanup` (`expires_at`),
            CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // User Logins Table
        $pdo->exec("
        DROP TABLE IF EXISTS `user_logins`;
        CREATE TABLE `user_logins` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `session_id` VARCHAR(128) NOT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` TEXT,
            `login_method` ENUM('password', '2fa', 'social', 'api') NOT NULL DEFAULT 'password',
            `logged_in_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `logged_out_at` TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_user_logins` (`user_id`, `logged_in_at`),
            CONSTRAINT `fk_user_logins_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    },
    
    'down' => function($pdo) {
        $pdo->exec("DROP TABLE IF EXISTS `user_logins`;");
        $pdo->exec("DROP TABLE IF EXISTS `password_resets`;");
        $pdo->exec("DROP TABLE IF EXISTS `user_sessions`;");
        $pdo->exec("DROP TABLE IF EXISTS `login_attempts`;");
    }
];
