<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Security Middleware: Role Enforcement
 * Every admin page starts with a session check verifying $_SESSION['role'] === 'admin'.
 * If not, redirects unauthorized users immediately to login.php.
 */
function require_admin()
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['flash_error'] = "Unauthorized access. Admin privileges required.";
        header("Location: login.php");
        exit();
    }
}

/**
 * Generate CSRF Token for Form Protection
 */
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token on Form Submissions
 */
function verify_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token']) || empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $_SESSION['flash_error'] = "CSRF Token Validation Failed. Request rejected.";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'admin_dashboard.php'));
        exit();
    }
}

/**
 * Output Escaping Helper (XSS Protection)
 */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
