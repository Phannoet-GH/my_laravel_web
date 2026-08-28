<?php
require_once __DIR__ . '/auth.php';
require_admin(); // Strict Role Enforcement

// Query all registered users
$stmt = $pdo->prepare("SELECT * FROM users ORDER BY id ASC");
$stmt->execute();
$users = $stmt->fetchAll();

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users Directory - SE Shop Admin</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="brand-badge">SE</div>
                <div class="brand-title">SE <span>Admin</span></div>
            </div>

            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="nav-item">
                    <i class="bi bi-speedometer2"></i> Dashboard Overview
                </a>
                <a href="manage_products.php" class="nav-item">
                    <i class="bi bi-boxes"></i> Inventory Products
                </a>
                <a href="manage_orders.php" class="nav-item">
                    <i class="bi bi-receipt"></i> Orders Fulfillment
                </a>
                <a href="manage_users.php" class="nav-item active">
                    <i class="bi bi-people"></i> Customers Directory
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile-badge">
                <div class="user-avatar">
                    <?= e(strtoupper(substr($_SESSION['name'] ?? 'A', 0, 2))); ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= e($_SESSION['name'] ?? 'Admin User'); ?></span>
                    <span class="user-role"><?= e($_SESSION['role'] ?? 'admin'); ?></span>
                </div>
            </div>
            <a href="logout.php" class="nav-item" style="margin-top: 0.75rem; color: #f87171;">
                <i class="bi bi-box-arrow-right" style="color: #f87171;"></i> Sign Out
            </a>
        </div>
    </aside>

    <main class="main-content">
        
        <!-- Merchant Control Box Header -->
        <div class="card" style="margin-bottom: 2rem; border-color: rgba(139, 92, 246, 0.35); background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));">
            <div class="top-bar" style="margin-bottom: 0;">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.75rem; border-radius: 9999px; background: rgba(139, 92, 246, 0.15); border: 1px solid rgba(139, 92, 246, 0.35); color: #c084fc; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.5rem;">
                        <i class="bi bi-people"></i> Customer Directory Control Box
                    </div>
                    <h1 class="page-title">User Accounts Directory</h1>
                    <p class="page-subtitle">View all registered store customers and system administrator accounts.</p>
                </div>
            </div>
        </div>


        <?php if ($flash_success): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= e($flash_success); ?></div>
        <?php endif; ?>

        <div class="table-card">
            <div class="table-header">
                <div class="table-title"><i class="bi bi-people"></i> Platform Accounts (<?= count($users); ?> Total)</div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Account Role</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: var(--accent-cyan);">
                                    #USR-<?= sprintf('%04d', $user['id']); ?>
                                </td>
                                <td style="font-weight: 700; color: white;">
                                    <?= e($user['name']); ?>
                                </td>
                                <td style="color: var(--text-muted);">
                                    <?= e($user['email']); ?>
                                </td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #c084fc; border: 1px solid rgba(139, 92, 246, 0.4);">
                                            <i class="bi bi-shield-lock"></i> ADMIN
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-delivered">
                                            <i class="bi bi-person"></i> CUSTOMER
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('M d, Y · h:i A', strtotime($user['created_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
