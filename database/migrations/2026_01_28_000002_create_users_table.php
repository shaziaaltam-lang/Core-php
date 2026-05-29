<?php

/**
 * Migration: Create Users Table
 * Layer 1: Database & Migrations
 * Version: 7.1.0
 */

return [
    'up' => function($pdo) {
        $sql = "
        DROP TABLE IF EXISTS `users`;
        CREATE TABLE `users` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `role_id` INT UNSIGNED NOT NULL,
            `uuid` CHAR(36) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(100) NOT NULL,
            `avatar` VARCHAR(255) NULL,
            `phone` VARCHAR(20) NULL,
            `status` ENUM('active', 'pending', 'suspended', 'banned') NOT NULL DEFAULT 'pending',
            `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
            `phone_verified_at` TIMESTAMP NULL DEFAULT NULL,
            `two_factor_secret` VARCHAR(255) NULL,
            `two_factor_enabled` TINYINT(1) NOT NULL DEFAULT 0,
            `last_login_at` TIMESTAMP NULL DEFAULT NULL,
            `last_login_ip` VARCHAR(45) NULL,
            `last_login_user_agent` TEXT NULL,
            `login_fingerprint` VARCHAR(255) NULL,
            `password_changed_at` TIMESTAMP NULL DEFAULT NULL,
            `remember_token` VARCHAR(100) NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `idx_uuid` (`uuid`),
            UNIQUE INDEX `idx_email_unique` (`email`),
            INDEX `idx_role_status` (`role_id`, `status`),
            INDEX `idx_email_verified` (`email_verified_at`),
            INDEX `idx_last_login` (`last_login_at`),
            INDEX `idx_deleted` (`deleted_at`),
            INDEX `idx_users_email_status` (`email`, `status`),
            INDEX `idx_users_role_deleted` (`role_id`, `deleted_at`),
            CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql);
    },
    
    'down' => function($pdo) {
        $pdo->exec("DROP TABLE IF EXISTS `users`;");
    }
];
