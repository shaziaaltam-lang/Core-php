<?php
/**
 * File: core/Auth.php
 * Core Authentication Class
 * @version 1.0.0
 * @author ZLA System
 * @description إدارة المصادقة (تسجيل الدخول، تسجيل الخروج، التحقق)
 */

namespace Core;

use PDO;

class Auth
{
    private PDO $db;
    private Session $session;
    private Logger $logger;
    private RateLimiter $rateLimiter;

    public function __construct(PDO $db, Session $session, Logger $logger)
    {
        $this->db = $db;
        $this->session = $session;
        $this->logger = $logger;
        $this->rateLimiter = new RateLimiter(new CacheManager());
    }

    public function login(string $email, string $password, string $ip = ''): array
    {
        $ip = $ip ?: ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        
        if (!$this->rateLimiter->attempt("login_{$ip}", 5, 15)) {
            return ['success' => false, 'message' => 'لقد تجاوزت عدد المحاولات المسموحة. يرجى المحاولة بعد 15 دقيقة.'];
        }
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND deleted_at IS NULL");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $this->logFailedAttempt($email, $ip, 'User not found');
            return ['success' => false, 'message' => 'البريد الإلكتروني غير موجود'];
        }
        
        if ($user['status'] !== 'active') {
            $this->logFailedAttempt($email, $ip, 'Account not active');
            return ['success' => false, 'message' => 'الحساب غير نشط. يرجى تفعيل حسابك أولاً.'];
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            $this->logFailedAttempt($email, $ip, 'Invalid password');
            return ['success' => false, 'message' => 'كلمة المرور غير صحيحة'];
        }
        
        if (password_needs_rehash($user['password_hash'], PASSWORD_ALGO, ['cost' => PASSWORD_COST])) {
            $newHash = password_hash($password, PASSWORD_ALGO, ['cost' => PASSWORD_COST]);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
            $stmt->execute(['hash' => $newHash, 'id' => $user['id']]);
        }
        
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW(), last_login_ip = :ip WHERE id = :id");
        $stmt->execute(['ip' => $ip, 'id' => $user['id']]);
        
        $this->clearFailedAttempts($email);
        $this->session->setUser($user['id'], $user['email'], $user['full_name'], $user['role_id']);
        
        $this->logger->info("User logged in", ['user_id' => $user['id'], 'email' => $email, 'ip' => $ip]);
        
        return ['success' => true, 'message' => 'تم تسجيل الدخول بنجاح'];
    }

    public function logout(): void
    {
        $userId = $this->session->getUserId();
        $this->logger->info("User logged out", ['user_id' => $userId]);
        $this->session->destroy();
    }

    public function isLoggedIn(): bool
    {
        return $this->session->isLoggedIn();
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) return null;
        
        $stmt = $this->db->prepare("SELECT id, email, full_name, role_id, status FROM users WHERE id = :id");
        $stmt->execute(['id' => $this->session->getUserId()]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCurrentUserId(): ?int
    {
        return $this->session->getUserId();
    }

    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            redirect('/pages/login/');
        }
    }

    private function logFailedAttempt(string $email, string $ip, string $reason): void
    {
        $stmt = $this->db->prepare("INSERT INTO login_attempts (email, ip_address, was_successful, failure_reason) VALUES (:email, :ip, 0, :reason)");
        $stmt->execute(['email' => $email, 'ip' => $ip, 'reason' => $reason]);
        $this->logger->warning("Failed login attempt", ['email' => $email, 'ip' => $ip, 'reason' => $reason]);
    }

    private function clearFailedAttempts(string $email): void
    {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE email = :email");
        $stmt->execute(['email' => $email]);
    }
}
