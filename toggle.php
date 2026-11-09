<?php
session_start();
require 'db.php';

// Only logged-in users, and only via a form (POST)
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$task_id = (int)($_POST['task_id'] ?? 0);

// Flip done/not done, but ONLY if the task belongs to this user
$stmt = $pdo->prepare(
    'UPDATE tasks SET is_done = 1 - is_done WHERE id = ? AND user_id = ?'
);
$stmt->execute([$task_id, $_SESSION['user_id']]);

header('Location: index.php');
exit;