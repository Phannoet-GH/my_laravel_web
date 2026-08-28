<?php

// 1. Prepare writable /tmp storage directory structure for Vercel Serverless environment
$tmpStorage = '/tmp/storage';
$tmpDirs = [
    $tmpStorage,
    "{$tmpStorage}/framework",
    "{$tmpStorage}/framework/views",
    "{$tmpStorage}/framework/cache",
    "{$tmpStorage}/framework/cache/data",
    "{$tmpStorage}/framework/sessions",
    "{$tmpStorage}/framework/testing",
    "{$tmpStorage}/logs",
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Configure Vercel serverless environment overrides
putenv('APP_ENV=production');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('LOG_CHANNEL=stderr');

$_ENV['APP_ENV'] = 'production';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['LOG_CHANNEL'] = 'stderr';

// Set fallback APP_KEY if missing in Vercel settings
if (!getenv('APP_KEY') && empty($_ENV['APP_KEY'])) {
    $fallbackKey = 'base64:xuukG2iKtEl144io51yWD0xcmkwgriuA7m5bUI1JDvQ=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// 3. Smart Database Fallback for Vercel Serverless
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'mysql');
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '127.0.0.1');

if ($dbConn === 'mysql' && ($dbHost === '127.0.0.1' || $dbHost === 'localhost')) {
    $tmpDb = '/tmp/database.sqlite';
    if (!file_exists($tmpDb)) {
        $sourceDb = __DIR__ . '/../database/seed_database.sqlite';
        if (file_exists($sourceDb)) {
            copy($sourceDb, $tmpDb);
        } else {
            touch($tmpDb);
        }
    }
    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

// 4. Bootstrap Laravel
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Dynamically bind storage path to /tmp/storage
$app->useStoragePath($tmpStorage);

$app->handleRequest(\Illuminate\Http\Request::capture());
