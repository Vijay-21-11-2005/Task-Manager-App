<?php
require 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if the username is already taken
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = 'That username is already taken.';
        } else {
            // Store a scrambled (hashed) password, never the real one
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$username, $hash]);
            $success = 'Account created. You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up - Task Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 400px; margin-top: 80px;">
        <div class="add-task-container">
            <h2 style="justify-content: center; margin-bottom: 20px;"><i class="fa-solid fa-user-plus"></i> Create Account</h2>
            
            <?php if ($error): ?>
                <p style="color:#e11d48; font-size: 0.9rem; margin-bottom: 15px; text-align: center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color:#16a34a; font-size: 0.9rem; margin-bottom: 15px; text-align: center;"><?= htmlspecialchars($success) ?></p>
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

                <button type="submit" class="btn-primary" style="width: 100%; margin-bottom: 15px;">Sign Up</button>
                
                <p style="text-align: center; font-size: 0.85rem; color: #666;">
                    Already have an account? <a href="login.php" style="color: #111; font-weight: 600; text-decoration: none;">Log in</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>