<?php
require_once __DIR__ . '/auth.php';
require_admin(); // Strict Role Enforcement

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_products.php");
    exit();
}

$csrf = $_POST['csrf_token'] ?? '';
verify_csrf_token($csrf);

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash_error'] = "Invalid Product SKU ID.";
    header("Location: manage_products.php");
    exit();
}

// Fetch product to retrieve image path for file unlinking
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product) {
    // Security & File Cleanup: Unlink image file from server disk if stored in uploads/
    if (!empty($product['image']) && str_starts_with($product['image'], 'uploads/')) {
        $filePath = __DIR__ . '/' . $product['image'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // Delete record from database using PDO Prepared Statement
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash_success'] = "Product SKU #{$id} ('" . e($product['name']) . "') and associated image file removed successfully!";
} else {
    $_SESSION['flash_error'] = "Product record not found.";
}

header("Location: manage_products.php");
exit();
