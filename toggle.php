<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$task_id = (int)($_POST['task_id'] ?? 0);

$stmt = $pdo->prepare('UPDATE tasks SET is_done = 1 - is_done WHERE id = ? AND workspace_id = ?');
$stmt->execute([$task_id, $_SESSION['workspace_id']]);

header('Location: index.php');
exit;