<?php
require_once __DIR__ . '/auth.php';
require_admin(); // Strict Role Enforcement

// Handle Status Update POST Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $csrf = $_POST['csrf_token'] ?? '';
    verify_csrf_token($csrf);

    $order_id = (int) ($_POST['order_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    if ($order_id > 0 && in_array($status, $allowedStatuses)) {
        // Update Order Status via PDO Prepared Statement
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);

        $_SESSION['flash_success'] = "Order #{$order_id} fulfillment status updated to '{$status}'.";
    } else {
        $_SESSION['flash_error'] = "Invalid status update request.";
    }

    header("Location: manage_orders.php");
    exit();
}

// Fetch all orders with item counts using PDO Prepared Statement
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.id) as item_count 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    GROUP BY o.id 
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll();

$csrf_token = generate_csrf_token();
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management & Fulfillment - SE Shop Admin</title>
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
                <a href="manage_orders.php" class="nav-item active">
                    <i class="bi bi-receipt"></i> Orders Fulfillment
                </a>
                <a href="manage_users.php" class="nav-item">
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
        <div class="card" style="margin-bottom: 2rem; border-color: rgba(99, 102, 241, 0.35); background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));">
            <div class="top-bar" style="margin-bottom: 0;">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.75rem; border-radius: 9999px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.35); color: #818cf8; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.5rem;">
                        <i class="bi bi-receipt"></i> Orders Fulfillment Control Box
                    </div>
                    <h1 class="page-title">Order Management & Fulfillment</h1>
                    <p class="page-subtitle">Track platform orders, review shipping info, and update fulfillment statuses.</p>
                </div>
            </div>
        </div>


        <?php if ($flash_success): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= e($flash_success); ?></div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
            <div class="alert alert-danger"><i class="bi bi-x-circle-fill"></i> <?= e($flash_error); ?></div>
        <?php endif; ?>

        <div class="table-card">
            <div class="table-header">
                <div class="table-title"><i class="bi bi-receipt"></i> Platform Customer Orders (<?= count($orders); ?> Records)</div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer Contact</th>
                            <th>Shipping Address</th>
                            <th>Date</th>
                            <th>Total Price</th>
                            <th>Current Status</th>
                            <th style="text-align: right;">Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No customer orders placed yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td style="font-family: monospace; font-weight: 700; color: var(--accent-cyan);">
                                        <?= e($order['order_number']); ?>
                                        <span style="display: block; font-size: 0.7rem; color: var(--text-muted);"><?= $order['item_count']; ?> items</span>
                                    </td>
                                    <td>
                                        <strong style="color: white;"><?= e($order['customer_name']); ?></strong>
                                        <span style="display: block; font-size: 0.75rem; color: var(--text-muted);"><?= e($order['customer_email']); ?></span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8rem; color: #cbd5e1;"><?= e($order['shipping_address']); ?>, <?= e($order['city']); ?></span>
                                    </td>
                                    <td>
                                        <?= date('M d, Y · h:i A', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td style="font-weight: 800; color: white;">
                                        $<?= number_format((float)$order['total_amount'], 2); ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $st = strtolower($order['status']);
                                            $badgeClass = 'badge-pending';
                                            if ($st === 'processing') $badgeClass = 'badge-processing';
                                            if ($st === 'shipped') $badgeClass = 'badge-shipped';
                                            if ($st === 'delivered') $badgeClass = 'badge-delivered';
                                            if ($st === 'cancelled') $badgeClass = 'badge-cancelled';
                                        ?>
                                        <span class="badge <?= $badgeClass; ?>"><?= e($order['status']); ?></span>
                                    </td>
                                    <td style="text-align: right;">
                                        <!-- Inline Status Updater Form -->
                                        <form action="manage_orders.php" method="POST" style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">

                                            <select name="status" class="form-control" style="width: auto; padding: 0.35rem 0.5rem; font-size: 0.75rem;">
                                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>

                                            <button type="submit" class="btn btn-primary btn-sm" title="Save Status">
                                                <i class="bi bi-check2"></i> Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
