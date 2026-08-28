<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Redirect if already logged in as admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';
if (isset($_SESSION['flash_error'])) {
    $error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    verify_csrf_token($csrf);

    if (empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        // Security: PDO Prepared Statement against SQL Injection
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] !== 'admin') {
                $error = "Access denied. Only Admin users can sign in to the Merchant Control Portal.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['flash_success'] = "Welcome back, " . $user['name'] . "!";

                header("Location: admin_dashboard.php");
                exit();
            }
        } else {
            $error = "Invalid email address or password.";
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In - Merchant Portal</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-logo">SE</div>
            <h2>Merchant Portal Sign In</h2>
            <p>Enter your admin credentials to access store controls</p>
        </div>

        <div class="demo-preset-box">
            <div class="demo-title">QUICK DEMO CREDENTIALS</div>
            <div class="demo-grid">
                <button type="button" onclick="fillAdmin()" class="btn-demo-admin">
                    <i class="bi bi-shield-lock"></i> Demo Admin
                </button>
                <button type="button" onclick="fillCustomer()" class="btn-demo-cust">
                    <i class="bi bi-person-x"></i> Test Customer
                </button>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span><?= e($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

            <div class="form-group">
                <label for="email">Admin Email Address</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-envelope icon"></i>
                    <input type="email" id="email" name="email" required placeholder="admin@eshop.com" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-lock icon"></i>
                    <input type="password" id="password" name="password" required placeholder="••••••••" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Sign In to Merchant Control <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    </div>

    <script>
        function fillAdmin() {
            document.getElementById('email').value = 'admin@eshop.com';
            document.getElementById('password').value = 'admin123';
        }
        function fillCustomer() {
            document.getElementById('email').value = 'customer@eshop.com';
            document.getElementById('password').value = 'user123';
        }
    </script>
</body>
</html>
