<?php
class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::start();
        return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
    }

    public static function require(): void
    {
        if (!self::check()) {
            header('Location: /admin.php?route=login');
            exit;
        }
    }

    public static function login(int $id, string $username, string $email): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['admin_logged']  = true;
        $_SESSION['admin_id']      = $id;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_email']   = $email;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function user(): ?array
    {
        self::start();
        if (!self::check()) return null;
        return [
            'id'       => $_SESSION['admin_id'] ?? 0,
            'username' => $_SESSION['admin_username'] ?? '',
            'email'    => $_SESSION['admin_email'] ?? '',
        ];
    }
}
