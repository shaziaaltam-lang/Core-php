<?php

namespace App\Core;

/**
 * Database Class - اتصال قاعدة البيانات
 * 
 * يدير اتصال قاعدة البيانات باستخدام PDO مع Prepared Statements
 * 
 * @package App\Core
 * @version 7.0.0
 */
class Database
{
    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @var array معلومات الاتصال
     */
    protected static $config = [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'zla_auth_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ];

    /**
     * الحصول على اتصال PDO
     * 
     * @param array $config إعدادات الاتصال (اختياري)
     * @return \PDO
     * @throws \PDOException
     */
    public static function connection(array $config = []): \PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = array_merge(self::$config, $config);

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

        try {
            self::$pdo = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']} COLLATE {$config['collation']}",
            ]);

            return self::$pdo;
        } catch (\PDOException $e) {
            throw new \PDOException("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    /**
     * تنفيذ استعلام SELECT
     * 
     * @param string $query الاستعلام
     * @param array $bindings المعاملات المرتبطة
     * @return array
     * @throws \PDOException
     */
    public static function select(string $query, array $bindings = []): array
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    /**
     * تنفيذ استعلام SELECT بنتيجة واحدة
     * 
     * @param string $query الاستعلام
     * @param array $bindings المعاملات المرتبطة
     * @return array|null
     * @throws \PDOException
     */
    public static function selectOne(string $query, array $bindings = []): ?array
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($bindings);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * تنفيذ استعلام INSERT
     * 
     * @param string $query الاستعلام
     * @param array $bindings المعاملات المرتبطة
     * @return int معرف الصف المُدرج
     * @throws \PDOException
     */
    public static function insert(string $query, array $bindings = []): int
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($bindings);
        return (int) self::connection()->lastInsertId();
    }

    /**
     * تنفيذ استعلام UPDATE
     * 
     * @param string $query الاستعلام
     * @param array $bindings المعاملات المرتبطة
     * @return int عدد الصفوف المتأثرة
     * @throws \PDOException
     */
    public static function update(string $query, array $bindings = []): int
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    /**
     * تنفيذ استعلام DELETE
     * 
     * @param string $query الاستعلام
     * @param array $bindings المعاملات المرتبطة
     * @return int عدد الصفوف المحذوفة
     * @throws \PDOException
     */
    public static function delete(string $query, array $bindings = []): int
    {
        $stmt = self::connection()->prepare($query);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    /**
     * بدء معاملة (Transaction)
     * 
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return self::connection()->beginTransaction();
    }

    /**
     * التزام المعاملة
     * 
     * @return bool
     */
    public static function commit(): bool
    {
        return self::connection()->commit();
    }

    /**
     * إرجاع المعاملة
     * 
     * @return bool
     */
    public static function rollback(): bool
    {
        return self::connection()->rollBack();
    }

    /**
     * التحقق من وجود جدول
     * 
     * @param string $table اسم الجدول
     * @return bool
     */
    public static function tableExists(string $table): bool
    {
        $query = "SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
        return self::selectOne($query, [$table]) !== null;
    }

    /**
     * حذف اتصال PDO
     * 
     * @return void
     */
    public static function disconnect(): void
    {
        self::$pdo = null;
    }

    /**
     * تعيين إعدادات الاتصال
     * 
     * @param array $config
     * @return void
     */
    public static function setConfig(array $config): void
    {
        self::$config = array_merge(self::$config, $config);
    }
}
