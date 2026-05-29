<?php

namespace App\Contracts;

/**
 * UserInterface - واجهة إدارة المستخدمين
 * 
 * تحتوي على جميع العمليات المتعلقة بإدارة بيانات المستخدمين
 * 
 * @package App\Contracts
 * @version 7.0.0
 * @author ZLA Team
 */
interface UserInterface extends BaseInterface
{
    /**
     * الحصول على مستخدم بناءً على البريد الإلكتروني
     * 
     * @param string $email البريد الإلكتروني
     * @return array{success: bool, data?: array, error?: string}
     * @throws \Exception
     */
    public function getByEmail(string $email): array;

    /**
     * الحصول على مستخدم بناءً على UUID
     * 
     * @param string $uuid المعرف الفريد العام
     * @return array{success: bool, data?: array, error?: string}
     * @throws \Exception
     */
    public function getByUuid(string $uuid): array;

    /**
     * الحصول على مستخدمي دور معين
     * 
     * @param int $role_id معرف الدور
     * @param int $page رقم الصفحة
     * @param int $per_page عدد العناصر في الصفحة
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function getByRole(int $role_id, int $page = 1, int $per_page = 15): array;

    /**
     * تغيير حالة المستخدم
     * 
     * @param int $user_id معرف المستخدم
     * @param string $status الحالة الجديدة (active, pending, suspended, banned)
     * @return array{success: bool, message?: string, error?: string}
     * @throws \Exception
     */
    public function changeStatus(int $user_id, string $status): array;

    /**
     * تغيير دور المستخدم
     * 
     * @param int $user_id معرف المستخدم
     * @param int $role_id معرف الدور الجديد
     * @return array{success: bool, message?: string, error?: string}
     * @throws \Exception
     */
    public function changeRole(int $user_id, int $role_id): array;

    /**
     * الحصول على معلومات المستخدم الكاملة
     * 
     * @param int $user_id معرف المستخدم
     * @return array{success: bool, data?: array, error?: string}
     * @throws \Exception
     */
    public function getFullProfile(int $user_id): array;

    /**
     * تحديث صورة المستخدم
     * 
     * @param int $user_id معرف المستخدم
     * @param string $avatar_path مسار الصورة
     * @return array{success: bool, avatar_url?: string, message?: string, error?: string}
     * @throws \Exception
     */
    public function updateAvatar(int $user_id, string $avatar_path): array;

    /**
     * التحقق من وجود بريد إلكتروني
     * 
     * @param string $email البريد الإلكتروني
     * @param int|null $exclude_user_id معرف المستخدم للاستثناء
     * @return bool
     * @throws \Exception
     */
    public function emailExists(string $email, ?int $exclude_user_id = null): bool;

    /**
     * الحصول على سجل تسجيل الدخول
     * 
     * @param int $user_id معرف المستخدم
     * @param int $limit عدد السجلات
     * @return array{success: bool, data: array, error?: string}
     * @throws \Exception
     */
    public function getLoginHistory(int $user_id, int $limit = 10): array;

    /**
     * حذف حساب المستخدم نهائياً
     * 
     * @param int $user_id معرف المستخدم
     * @param string $password كلمة المرور للتأكيد
     * @return array{success: bool, message?: string, error?: string}
     * @throws \Exception
     */
    public function deleteAccount(int $user_id, string $password): array;

    /**
     * حذف حساب المستخدم مؤقتاً (soft delete)
     * 
     * @param int $user_id معرف المستخدم
     * @return array{success: bool, message?: string, error?: string}
     * @throws \Exception
     */
    public function softDelete(int $user_id): array;

    /**
     * استعادة حساب محذوف مؤقتاً
     * 
     * @param int $user_id معرف المستخدم
     * @return array{success: bool, message?: string, error?: string}
     * @throws \Exception
     */
    public function restore(int $user_id): array;

    /**
     * الحصول على إحصائيات المستخدمين
     * 
     * @return array{total: int, active: int, pending: int, suspended: int, banned: int}
     * @throws \Exception
     */
    public function getStatistics(): array;

    /**
     * البحث عن المستخدمين
     * 
     * @param string $keyword كلمة البحث
     * @param int $page رقم الصفحة
     * @param int $per_page عدد العناصر في الصفحة
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function searchUsers(string $keyword, int $page = 1, int $per_page = 15): array;

    /**
     * تصدير قائمة المستخدمين
     * 
     * @param array $filters المرشحات
     * @param string $format صيغة التصدير (csv, json, excel)
     * @return array{success: bool, data?: string, error?: string}
     * @throws \Exception
     */
    public function export(array $filters, string $format = 'csv'): array;

    /**
     * استيراد المستخدمين من ملف
     * 
     * @param string $file_path مسار الملف
     * @param string $format صيغة الملف (csv, json, excel)
     * @return array{success: bool, imported: int, failed: int, errors?: array}
     * @throws \Exception
     */
    public function import(string $file_path, string $format = 'csv'): array;
}
