<?php
session_start();
require_once '../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - MySQL Table Manager</title>
    <link rel="stylesheet" href="css/login.css">
    <style>
        .login-box { max-width: 350px; margin: 80px auto; background: #fff; padding: 30px 25px; border-radius: 8px; box-shadow: 0 2px 12px #0001; }
        .login-box h2 { margin-top: 0; }
        .login-box input[type="text"], .login-box input[type="password"] { width: 100%; padding: 8px; margin-bottom: 15px; }
        .login-box button { width: 100%; padding: 8px; }
        .error { color: #f44336; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>LOGIN</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>