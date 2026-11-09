<?php
session_start();
require 'db.php';

// Only logged-in users can use this page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';

// Save the changes when the form is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = (int)($_POST['task_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';

    if ($title === '') {
        $error = 'Please enter a task title.';
    } elseif (!in_array($priority, ['High', 'Medium', 'Low'], true)) {
        $error = 'Invalid priority.';
    } else {
        // Update the task, but ONLY if it belongs to this user
        $stmt = $pdo->prepare(
            'UPDATE tasks SET title = ?, due_date = ?, priority = ? WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$title, $due_date !== '' ? $due_date : null, $priority, $task_id, $user_id]);
        header('Location: index.php');
        exit;
    }
} else {
    $task_id = (int)($_GET['id'] ?? 0);
}

// Load the task (only if it belongs to this user)
$stmt = $pdo->prepare('SELECT id, title, due_date, priority FROM tasks WHERE id = ? AND user_id = ?');
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Task - Task Manager</title>
</head>
<body>
    <h1>Edit Task</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
        <label>Title:</label><br>
        <input type="text" name="title" maxlength="100" required
               value="<?= htmlspecialchars($task['title']) ?>"><br><br>
        <label>Due date:</label><br>
        <input type="date" name="due_date"
               value="<?= htmlspecialchars($task['due_date'] ?? '') ?>"><br><br>
        <label>Priority:</label><br>
        <select name="priority">
            <?php foreach (['High', 'Medium', 'Low'] as $p): ?>
                <option <?= $task['priority'] === $p ? 'selected' : '' ?>><?= $p ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <button type="submit">Save changes</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>