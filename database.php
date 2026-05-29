<?php
/**
 * File: core/Database.php
 * Core Database Class - PDO Connection Manager
 * @version 1.0.0
 * @author ZLA System
 * @description إدارة اتصال قاعدة البيانات باستخدام نمط Singleton
 */

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    private function __construct() {}

    public static function getInstance(array $config = []): PDO
    {
        if (self::$instance === null) {
            self::$config = array_merge([
                'host' => DB_HOST,
                'dbname' => DB_NAME,
                'user' => DB_USER,
                'pass' => DB_PASS,
                'charset' => 'utf8mb4'
            ], $config);
            self::$instance = self::connect();
        }
        return self::$instance;
    }

    private static function connect(): PDO
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', 
            self::$config['host'], 
            self::$config['dbname'], 
            self::$config['charset']
        );
        
        try {
            $pdo = new PDO($dsn, self::$config['user'], self::$config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 30
            ]);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database Connection Failed: " . $e->getMessage());
            throw new \RuntimeException('Database connection failed');
        }
    }

    public static function transaction(callable $callback)
    {
        self::$instance->beginTransaction();
        try {
            $result = $callback(self::$instance);
            self::$instance->commit();
            return $result;
        } catch (\Throwable $e) {
            self::$instance->rollBack();
            throw $e;
        }
    }

    public static function disconnect(): void
    {
        self::$instance = null;
    }

    public static function getConfig(): array
    {
        return self::$config;
    }
}
