<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Config;
use App\Database;

Config::load();
Database::init();

session_start();

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $pdo = Database::connect();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header('Location: /admin/index.php');
        exit;
    }

    $loginError = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SMM Panel</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <style>
        body {
            background: linear-gradient(180deg, #07111f, #0d1728);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Inter', sans-serif; color: #edf3ff;
        }
        .auth-box {
            width: min(450px, 92vw);
            background: rgba(15,23,42,0.9);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 22px;
            padding: 32px;
            box-shadow: 0 30px 70px rgba(0,0,0,0.28);
        }
        .auth-box h1 { margin-bottom: 10px; }
        .auth-box p { color: #a1b0c7; }
        .field { margin-top: 18px; }
        .field label { display:block; margin-bottom:8px; }
        input {
            width:100%; padding:14px; border-radius:10px;
            border:1px solid rgba(255,255,255,0.08); background:#091b2f; color:white;
        }
        .login-btn {
            width:100%; margin-top:22px; padding:14px; border:none; border-radius:10px;
            background: linear-gradient(135deg, #60a5fa, #2563eb); color:white; font-weight:700; cursor:pointer;
        }
        .alert { margin-top:16px; color:#fecaca; background:rgba(248,113,113,0.12); border:1px solid rgba(248,113,113,0.3); padding:12px; border-radius:10px; }
    </style>
</head>
<body>
    <div class="auth-box">
        <h1>Admin Login</h1>
        <p>Manage services, orders, and users from one place.</p>

        <?php if ($loginError): ?>
            <div class="alert"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="action" value="login">
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="admin@example.com" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="********" required>
            </div>
            <button class="login-btn" type="submit">Login</button>
        </form>
    </div>
</body>
</html>
