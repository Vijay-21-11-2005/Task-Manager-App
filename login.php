<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $team_code = trim($_POST['team_code'] ?? '');

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $workspace_id = null;
        if ($team_code === '') {
            $stmt = $pdo->prepare('SELECT workspace_id FROM workspace_members WHERE user_id = ? AND role = "admin" LIMIT 1');
            $stmt->execute([$user['id']]);
            $member = $stmt->fetch();
            $workspace_id = $member ? $member['workspace_id'] : null;
        } else {
            $stmt = $pdo->prepare('SELECT id FROM workspaces WHERE id = ?');
            $stmt->execute([$team_code]);
            $workspace = $stmt->fetch();
            if ($workspace) {
                $workspace_id = $workspace['id'];
                $stmt = $pdo->prepare('INSERT IGNORE INTO workspace_members (user_id, workspace_id, role) VALUES (?, ?, "member")');
                $stmt->execute([$user['id'], $workspace_id]);
            } else {
                $error = 'Invalid Team Code.';
            }
        }

        if ($workspace_id) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['workspace_id'] = $workspace_id; 
            header('Location: index.php');
            exit;
        }
    } else {
        $error = 'Wrong username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TaskMaster</title>
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
                <h1>Welcome Back</h1>
                <p>Log in to your Solo or Team space</p>
            </div>
            <?php if ($error): ?>
                <p style="color:#e11d48; font-size: 0.85rem; margin-bottom: 15px; text-align: center;"><?= htmlspecialchars($error) ?></p>
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
                <div class="team-code-wrapper">
                    <label><i class="fa-solid fa-users"></i> Team Code (Optional)</label>
                    <p>Leave blank for Solo login, or enter a code to join a Team.</p>
                    <input type="number" name="team_code" placeholder="Enter Team Code" style="margin-bottom: 0;">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">Log In</button>
            </form>
            <div class="auth-footer">
                Don't have an account? <a href="register.php">Sign up</a>
            </div>
        </div>
    </div>
    <script src="theme.js"></script>
</body>
</html>