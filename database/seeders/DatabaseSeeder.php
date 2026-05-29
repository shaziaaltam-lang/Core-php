<?php

/**
 * Database Seeder: Insert Initial Data
 * Layer 1: Database & Migrations
 * Version: 7.1.0
 */

return [
    'seed' => function($pdo) {
        // Insert Roles
        $pdo->exec("
        INSERT INTO `roles` (`role_name`, `display_name`, `dashboard_url`, `description`, `priority`, `is_active`) VALUES
        ('super_admin', 'المدير العام', '/super-admin/dashboard', 'صلاحية كاملة - لوحة تحكم المدير العام', 1000, 1),
        ('admin', 'مدير النظام', '/admin/dashboard', 'إدارة النظام والمستخدمين', 100, 1),
        ('manager', 'مدير', '/manager/dashboard', 'لوحة تحكم المديرين', 50, 1),
        ('editor', 'محرر', '/editor/dashboard', 'لوحة تحكم المحررين', 30, 1),
        ('accountant', 'محاسب', '/accountant/dashboard', 'لوحة تحكم الحسابات', 40, 1),
        ('support', 'دعم فني', '/support/dashboard', 'لوحة تحكم فريق الدعم', 35, 1),
        ('user', 'مستخدم', '/user/dashboard', 'لوحة تحكم المستخدم العادي', 10, 1);
        ");

        // Insert System Settings
        $pdo->exec("
        INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`, `is_encrypted`, `description`, `is_public`) VALUES
        ('app_name', 'ZLA Dynamic Auth System', 'general', 'string', 0, 'اسم التطبيق', 1),
        ('app_locale', 'ar', 'general', 'string', 0, 'اللغة الافتراضية', 1),
        ('app_timezone', 'Asia/Riyadh', 'general', 'string', 0, 'المنطقة الزمنية', 1),
        ('session_lifetime', '7200', 'security', 'integer', 0, 'مدة صلاحية الجلسة بالثواني', 0),
        ('max_login_attempts', '5', 'security', 'integer', 0, 'الحد الأقصى لمحاولات الدخول الفاشلة', 0),
        ('login_lockout_minutes', '15', 'security', 'integer', 0, 'مدة الحظر بعد المحاولات الفاشلة', 0),
        ('2fa_enforced', '0', 'security', 'boolean', 0, 'تفعيل المصادقة الثنائية إجبارياً', 0),
        ('password_min_length', '8', 'security', 'integer', 0, 'الحد الأدنى لطول كلمة المرور', 0),
        ('smtp_host', '', 'mail', 'string', 0, 'خادم SMTP', 0),
        ('smtp_port', '587', 'mail', 'integer', 0, 'منفذ SMTP', 0),
        ('smtp_encryption', 'tls', 'mail', 'string', 0, 'نوع التشفير', 0),
        ('whatsapp_api_key', '', 'whatsapp', 'encrypted', 1, 'مفتاح API للواتساب', 0),
        ('sms_gateway_token', '', 'sms', 'encrypted', 1, 'توكن بوابة SMS', 0),
        ('maintenance_mode', '0', 'system', 'boolean', 0, 'وضع الصيانة', 1),
        ('registration_enabled', '1', 'system', 'boolean', 0, 'تفعيل التسجيل الجديد', 1),
        ('email_verification_required', '1', 'system', 'boolean', 0, 'طلب تأكيد البريد الإلكتروني', 1);
        ");

        // Insert Notification Templates
        $pdo->exec("
        INSERT INTO `notification_templates` (`template_key`, `title_ar`, `title_en`, `body_ar`, `body_en`, `channels`, `variables`, `is_active`) VALUES
        ('welcome_email', 'مرحباً بك في {{app_name}}', 'Welcome to {{app_name}}', 'مرحباً {{user_name}},\\nنرحب بانضمامك إلى منصتنا. يرجى تأكيد بريدك الإلكتروني من خلال الرابط التالي:\\n{{verification_link}}', 'Hello {{user_name}},\\nWelcome to our platform. Please verify your email using the link below:\\n{{verification_link}}', 'db,mail', '[\"user_name\", \"app_name\", \"verification_link\"]', 1),
        ('login_alert', 'تنبيه: تسجيل دخول جديد', 'Alert: New Login Detected', 'تم تسجيل دخول جديد إلى حسابك.\\n\\n📍 IP: {{ip_address}}\\n🖥️ الجهاز: {{user_agent}}\\n🕐 الوقت: {{login_time}}\\n\\nإذا لم تكن أنت، يرجى تغيير كلمة المرور فوراً.', 'A new login was detected on your account.\\n\\n📍 IP: {{ip_address}}\\n🖥️ Device: {{user_agent}}\\n🕐 Time: {{login_time}}\\n\\nIf this wasn\\'t you, please change your password immediately.', 'db,mail,whatsapp', '[\"ip_address\", \"user_agent\", \"login_time\"]', 1),
        ('password_changed', 'تم تغيير كلمة المرور', 'Password Changed', 'تم تغيير كلمة المرور الخاصة بحسابك بنجاح.\\n\\nإذا لم تقم بهذا التغيير، يرجى الاتصال بالدعم فوراً.', 'Your account password has been changed successfully.\\n\\nIf you did not make this change, please contact support immediately.', 'db,mail', '[]', 1);
        ");
    },
    
    'rollback' => function($pdo) {
        // Truncate tables (don't delete, just clear)
        $pdo->exec("TRUNCATE TABLE `notification_templates`;");
        $pdo->exec("TRUNCATE TABLE `system_settings`;");
        $pdo->exec("TRUNCATE TABLE `roles`;");
    }
];
