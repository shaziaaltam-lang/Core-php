<?php
/**
 * File: core/Session.php
 * Core Session Management Class
 * @version 1.0.0
 * @author ZLA System
 * @description إدارة جلسات المستخدمين بشكل آمن مع بصمة الجهاز
 */

namespace Core;

class Session
{
    private string $prefix;
    private string $fingerprint;
    private int $lifetime;

    public function __construct(string $prefix = 'zla_', int $lifetime = 7200)
    {
        $this->prefix = $prefix;
        $this->lifetime = $lifetime;
        $this->fingerprint = $this->generateFingerprint();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME ?? 'zla_session');
            session_start();
        }
        
        $this->validate();
        $this->checkExpiry();
    }

    private function generateFingerprint(): string
    {
        $data = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $data .= $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $data .= $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
        return hash('sha256', $data);
    }

    private function validate(): void
    {
        $stored = $this->get('fingerprint');
        if ($stored && $stored !== $this->fingerprint) {
            $this->destroy();
            throw new \RuntimeException('Session validation failed');
        }
    }

    private function checkExpiry(): void
    {
        $loginTime = $this->get('login_time', 0);
        if ($loginTime && (time() - $loginTime) > $this->lifetime) {
            $this->destroy();
            throw new \RuntimeException('Session expired');
        }
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$this->prefix . $key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$this->prefix . $key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$this->prefix . $key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$this->prefix . $key]);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
        $this->set('fingerprint', $this->fingerprint);
        $this->set('login_time', time());
    }

    public function setUser(int $id, string $email, string $name, int $role): void
    {
        $this->set('user_id', $id);
        $this->set('user_email', $email);
        $this->set('user_name', $name);
        $this->set('role_id', $role);
        $this->set('fingerprint', $this->fingerprint);
        $this->set('login_time', time());
        $this->regenerate();
    }

    public function isLoggedIn(): bool
    {
        return $this->has('user_id');
    }

    public function getUserId(): ?int
    {
        return $this->get('user_id');
    }

    public function getRoleId(): ?int
    {
        return $this->get('role_id', 2);
    }
}
