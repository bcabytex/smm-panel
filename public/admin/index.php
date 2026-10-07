<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Config;
use App\Database;

Config::load();
Database::init();

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

if (($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: /');
    exit;
}

$pdo = Database::connect();

$services = $pdo->query('SELECT * FROM services ORDER BY id DESC LIMIT 20')->fetchAll();
$orders = $pdo->query('SELECT o.*, s.name AS service_name, u.name AS user_name FROM orders o JOIN services s ON s.id = o.service_id JOIN users u ON u.id = o.user_id ORDER BY o.id DESC LIMIT 20')->fetchAll();
$users = $pdo->query('SELECT * FROM users ORDER BY id DESC LIMIT 20')->fetchAll();
$transactionTotal = $pdo->query('SELECT COALESCE(SUM(amount), 0) AS total FROM transactions')->fetch()['total'];

$stats = [
    'total_users' => count($users),
    'total_orders' => count($orders),
    'services' => count($services),
    'revenue' => $transactionTotal,
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SMM Panel</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        body { margin:0; background:#07111f; color:#edf3ff; font-family:'Inter', sans-serif; }
        .admin-shell { display:flex; min-height:100vh; }
        .sidebar { width:240px; background:#0d1728; border-right:1px solid rgba(255,255,255,0.08); padding:24px 18px; }
        .brand { display:flex; align-items:center; gap:10px; font-weight:700; margin-bottom:32px; }
        .brand-mark { width:30px; height:30px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#60a5fa,#2563eb); }
        .nav-links { display:flex; flex-direction:column; gap:10px; }
        .nav-links a { padding:12px 14px; border-radius:12px; color:#d6e2f3; text-decoration:none; background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.04); }
        .nav-links a:hover { background:rgba(96,165,250,0.08); }
        .content { flex:1; padding:32px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .stats-grid { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:18px; margin-bottom:30px; }
        .stat-card { background:rgba(15,23,42,0.8); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:20px; }
        .stat-card .label { color:#a1b0c7; font-size:13px; }
        .stat-card .value { font-size:2rem; font-weight:700; margin-top:12px; }
        .panel { background:rgba(15,23,42,0.8); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:22px; }
        table { width:100%; border-collapse:collapse; }
        th, td { border-bottom:1px solid rgba(255,255,255,0.06); padding:12px 10px; text-align:left; }
        th { color:#bfdbfe; }
        .badge { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; }
        .badge.pending { background:rgba(251,191,36,0.12); color:#fcd34d; }
        .badge.processing { background:rgba(96,165,250,0.12); color:#bfdbfe; }
        .badge.completed { background:rgba(52,211,153,0.12); color:#a7f3d0; }
        .logout { color:#fecaca; text-decoration:none; }
        @media (max-width: 1000px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 720px) { .admin-shell { flex-direction:column; } .sidebar { width:100%; } .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="sidebar">
            <div class="brand">
                <span class="brand-mark">S</span>
                <span>SocialBoost</span>
            </div>
            <nav class="nav-links">
                <a href="/admin/index.php">Dashboard</a>
                <a href="/admin/services.php">Services</a>
                <a href="/admin/orders.php">Orders</a>
                <a href="/admin/users.php">Users</a>
                <a href="/admin/settings.php">Settings</a>
                <a class="logout" href="/admin/logout.php">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>.</p>
                </div>
                <a class="logout" href="/admin/logout.php">Logout</a>
            </div>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="label">Total Users</div>
                    <div class="value"><?= $stats['total_users'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Orders</div>
                    <div class="value"><?= $stats['total_orders'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Services</div>
                    <div class="value"><?= $stats['services'] ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Revenue</div>
                    <div class="value">$<?= number_format((float) $stats['revenue'], 2) ?></div>
                </div>
            </section>

            <section class="panel">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                <td><?= htmlspecialchars($order['service_name']) ?></td>
                                <td><?= (int) $order['quantity'] ?></td>
                                <td><span class="badge <?= htmlspecialchars($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span></td>
                                <td>$<?= number_format((float) $order['price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
