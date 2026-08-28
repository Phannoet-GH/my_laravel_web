<?php
require_once __DIR__ . '/auth.php';
require_admin(); // Strict Role Enforcement

// Query 1: Total Revenue (Sum of non-cancelled orders)
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'Cancelled'");
$stmt->execute();
$total_revenue = (float) $stmt->fetchColumn();

// Query 2: Total Orders Count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders");
$stmt->execute();
$total_orders = (int) $stmt->fetchColumn();

// Query 3: Total Registered Customers
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$stmt->execute();
$total_customers = (int) $stmt->fetchColumn();

// Query 4: Total Products in Stock
$stmt = $pdo->prepare("SELECT COALESCE(SUM(stock), 0) FROM products");
$stmt->execute();
$total_stock = (int) $stmt->fetchColumn();

// Query 5: Recent 5 Activity Orders
$stmt = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_orders = $stmt->fetchAll();

$flash_success = $_SESSION['flash_success'] ?? '';
$flash_warning = $_SESSION['flash_warning'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_warning'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SE Shop Merchant Control</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <!-- Fixed Sidebar Navigation -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="brand-badge">SE</div>
                <div class="brand-title">SE <span>Admin</span></div>
            </div>

            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="nav-item active">
                    <i class="bi bi-speedometer2"></i> Dashboard Overview
                </a>
                <a href="manage_products.php" class="nav-item">
                    <i class="bi bi-boxes"></i> Inventory Products
                </a>
                <a href="manage_orders.php" class="nav-item">
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

    <!-- Main Content Area -->
    <main class="main-content">
        
        <!-- Merchant Control Header Box -->
        <div class="card" style="margin-bottom: 2rem; border-color: rgba(99, 102, 241, 0.35); background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));">
            <div class="top-bar" style="margin-bottom: 0;">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.75rem; border-radius: 9999px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.35); color: #818cf8; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.5rem;">
                        <i class="bi bi-shield-lock-fill"></i> SE Shop Control Box
                    </div>
                    <h1 class="page-title">Store Overview</h1>
                    <p class="page-subtitle">Welcome to SE Shop merchant metrics and fulfillment control center.</p>
                </div>
                <a href="add_product.php" class="btn btn-primary" style="gap: 0.6rem;">
                    <i class="bi bi-plus-lg"></i> Add New Product
                </a>
            </div>
        </div>

        <?php if ($flash_success): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> <?= e($flash_success); ?></div>
        <?php endif; ?>
        <?php if ($flash_warning): ?>
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill"></i> <?= e($flash_warning); ?></div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
            <div class="alert alert-danger"><i class="bi bi-x-circle-fill"></i> <?= e($flash_error); ?></div>
        <?php endif; ?>

        <!-- Key Metrics Cards Box Grid (4 Stat Cards) -->
        <div class="metrics-grid">
            
            <div class="card">
                <div class="metric-header">
                    <span class="metric-label">Total Revenue</span>
                    <div class="metric-icon icon-emerald"><i class="bi bi-currency-dollar"></i></div>
                </div>
                <div class="metric-value">$<?= number_format($total_revenue, 2); ?></div>
                <div class="metric-sub"><i class="bi bi-graph-up-arrow"></i> Completed transactions</div>
            </div>

            <div class="card">
                <div class="metric-header">
                    <span class="metric-label">Total Orders</span>
                    <div class="metric-icon icon-blue"><i class="bi bi-cart-check"></i></div>
                </div>
                <div class="metric-value"><?= number_format($total_orders); ?></div>
                <div class="metric-sub">Processed customer orders</div>
            </div>

            <div class="card">
                <div class="metric-header">
                    <span class="metric-label">Registered Customers</span>
                    <div class="metric-icon icon-purple"><i class="bi bi-people"></i></div>
                </div>
                <div class="metric-value"><?= number_format($total_customers); ?></div>
                <div class="metric-sub">Active user accounts</div>
            </div>

            <div class="card">
                <div class="metric-header">
                    <span class="metric-label">Products in Stock</span>
                    <div class="metric-icon icon-amber"><i class="bi bi-boxes"></i></div>
                </div>
                <div class="metric-value"><?= number_format($total_stock); ?></div>
                <div class="metric-sub">Inventory unit items</div>
            </div>

        </div>

        <!-- Recent Activity Data Table Box -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title"><i class="bi bi-clock-history"></i> Recent Activity (Latest 5 Orders)</div>
                <a href="manage_orders.php" style="color: var(--accent-cyan); text-decoration: none; font-size: 0.75rem; font-weight: 700;">View All Orders &rarr;</a>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Date</th>
                            <th>Total Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No orders recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td style="font-family: monospace; font-weight: 700; color: var(--accent-cyan);">
                                        <?= e($order['order_number']); ?>
                                    </td>
                                    <td style="font-weight: 700; color: white;">
                                        <?= e($order['customer_name']); ?>
                                    </td>
                                    <td style="color: var(--text-muted);">
                                        <?= e($order['customer_email']); ?>
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
