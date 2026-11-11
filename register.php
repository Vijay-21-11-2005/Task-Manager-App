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
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = 'That username is already taken.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO workspaces (name) VALUES (?)');
            $stmt->execute([$username . "'s Workspace"]);
            $workspace_id = $pdo->lastInsertId();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $stmt->execute([$username, $hash]);
            $user_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare('INSERT INTO workspace_members (user_id, workspace_id, role) VALUES (?, ?, "admin")');
            $stmt->execute([$user_id, $workspace_id]);

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
    <title>Sign Up - TaskMaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div style="position: absolute; top: 20px; right: 20px;">
                <button id="theme-toggle" class="btn-icon" onclick="toggleTheme()"><i class="fa-solid fa-moon" id="theme-icon"></i></button>
            </div>
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Start organizing your work today</p>
            </div>
            <?php if ($error): ?>
                <p style="color:#e11d48; font-size: 0.85rem; margin-bottom: 15px; text-align: center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color:#16a34a; font-size: 0.85rem; margin-bottom: 15px; text-align: center;"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Sign Up</button>
            </form>
            <div class="auth-footer">
                Already have an account? <a href="login.php">Log in</a>
            </div>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>