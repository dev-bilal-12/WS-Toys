<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Wrong username or password!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WS Toys - Admin Login</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { background: #1a1a1a; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
.login-box { background: #fff; padding: 50px 40px; border-radius: 20px; width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.login-box h1 { text-align: center; margin-bottom: 10px; font-size: 28px; background: linear-gradient(135deg, #2864B4, #3a7bc8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.login-box p { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
.login-box input { width: 100%; padding: 15px 20px; border: 2px solid #eee; border-radius: 12px; font-size: 16px; outline: none; transition: .3s; margin-bottom: 20px; }
.login-box input:focus { border-color: #2864B4; }
.login-box button { width: 100%; padding: 15px; background: linear-gradient(135deg, #2864B4, #3a7bc8); color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: .3s; }
.login-box button:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(40,100,180,0.4); }
.error { color: #c0392b; text-align: center; margin-bottom: 15px; font-size: 14px; }
.logo { text-align: center; margin-bottom: 20px; }
.logo img { height: 60px; border-radius: 50%; }
</style>
</head>
<body>
<div class="login-box">
    <div class="logo"><img src="../../logo.jpg" alt="WS Toys"></div>
    <h1>WS Toys Admin</h1>
    <p>Enter admin password to continue</p>
    <?php if (isset($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>

