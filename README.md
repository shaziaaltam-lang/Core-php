# Core-php
📁 الملف الأول: core/Database.php

```php
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
```

---

📖 شرح مفصل لملف Database.php

الغرض من الملف

هذا الملف هو المسؤول عن إدارة اتصال قاعدة البيانات في النظام. يستخدم نمط Singleton لضمان وجود اتصال واحد فقط طوال عمر التطبيق.

الوظائف الرئيسية

الدالة الوصف مثال الاستخدام
getInstance() الحصول على اتصال PDO (ينشئه إذا لم يكن موجوداً) $db = Database::getInstance();
connect() إنشاء اتصال جديد بقاعدة البيانات يتم استدعاؤها تلقائياً
transaction() تنفيذ عمليات متعددة داخل معاملة واحدة Database::transaction(function($db){...});
disconnect() إغلاق اتصال قاعدة البيانات Database::disconnect();
getConfig() الحصول على إعدادات الاتصال الحالية $config = Database::getConfig();

الميزات الأمنية

1. Prepared Statements - محمية تلقائياً عبر PDO
2. Exception Mode - يرمي استثناءات عند الأخطاء
3. No Emulated Prepares - يمنع هجمات SQL Injection
4. UTF8MB4 - يدعم الرموز التعبيرية (Emoji) والعربية

مثال الاستخدام

```php
// الحصول على اتصال قاعدة البيانات
$db = Database::getInstance();

// تنفيذ استعلام عادي
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => 1]);
$user = $stmt->fetch();

// استخدام المعاملات (Transactions)
Database::transaction(function($db) {
    $db->prepare("UPDATE users SET balance = balance - 100 WHERE id = 1")->execute();
    $db->prepare("UPDATE users SET balance = balance + 100 WHERE id = 2")->execute();
});
```

الاعتماديات (Dependencies)

· DB_HOST, DB_NAME, DB_USER, DB_PASS - ثوابت من config.php
· PDO - ملحق PHP مدمج

الأخطاء المحتملة وحلها

الخطأ السبب الحل
Database connection failed إعدادات قاعدة البيانات خاطئة تأكد من DB_HOST, DB_USER, DB_PASS في config.php
Connection refused MySQL غير شغال شغل خدمة MySQL
Unknown database قاعدة البيانات غير موجودة أنشئ قاعدة البيانات أولاً

