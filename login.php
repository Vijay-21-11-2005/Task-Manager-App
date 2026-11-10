<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Check the typed password against the saved hash
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Wrong username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Task Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin-top: 80px;">
        <div class="add-task-container">
            <h2 style="justify-content: center; margin-bottom: 20px;"><i class="fa-solid fa-right-to-bracket"></i> Welcome Back</h2>
            
            <?php if ($error): ?>
                <p style="color:#e11d48; font-size: 0.9rem; margin-bottom: 15px; text-align: center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" class="add-task-form" style="flex-direction: column;">
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.9rem; color: #555; margin-bottom: 5px; display: block;">Username</label>
                    <input type="text" name="username" required style="width: 100%;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-size: 0.9rem; color: #555; margin-bottom: 5px; display: block;">Password</label>
                    <input type="password" name="password" required style="width: 100%;">
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-bottom: 15px;">Log In</button>
                
                <p style="text-align: center; font-size: 0.85rem; color: #666;">
                    Don't have an account? <a href="register.php" style="color: #111; font-weight: 600; text-decoration: none;">Sign up</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>