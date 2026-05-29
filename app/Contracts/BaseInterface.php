<?php

namespace App\Contracts;

/**
 * BaseInterface - العقد الأساسي لجميع الواجهات
 * 
 * يحتوي على الدوال الأساسية التي يجب أن تتواجد في جميع الواجهات
 * 
 * @package App\Contracts
 * @version 7.0.0
 * @author ZLA Team
 */
interface BaseInterface
{
    /**
     * إنشاء سجل جديد
     * 
     * @param array $data البيانات المراد إنشاء السجل بها
     * @return array{success: bool, data?: array, error?: string}
     * @throws \Exception
     */
    public function create(array $data): array;

    /**
     * قراءة سجل بناءً على المعرف
     * 
     * @param int|string $id معرف السجل
     * @return array{success: bool, data?: array, error?: string}
     * @throws \Exception
     */
    public function read(int|string $id): array;

    /**
     * تحديث سجل موجود
     * 
     * @param int|string $id معرف السجل
     * @param array $data البيانات الجديدة
     * @return array{success: bool, data?: array, error?: string}
     * @throws \Exception
     */
    public function update(int|string $id, array $data): array;

    /**
     * حذف سجل
     * 
     * @param int|string $id معرف السجل
     * @return array{success: bool, message?: string, error?: string}
     * @throws \Exception
     */
    public function delete(int|string $id): array;

    /**
     * الحصول على جميع السجلات بصيغة مرقمة
     * 
     * @param int $page رقم الصفحة (افتراضي: 1)
     * @param int $per_page عدد العناصر في الصفحة (افتراضي: 15)
     * @param array $filters مرشحات البحث
     * @return array{success: bool, data: array, pagination: array, error?: string}
     * @throws \Exception
     */
    public function paginate(int $page = 1, int $per_page = 15, array $filters = []): array;

    /**
     * البحث عن السجلات
     * 
     * @param array $criteria معايير البحث
     * @return array{success: bool, data: array, count: int, error?: string}
     * @throws \Exception
     */
    public function search(array $criteria): array;

    /**
     * التحقق من وجود سجل
     * 
     * @param array $conditions الشروط
     * @return bool
     * @throws \Exception
     */
    public function exists(array $conditions): bool;

    /**
     * عد عدد السجلات
     * 
     * @param array $conditions الشروط (اختياري)
     * @return int
     * @throws \Exception
     */
    public function count(array $conditions = []): int;

    /**
     * الحصول على آخر خطأ
     * 
     * @return string|null
     */
    public function getLastError(): ?string;

    /**
     * تعيين معرف المستخدم الحالي
     * 
     * @param int|null $userId
     * @return void
     */
    public function setCurrentUser(?int $userId): void;
}
