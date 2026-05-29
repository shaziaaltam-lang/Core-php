<?php

namespace App\Contracts;

/**
 * AuditInterface - واجهة نظام التدقيق والسجلات
 * 
 * تحتوي على جميع العمليات المتعلقة بتتبع وتدقيق جميع العمليات
 * 
 * @package App\Contracts
 * @version 7.0.0
 * @author ZLA Team
 */
interface AuditInterface extends BaseInterface
{
    /**
     * تسجيل عملية تدقيق
     * 
     * @param int|null $user_id معرف المستخدم
     * @param string $action اسم الإجراء
     * @param string|null $entity_type نوع الكيان
     * @param string|null $entity_id معرف الكيان
     * @param array|null $old_values القيم القديمة
     * @param array|null $new_values القيم الجديدة
     * @param array $metadata بيانات إضافية
     * @return array{success: bool, log_id?: int, error?: string}
     * @throws \Exception
     */
    public function log(?int $user_id, string $action, ?string $entity_type = null, ?string $entity_id = null, ?array $old_values = null, ?array $new_values = null, array $metadata = []): array;

    /**
     * الحصول على سجلات التدقيق لمستخدم معين
     * 
     * @param int $user_id معرف المستخدم
     * @param int $page رقم الصفحة
     * @param int $per_page عدد العناصر في الصفحة
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function getUserLogs(int $user_id, int $page = 1, int $per_page = 20): array;

    /**
     * الحصول على سجلات التدقيق لكيان معين
     * 
     * @param string $entity_type نوع الكيان
     * @param string $entity_id معرف الكيان
     * @return array{success: bool, data: array, error?: string}
     * @throws \Exception
     */
    public function getEntityLogs(string $entity_type, string $entity_id): array;

    /**
     * البحث في سجلات التدقيق
     * 
     * @param array $criteria معايير البحث
     * @param int $page رقم الصفحة
     * @param int $per_page عدد العناصر في الصفحة
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function search(array $criteria, int $page = 1, int $per_page = 20): array;

    /**
     * الحصول على سجلات التدقيق حسب الإجراء
     * 
     * @param string $action اسم الإجراء
     * @param int $page رقم الصفحة
     * @param int $per_page عدد العناصر في الصفحة
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function getByAction(string $action, int $page = 1, int $per_page = 20): array;

    /**
     * الحصول على سجلات التدقيق حسب عنوان IP
     * 
     * @param string $ip_address عنوان IP
     * @param int $page رقم الصفحة
     * @param int $per_page عدد العناصر في الصفحة
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function getByIpAddress(string $ip_address, int $page = 1, int $per_page = 20): array;

    /**
     * الحصول على سجلات التدقيق حسب نطاق زمني
     * 
     * @param string $start_date تاريخ البداية
     * @param string $end_date تاريخ النهاية
     * @param int $page رقم الصفحة
     * @param int $per_page عدد العناصر في الصفحة
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function getByDateRange(string $start_date, string $end_date, int $page = 1, int $per_page = 20): array;

    /**
     * حذف سجلات التدقيق القديمة
     * 
     * @param int $days_old حذف السجلات الأقدم من عدد الأيام المحدد
     * @return array{success: bool, deleted: int, error?: string}
     * @throws \Exception
     */
    public function deleteOldLogs(int $days_old = 90): array;

    /**
     * الحصول على إحصائيات التدقيق
     * 
     * @return array{success: bool, data: array, error?: string}
     * @throws \Exception
     */
    public function getStatistics(): array;

    /**
     * تصدير سجلات التدقيق
     * 
     * @param array $filters المرشحات
     * @param string $format صيغة التصدير (csv, json, excel, pdf)
     * @return array{success: bool, data?: string, error?: string}
     * @throws \Exception
     */
    public function export(array $filters, string $format = 'csv'): array;

    /**
     * الحصول على الأنشطة الحديثة
     * 
     * @param int $limit عدد الأنشطة المطلوبة
     * @return array{success: bool, data: array, error?: string}
     * @throws \Exception
     */
    public function getRecentActivities(int $limit = 50): array;

    /**
     * الحصول على التقرير الشامل للتدقيق
     * 
     * @param string $start_date تاريخ البداية
     * @param string $end_date تاريخ النهاية
     * @return array{success: bool, data: array, error?: string}
     * @throws \Exception
     */
    public function getComprehensiveReport(string $start_date, string $end_date): array;

    /**
     * الحصول على قائمة الأنواع الحساسة للتدقيق
     * 
     * @return array
     */
    public function getSensitiveActionTypes(): array;

    /**
     * تنبيه على أنشطة مريبة
     * 
     * @param array $criteria معايير الكشف
     * @return array{success: bool, alerts: array, error?: string}
     * @throws \Exception
     */
    public function detectSuspiciousActivity(array $criteria): array;
}
