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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_service') {
    $stmt = $pdo->prepare('INSERT INTO services (name, category, description, price, min_quantity, max_quantity, status) VALUES (:name, :category, :description, :price, :min, :max, :status)');
    $stmt->execute([
        'name' => trim((string) ($_POST['name'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'price' => (float) ($_POST['price'] ?? 0),
        'min' => (int) ($_POST['min_quantity'] ?? 0),
        'max' => (int) ($_POST['max_quantity'] ?? 0),
        'status' => (string) ($_POST['status'] ?? 'active'),
    ]);
}

$services = $pdo->query('SELECT * FROM services ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | SMM Panel</title>
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
        .panel { background:rgba(15,23,42,0.8); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:22px; margin-bottom:25px; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
        .field { margin-bottom:16px; }
        .field label { display:block; margin-bottom:8px; }
        input, select, textarea { width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.08); background:#091b2f; color:white; }
        button { padding:12px 18px; border:none; border-radius:12px; background:linear-gradient(135deg,#60a5fa,#2563eb); color:white; font-weight:700; cursor:pointer; }
        table { width:100%; border-collapse:collapse; }
        th, td { border-bottom:1px solid rgba(255,255,255,0.06); padding:12px 10px; text-align:left; }
        th { color:#bfdbfe; }
        .badge { display:inline-block; padding:5px 10px; border-radius:999px; font-size:12px; }
        .badge.active { background:rgba(52,211,153,0.12); color:#a7f3d0; }
        .badge.inactive { background:rgba(248,113,113,0.12); color:#fecaca; }
        @media (max-width: 800px) { .admin-shell{flex-direction:column;} .sidebar{width:100%;} .grid{grid-template-columns:1fr;} }
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
            <h1>Manage Services</h1>

            <div class="panel">
                <div class="grid">
                    <form method="POST">
                        <input type="hidden" name="action" value="create_service">
                        <div class="field">
                            <label>Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="field">
                            <label>Category</label>
                            <input type="text" name="category" required>
                        </div>
                        <div class="field">
                            <label>Description</label>
                            <textarea name="description" rows="3"></textarea>
                        </div>
                        <div class="field">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" required>
                        </div>
                        <div class="field">
                            <label>Min Quantity</label>
                            <input type="number" name="min_quantity" required>
                        </div>
                        <div class="field">
                            <label>Max Quantity</label>
                            <input type="number" name="max_quantity" required>
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit">Add Service</button>
                    </form>

                    <div>
                        <h3>Service Inventory</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($service['name']) ?></td>
                                        <td><?= htmlspecialchars($service['category']) ?></td>
                                        <td>$<?= number_format((float) $service['price'], 2) ?></td>
                                        <td><span class="badge <?= htmlspecialchars($service['status']) ?>"><?= htmlspecialchars($service['status']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
