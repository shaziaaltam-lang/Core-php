<?php

namespace App\Contracts;

/**
 * AuthInterface - واجهة نظام المصادقة والتحقق
 * 
 * تحتوي على جميع العمليات المتعلقة بالتسجيل والدخول والتحقق الثنائي
 * 
 * @package App\Contracts
 * @version 7.0.0
 * @author ZLA Team
 */
interface AuthInterface
{
    /**
     * تسجيل مستخدم جديد
     * 
     * @param array{email: string, password: string, full_name: string, phone?: string} $data
     * @return array{success: bool, user_id?: int, token?: string, message?: string, errors?: array}
     * @throws \Exception
     */
    public function register(array $data): array;

    /**
     * تسجيل الدخول
     * 
     * @param string $email البريد الإلكتروني
     * @param string $password كلمة المرور
     * @param bool $remember تذكرني (اختياري)
     * @return array{success: bool, user_id?: int, token?: string, requires_2fa?: bool, message?: string, error?: string}
     * @throws \Exception
     */
    public function login(string $email, string $password, bool $remember = false): array;

    /**
     * التحقق من المصادقة الثنائية
     * 
     * @param int $user_id معرف المستخدم
     * @param string $code رمز التحقق
     * @return array{success: bool, token?: string, message?: string, error?: string}
     * @throws \Exception
     */
    public function verify2FA(int $user_id, string $code): array;

    /**
     * تسجيل الخروج
     * 
     * @param int $user_id معرف المستخدم
     * @param string|null $token التوكن (اختياري)
     * @return array{success: bool, message: string, error?: string}
     * @throws \Exception
     */
    public function logout(int $user_id, ?string $token = null): array;

    /**
     * التحقق من صحة التوكن
     * 
     * @param string $token التوكن
     * @return array{valid: bool, user_id?: int, error?: string}
     * @throws \Exception
     */
    public function verifyToken(string $token): array;

    /**
     * طلب إعادة تعيين كلمة المرور
     * 
     * @param string $email البريد الإلكتروني
     * @return array{success: bool, message: string, error?: string}
     * @throws \Exception
     */
    public function forgotPassword(string $email): array;

    /**
     * إعادة تعيين كلمة المرور
     * 
     * @param string $token توكن إعادة التعيين
     * @param string $password كلمة المرور الجديدة
     * @return array{success: bool, message: string, error?: string}
     * @throws \Exception
     */
    public function resetPassword(string $token, string $password): array;

    /**
     * تغيير كلمة المرور
     * 
     * @param int $user_id معرف المستخدم
     * @param string $old_password كلمة المرور القديمة
     * @param string $new_password كلمة المرور الجديدة
     * @return array{success: bool, message: string, error?: string}
     * @throws \Exception
     */
    public function changePassword(int $user_id, string $old_password, string $new_password): array;

    /**
     * تفعيل المصادقة الثنائية
     * 
     * @param int $user_id معرف المستخدم
     * @return array{success: bool, secret?: string, qrCode?: string, message?: string, error?: string}
     * @throws \Exception
     */
    public function enable2FA(int $user_id): array;

    /**
     * تعطيل المصادقة الثنائية
     * 
     * @param int $user_id معرف المستخدم
     * @param string $code رمز التحقق
     * @return array{success: bool, message: string, error?: string}
     * @throws \Exception
     */
    public function disable2FA(int $user_id, string $code): array;

    /**
     * التحقق من البريد الإلكتروني
     * 
     * @param int $user_id معرف المستخدم
     * @param string $code رمز التحقق
     * @return array{success: bool, message: string, error?: string}
     * @throws \Exception
     */
    public function verifyEmail(int $user_id, string $code): array;

    /**
     * إرسال رمز التحقق من البريد الإلكتروني
     * 
     * @param int $user_id معرف المستخدم
     * @return array{success: bool, message: string, error?: string}
     * @throws \Exception
     */
    public function sendEmailVerification(int $user_id): array;

    /**
     * الحصول على معلومات المستخدم الحالي
     * 
     * @param string $token التوكن
     * @return array{success: bool, user?: array, error?: string}
     * @throws \Exception
     */
    public function getCurrentUser(string $token): array;

    /**
     * تحديث ملف تعريف المستخدم
     * 
     * @param int $user_id معرف المستخدم
     * @param array $data البيانات المراد تحديثها
     * @return array{success: bool, user?: array, message?: string, error?: string}
     * @throws \Exception
     */
    public function updateProfile(int $user_id, array $data): array;

    /**
     * قفل الحساب مؤقتاً بعد محاولات فاشلة
     * 
     * @param string $email البريد الإلكتروني
     * @return array{success: bool, locked?: bool, lockout_minutes?: int, error?: string}
     * @throws \Exception
     */
    public function checkLoginAttempts(string $email): array;
}
