<?php
require_once __DIR__ . '/auth.php';
require_admin();

// Fetch categories for dropdown
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
$stmt->execute();
$categories = $stmt->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    verify_csrf_token($csrf);

    $name = trim($_POST['name'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $tagline = trim($_POST['tagline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $sale_price = !empty($_POST['sale_price']) ? (float) $_POST['sale_price'] : null;
    $stock = (int) ($_POST['stock'] ?? 0);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_trending = isset($_POST['is_trending']) ? 1 : 0;
    $image_url_fallback = trim($_POST['image_url'] ?? '');

    if (empty($name) || empty($description) || $price <= 0) {
        $error = "Please fill in all required fields (Name, Description, and Price must be greater than 0).";
    } else {
        $imagePath = $image_url_fallback;

        // Image file upload handling
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image_file']['tmp_name'];
            $fileName = $_FILES['image_file']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = 'prod_' . uniqid() . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/uploads/';

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $destPath = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $imagePath = 'uploads/' . $newFileName;
                } else {
                    $error = "Failed to upload product image to server disk.";
                }
            } else {
                $error = "Invalid image file format. Allowed extensions: JPG, JPEG, PNG, WEBP, GIF.";
            }
        }

        if (empty($imagePath)) {
            $imagePath = 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1000&q=80';
        }

        if (empty($error)) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));

            // PDO Prepared Statement
            $stmt = $pdo->prepare("
                INSERT INTO products (category_id, name, slug, tagline, description, price, sale_price, stock, is_featured, is_trending, image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $category_id ?: null,
                $name,
                $slug . '-' . rand(100, 999),
                $tagline,
                $description,
                $price,
                $sale_price,
                $stock,
                $is_featured,
                $is_trending,
                $imagePath
            ]);

            $_SESSION['flash_success'] = "Product SKU '{$name}' created successfully!";
            header("Location: manage_products.php");
            exit();
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
    <title>Add New Product SKU - SE Shop Admin</title>
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
                        <i class="bi bi-plus-circle"></i> Create SKU Control Box
                    </div>
                    <h1 class="page-title">Add New Product SKU</h1>
                    <p class="page-subtitle">Enter hardware details, pricing, stock levels, and upload thumbnail image.</p>
                </div>
                <a href="manage_products.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Inventory
                </a>
            </div>
        </div>


        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i> <?= e($error); ?></div>
        <?php endif; ?>

        <div class="table-card" style="padding: 2rem;">
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required placeholder="e.g. SE ProBook Cyber 16&quot;" class="form-control" value="<?= e($_POST['name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">Select Category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id']; ?>"><?= e($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tagline">Tagline / Subheading</label>
                    <input type="text" id="tagline" name="tagline" placeholder="e.g. High Performance M3 Architecture" class="form-control" value="<?= e($_POST['tagline'] ?? ''); ?>">
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="price">Regular Price ($) *</label>
                        <input type="number" step="0.01" id="price" name="price" required placeholder="1999.99" class="form-control" value="<?= e($_POST['price'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="sale_price">Sale Price ($ Optional)</label>
                        <input type="number" step="0.01" id="sale_price" name="sale_price" placeholder="1799.99" class="form-control" value="<?= e($_POST['sale_price'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Initial Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" required placeholder="25" class="form-control" value="<?= e($_POST['stock'] ?? '10'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Full Specifications & Description *</label>
                    <textarea id="description" name="description" rows="4" required placeholder="Enter technical specs, hardware details..." class="form-control"><?= e($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="image_file">Upload Image File (Saves to uploads/)</label>
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="image_url">Or Image URL (Fallback)</label>
                        <input type="url" id="image_url" name="image_url" placeholder="https://images.unsplash.com/..." class="form-control">
                    </div>
                </div>

                <div class="form-group" style="display: flex; gap: 2rem; align-items: center; padding-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1"> <span>Featured Product</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_trending" value="1"> <span>Trending Product</span>
                    </label>
                </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Product SKU
                    </button>
                    <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

    </main>

</body>
</html>
