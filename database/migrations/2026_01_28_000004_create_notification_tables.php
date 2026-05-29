<?php

/**
 * Migration: Create Notification Tables
 * Layer 1: Database & Migrations
 * Version: 7.1.0
 */

return [
    'up' => function($pdo) {
        // Notifications Table
        $pdo->exec("
        DROP TABLE IF EXISTS `notifications`;
        CREATE TABLE `notifications` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `notification_uuid` CHAR(36) NOT NULL,
            `channels` SET('db', 'mail', 'whatsapp', 'sms', 'push') NOT NULL DEFAULT 'db',
            `type` VARCHAR(50) NOT NULL,
            `priority` ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal',
            `title` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `data` JSON NULL,
            `is_read` TINYINT(1) NOT NULL DEFAULT 0,
            `read_at` TIMESTAMP NULL DEFAULT NULL,
            `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `delivery_status` JSON NULL,
            `retry_count` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `idx_uuid` (`notification_uuid`),
            INDEX `idx_user_unread` (`user_id`, `is_read`, `sent_at`),
            INDEX `idx_user_priority` (`user_id`, `priority`, `sent_at`),
            INDEX `idx_type` (`type`),
            INDEX `idx_sent_at` (`sent_at`),
            INDEX `idx_notifications_composite` (`user_id`, `is_read`, `priority`, `sent_at`),
            FULLTEXT INDEX `ft_notification_search` (`title`, `message`),
            CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Notification Templates Table
        $pdo->exec("
        DROP TABLE IF EXISTS `notification_templates`;
        CREATE TABLE `notification_templates` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `template_key` VARCHAR(100) NOT NULL UNIQUE,
            `title_ar` VARCHAR(255) NOT NULL,
            `title_en` VARCHAR(255) NULL,
            `body_ar` TEXT NOT NULL,
            `body_en` TEXT NULL,
            `channels` SET('db', 'mail', 'whatsapp', 'sms', 'push') NOT NULL DEFAULT 'db',
            `variables` JSON NULL,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_active` (`is_active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    },
    
    'down' => function($pdo) {
        $pdo->exec("DROP TABLE IF EXISTS `notification_templates`;");
        $pdo->exec("DROP TABLE IF EXISTS `notifications`;");
    }
];
