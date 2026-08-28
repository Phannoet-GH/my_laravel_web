<?php

// Load .env configuration
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $env[trim($name)] = trim($value);
        }
    }
}

$dbConnection = $env['DB_CONNECTION'] ?? 'sqlite';

try {
    if ($dbConnection === 'mysql') {
        $host = $env['DB_HOST'] ?? '127.0.0.1';
        $port = $env['DB_PORT'] ?? '3306';
        $dbname = $env['DB_DATABASE'] ?? 'my_laravel_app';
        $username = $env['DB_USERNAME'] ?? 'root';
        $password = $env['DB_PASSWORD'] ?? '';
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password);
    } else {
        $db_file = __DIR__ . '/../database/database.sqlite';
        $pdo = new PDO("sqlite:" . $db_file);
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $autoInc = ($dbConnection === 'mysql') ? "AUTO_INCREMENT" : "AUTOINCREMENT";

    // Auto-create database tables if they do not exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY {$autoInc},
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) DEFAULT 'customer',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INT PRIMARY KEY {$autoInc},
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT
        );

        CREATE TABLE IF NOT EXISTS products (
            id INT PRIMARY KEY {$autoInc},
            category_id INT,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            tagline VARCHAR(255),
            description TEXT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            sale_price DECIMAL(10, 2),
            stock INT DEFAULT 0,
            image VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INT PRIMARY KEY {$autoInc},
            order_number VARCHAR(100) UNIQUE NOT NULL,
            user_id INT,
            customer_name VARCHAR(255) NOT NULL,
            customer_email VARCHAR(255) NOT NULL,
            customer_phone VARCHAR(50),
            shipping_address TEXT NOT NULL,
            city VARCHAR(100) NOT NULL,
            postal_code VARCHAR(20) NOT NULL,
            total_amount DECIMAL(10, 2) NOT NULL,
            payment_method VARCHAR(50) DEFAULT 'card',
            status VARCHAR(50) DEFAULT 'Pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INT PRIMARY KEY {$autoInc},
            order_id INT NOT NULL,
            product_id INT,
            product_name VARCHAR(255) NOT NULL,
            unit_price DECIMAL(10, 2) NOT NULL,
            quantity INT NOT NULL,
            subtotal DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
        );
    ");

    // Seed default Admin & Customer users if database is empty
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['System Administrator', 'admin@eshop.com', $adminPass]);

        $custPass = password_hash('user123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
        $stmt->execute(['David Kim', 'customer@eshop.com', $custPass]);
    }

    // Seed initial categories & products if database is empty
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $stmt->execute(['Laptops & Workstations', 'laptops', 'High performance developer rigs']);
        $catId1 = $pdo->lastInsertId();

        $stmt->execute(['Smart Peripherals', 'peripherals', 'Ergonomic keyboards & mice']);
        $catId2 = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, tagline, description, price, sale_price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$catId1, 'SE ProBook Cyber X 16"', 'se-probook-cyber-x-16', 'Next-Gen M3 Architecture with 64GB RAM', 'Engineered specifically for full-stack software development.', 2499.99, 2299.99, 18, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1000&q=80']);
        $p1 = $pdo->lastInsertId();

        $stmt->execute([$catId2, 'CyberTactile 75% Mechanical Keyboard', 'cybertactile-keyboard', 'Hot-Swappable Lubricated Linear Switches', 'Custom PBT keycaps with tri-mode connectivity.', 189.99, 159.99, 45, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1000&q=80']);
        $p2 = $pdo->lastInsertId();

        // Seed sample order
        $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone, shipping_address, city, postal_code, total_amount, payment_method, status) VALUES (?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['SE-ORD-894102', 'David Kim', 'customer@eshop.com', '+1 (555) 234-5678', '742 Silicon Valley Ave, Suite 300', 'San Francisco', '94107', 2459.98, 'card', 'Shipped']);
        $orderId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orderId, $p1, 'SE ProBook Cyber X 16"', 2299.99, 1, 2299.99]);
        $stmt->execute([$orderId, $p2, 'CyberTactile 75% Mechanical Keyboard', 159.99, 1, 159.99]);
    }

} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}
