<?php
session_start();

// If the user is not logged in, send them to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Tasks - Task Manager</title>
</head>
<body>
    <h1>My Tasks</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
    <p><a href="logout.php">Log out</a></p>
</body>
</html>