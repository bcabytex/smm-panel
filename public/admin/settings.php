<?php
require __DIR__ . '/../../vendor/autoload.php';

use App\Config;
use App\Database;

Config::load();
Database::init();

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: /admin/login.php');
    exit;
}

$pdo = Database::connect();
$settings = [
    'api_base_url' => $_ENV['SMM_API_BASE_URL'] ?? 'https://api.example.com',
    'api_key' => $_ENV['SMM_API_KEY'] ?? '',
    'api_username' => $_ENV['SMM_API_USERNAME'] ?? '',
    'api_timeout' => $_ENV['SMM_API_TIMEOUT'] ?? '20',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | SMM Panel</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        body { margin:0; background:#07111f; color:#edf3ff; font-family:'Inter', sans-serif; }
        .admin-shell { display:flex; min-height:100vh; }
        .sidebar { width:240px; background:#0d1728; border-right:1px solid rgba(255,255,255,0.08); padding:24px 18px; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; margin-bottom:32px; }
        .brand-mark { width:30px; height:30px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#60a5fa,#2563eb); }
        .nav-links { display:flex; flex-direction:column; gap:10px; }
        .nav-links a { padding:12px 14px; border-radius:12px; color:#d6e2f3; text-decoration:none; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.04); }
        .content { flex:1; padding:32px; }
        .panel { background:rgba(15,23,42,0.8); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:22px; }
        .field { margin-bottom:18px; }
        .field label { display:block; margin-bottom:8px; }
        input { width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.08); background:#091b2f; color:white; }
        @media (max-width: 800px){ .admin-shell{flex-direction:column;} .sidebar{width:100%;} }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand"><span class="brand-mark">S</span><span>SocialBoost</span></div>
            <nav class="nav-links">
                <a href="/admin/index.php">Dashboard</a>
                <a href="/admin/services.php">Services</a>
                <a href="/admin/orders.php">Orders</a>
                <a href="/admin/users.php">Users</a>
                <a href="/admin/settings.php">Settings</a>
                <a href="/admin/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>API Settings</h1>
            <div class="panel">
                <div class="field">
                    <label>API Base URL</label>
                    <input type="text" value="<?= htmlspecialchars($settings['api_base_url']) ?>" readonly>
                </div>
                <div class="field">
                    <label>API Key</label>
                    <input type="text" value="<?= htmlspecialchars($settings['api_key']) ?>" readonly>
                </div>
                <div class="field">
                    <label>API Username</label>
                    <input type="text" value="<?= htmlspecialchars($settings['api_username']) ?>" readonly>
                </div>
                <div class="field">
                    <label>Timeout</label>
                    <input type="text" value="<?= htmlspecialchars($settings['api_timeout']) ?> seconds" readonly>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
