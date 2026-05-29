<?php

/**
 * Migration: Create Audit, Settings, API Tokens, and Infrastructure Tables
 * Layer 1: Database & Migrations
 * Version: 7.1.0
 */

return [
    'up' => function($pdo) {
        // Audit Logs Table
        $pdo->exec("
        DROP TABLE IF EXISTS `audit_logs`;
        CREATE TABLE `audit_logs` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED NULL,
            `action` VARCHAR(100) NOT NULL,
            `entity_type` VARCHAR(50) NULL,
            `entity_id` VARCHAR(255) NULL,
            `old_values` JSON NULL,
            `new_values` JSON NULL,
            `metadata` JSON NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` TEXT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_user_action` (`user_id`, `action`),
            INDEX `idx_entity` (`entity_type`, `entity_id`),
            INDEX `idx_created_at` (`created_at`),
            INDEX `idx_ip_address` (`ip_address`),
            INDEX `idx_audit_composite` (`created_at`, `action`, `user_id`),
            FULLTEXT INDEX `ft_audit_search` (`action`, `metadata`),
            CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // System Settings Table
        $pdo->exec("
        DROP TABLE IF EXISTS `system_settings`;
        CREATE TABLE `system_settings` (
            `setting_key` VARCHAR(100) NOT NULL,
            `setting_value` TEXT NOT NULL,
            `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
            `setting_type` ENUM('string', 'integer', 'boolean', 'json', 'encrypted') NOT NULL DEFAULT 'string',
            `is_encrypted` TINYINT(1) NOT NULL DEFAULT 0,
            `description` VARCHAR(255) NULL,
            `is_public` TINYINT(1) NOT NULL DEFAULT 0,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`setting_key`),
            INDEX `idx_group` (`setting_group`),
            INDEX `idx_public` (`is_public`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // User Preferences Table
        $pdo->exec("
        DROP TABLE IF EXISTS `user_preferences`;
        CREATE TABLE `user_preferences` (
            `user_id` BIGINT UNSIGNED NOT NULL,
            `preference_key` VARCHAR(100) NOT NULL,
            `preference_value` JSON NOT NULL,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`user_id`, `preference_key`),
            CONSTRAINT `fk_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // API Tokens Table
        $pdo->exec("
        DROP TABLE IF EXISTS `api_tokens`;
        CREATE TABLE `api_tokens` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `token` VARCHAR(64) NOT NULL UNIQUE,
            `last_used_at` TIMESTAMP NULL DEFAULT NULL,
            `expires_at` TIMESTAMP NULL DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_token` (`token`),
            INDEX `idx_user_tokens` (`user_id`),
            INDEX `idx_expires` (`expires_at`),
            CONSTRAINT `fk_api_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Jobs Queue Table
        $pdo->exec("
        DROP TABLE IF EXISTS `jobs`;
        CREATE TABLE `jobs` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `queue` VARCHAR(100) NOT NULL DEFAULT 'default',
            `payload` LONGTEXT NOT NULL,
            `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `reserved_at` INT UNSIGNED NULL,
            `available_at` INT UNSIGNED NOT NULL,
            `created_at` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_queue_reserved` (`queue`, `reserved_at`),
            INDEX `idx_available` (`available_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Failed Jobs Table
        $pdo->exec("
        DROP TABLE IF EXISTS `failed_jobs`;
        CREATE TABLE `failed_jobs` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `uuid` CHAR(36) NOT NULL,
            `connection` TEXT NOT NULL,
            `queue` TEXT NOT NULL,
            `payload` LONGTEXT NOT NULL,
            `exception` LONGTEXT NOT NULL,
            `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `idx_uuid` (`uuid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Cache Table
        $pdo->exec("
        DROP TABLE IF EXISTS `cache`;
        CREATE TABLE `cache` (
            `key` VARCHAR(255) NOT NULL,
            `value` MEDIUMTEXT NOT NULL,
            `expiration` INT UNSIGNED NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`key`),
            INDEX `idx_expiration` (`expiration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    },
    
    'down' => function($pdo) {
        $pdo->exec("DROP TABLE IF EXISTS `cache`;");
        $pdo->exec("DROP TABLE IF EXISTS `failed_jobs`;");
        $pdo->exec("DROP TABLE IF EXISTS `jobs`;");
        $pdo->exec("DROP TABLE IF EXISTS `api_tokens`;");
        $pdo->exec("DROP TABLE IF EXISTS `user_preferences`;");
        $pdo->exec("DROP TABLE IF EXISTS `system_settings`;");
        $pdo->exec("DROP TABLE IF EXISTS `audit_logs`;");
    }
];
