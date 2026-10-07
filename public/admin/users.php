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
$users = $pdo->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | SMM Panel</title>
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
        table { width:100%; border-collapse:collapse; }
        th, td { padding:12px 10px; border-bottom:1px solid rgba(255,255,255,0.06); text-align:left; }
        th { color:#bfdbfe; }
        .badge { display:inline-block; padding:5px 10px; border-radius:999px; font-size:12px; }
        .badge.admin { background:rgba(96,165,250,0.12); color:#bfdbfe; }
        .badge.user { background:rgba(52,211,153,0.12); color:#a7f3d0; }
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
            <h1>Users</h1>
            <div class="panel">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><span class="badge <?= htmlspecialchars($user['role']) ?>"><?= htmlspecialchars($user['role']) ?></span></td>
                                <td>$<?= number_format((float) $user['balance'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
