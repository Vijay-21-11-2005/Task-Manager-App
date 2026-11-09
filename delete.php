<?php
session_start();
require 'db.php';

// Only logged-in users, and only via a form (POST)
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$task_id = (int)($_POST['task_id'] ?? 0);

// Delete the task, but ONLY if it belongs to this user
$stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
$stmt->execute([$task_id, $_SESSION['user_id']]);

header('Location: index.php');
exit;