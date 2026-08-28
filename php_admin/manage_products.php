<?php
require_once __DIR__ . '/auth.php';
require_admin();

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
$stmt->execute();
$products = $stmt->fetchAll();

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
    <title>Manage Inventory Products - SE Shop Merchant Control</title>
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
                <a href="manage_products.php" class="nav-item active">
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

    <main class="main-content">
        
        <!-- Merchant Control Box Header -->
        <div class="card" style="margin-bottom: 2rem; border-color: rgba(6, 182, 212, 0.35); background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));">
            <div class="top-bar" style="margin-bottom: 0;">
                <div>
                    <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.75rem; border-radius: 9999px; background: rgba(6, 182, 212, 0.15); border: 1px solid rgba(6, 182, 212, 0.35); color: #38bdf8; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 0.5rem;">
                        <i class="bi bi-boxes"></i> Product Inventory Control Box
                    </div>
                    <h1 class="page-title">Inventory Management</h1>
                    <p class="page-subtitle">Add, edit, update stock levels, or delete hardware product SKUs.</p>
                </div>
                <a href="add_product.php" class="btn btn-primary" style="gap: 0.6rem;">
                    <i class="bi bi-plus-lg"></i> Create Product SKU
                </a>
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
                <div class="table-title"><i class="bi bi-boxes"></i> Product SKUs (<?= count($products); ?> Items)</div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock Level</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">No products found in inventory. Click "Create Product SKU" to add one.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?= e($product['image']); ?>" alt="<?= e($product['name']); ?>" class="img-thumb">
                                    </td>
                                    <td>
                                        <strong style="color: white; font-size: 0.9rem;"><?= e($product['name']); ?></strong>
                                        <span style="display: block; font-size: 0.75rem; color: var(--text-muted);"><?= e($product['tagline'] ?? ''); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(6, 182, 212, 0.15); color: #38bdf8; border: 1px solid rgba(6, 182, 212, 0.3);">
                                            <?= e($product['category_name'] ?? 'Hardware'); ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 800; color: white;">
                                        $<?= number_format((float)$product['price'], 2); ?>
                                        <?php if ($product['sale_price']): ?>
                                            <span style="display: block; font-size: 0.7rem; color: #34d399;">Sale: $<?= number_format((float)$product['sale_price'], 2); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['stock'] > 10): ?>
                                            <span class="badge badge-delivered"><i class="bi bi-check-circle"></i> <?= $product['stock']; ?> units</span>
                                        <?php elseif ($product['stock'] > 0): ?>
                                            <span class="badge badge-pending"><i class="bi bi-exclamation-triangle"></i> <?= $product['stock']; ?> Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge badge-cancelled"><i class="bi bi-x-circle"></i> Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                            <a href="edit_product.php?id=<?= $product['id']; ?>" class="btn btn-secondary btn-sm" title="Edit Product">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>

                                            <!-- Secure CSRF Delete Form -->
                                            <form action="delete_product.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product SKU? This will unlink the product image file.');" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                                                <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Product">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
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
