<?php
require_once __DIR__ . '/auth.php';
require_admin();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// Query existing product using PDO Prepared Statement
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['flash_error'] = "Product SKU not found.";
    header("Location: manage_products.php");
    exit();
}

// Fetch categories
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
    $imagePath = $product['image'];

    if (empty($name) || empty($description) || $price <= 0) {
        $error = "Please fill in all required fields (Name, Description, and Price must be greater than 0).";
    } else {

        // Check if replacement image file was uploaded
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
                    // Unlink old local image file if present in uploads/ directory
                    if (str_starts_with($product['image'], 'uploads/')) {
                        $oldFilePath = __DIR__ . '/' . $product['image'];
                        if (file_exists($oldFilePath)) {
                            @unlink($oldFilePath);
                        }
                    }
                    $imagePath = 'uploads/' . $newFileName;
                } else {
                    $error = "Failed to save uploaded replacement image.";
                }
            } else {
                $error = "Invalid file extension. Allowed formats: JPG, JPEG, PNG, WEBP, GIF.";
            }
        } elseif (!empty($_POST['image_url'])) {
            $imagePath = trim($_POST['image_url']);
        }

        if (empty($error)) {
            // Update product in MySQL/PDO database
            $stmt = $pdo->prepare("
                UPDATE products 
                SET category_id = ?, name = ?, tagline = ?, description = ?, price = ?, sale_price = ?, stock = ?, is_featured = ?, is_trending = ?, image = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $category_id ?: null,
                $name,
                $tagline,
                $description,
                $price,
                $sale_price,
                $stock,
                $is_featured,
                $is_trending,
                $imagePath,
                $id
            ]);

            $_SESSION['flash_success'] = "Product SKU '{$name}' updated successfully!";
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
    <title>Edit Product SKU #<?= $product['id']; ?> - SE Shop Admin</title>
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
                        <i class="bi bi-pencil-square"></i> Edit SKU Control Box
                    </div>
                    <h1 class="page-title">Edit Product SKU #<?= $product['id']; ?></h1>
                    <p class="page-subtitle">Update pricing, stock levels, specifications, or thumbnail image.</p>
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
            <form action="edit_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token); ?>">
                <input type="hidden" name="id" value="<?= $product['id']; ?>">

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required class="form-control" value="<?= e($product['name']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="">Select Category...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id']; ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?= e($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tagline">Tagline / Subheading</label>
                    <input type="text" id="tagline" name="tagline" class="form-control" value="<?= e($product['tagline'] ?? ''); ?>">
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="price">Regular Price ($) *</label>
                        <input type="number" step="0.01" id="price" name="price" required class="form-control" value="<?= e($product['price']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="sale_price">Sale Price ($ Optional)</label>
                        <input type="number" step="0.01" id="sale_price" name="sale_price" class="form-control" value="<?= e($product['sale_price'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Inventory Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" required class="form-control" value="<?= e($product['stock']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Specifications & Description *</label>
                    <textarea id="description" name="description" rows="4" required class="form-control"><?= e($product['description']); ?></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Current Thumbnail</label>
                        <div style="display: flex; items-center; gap: 1rem;">
                            <img src="<?= e($product['image']); ?>" alt="Current Image" class="img-thumb" style="width: 60px; height: 60px;">
                            <span style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center;">
                                <?= e($product['image']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image_file">Replace Image File (Uploads to uploads/)</label>
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="display: flex; gap: 2rem; align-items: center; padding-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : ''; ?>> <span>Featured Product</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_trending" value="1" <?= $product['is_trending'] ? 'checked' : ''; ?>> <span>Trending Product</span>
                    </label>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Product Details
                    </button>
                    <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

    </main>

</body>
</html>
